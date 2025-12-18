<?php

namespace App\Http\Controllers;

use App\Models\ChatMessage;
use App\Services\ChatbotFallbackService;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Http;

class ChatbotController extends Controller
{
    public function fetchMessages(Request $request)
    {
        $userId = Auth::id();
        $token = $request->cookie('chat_token');

        $msgs = ChatMessage::where(function ($q) use ($userId, $token) {
            if ($userId) {
                $q->where('user_id', $userId);
            } elseif ($token) {
                $q->where('guest_token', $token);
            } else {
                $q->whereRaw('1=0');
            }
        })->orderBy('created_at', 'asc')->get();

        return response()->json($msgs);
    }

    public function sendMessage(Request $request)
{
    $request->validate(['message' => 'required|string|max:2000']);

    $userId = Auth::id();
    $guestToken = $request->cookie('chat_token');
    
    // Nếu chưa đăng nhập và chưa có guest token, tạo mới
    if (!$userId && !$guestToken) {
        $guestToken = 'guest_' . Str::random(32);
    }

    // 1. Lưu tin nhắn người dùng vào DB
    $userMsg = ChatMessage::create([
        'user_id' => $userId,
        'guest_token' => $userId ? null : $guestToken,
        'sender' => 'user',
        'message' => $request->message,
    ]);

    // 2. Chuẩn bị Dữ liệu sản phẩm & System Instruction
    $productsData = DB::table('products')
    ->join('product_variants', 'products.id', '=', 'product_variants.product_id')
    ->join('variants_scents', 'product_variants.scent_id', '=', 'variants_scents.id')
    // Loại bỏ products.brand nếu nó gây lỗi, hoặc đổi thành tên cột đúng trong DB của bạn
    ->select('products.id', 'products.name', 'products.price', 'variants_scents.name as scent_name')
    ->distinct()
    ->limit(5) 
    ->get()
    ->map(function($p) {
        return "- {$p->name} (Mùi: {$p->scent_name}). Giá: " . number_format($p->price, 0, ',', '.') . "đ. Link: /products/{$p->id}";
    })
    ->implode("\n");

    $systemInstruction = "Bạn là trợ lý ảo chuyên nghiệp của shop 46 Perfume.\n";
    $systemInstruction .= "Nhiệm vụ: Tư vấn nhiệt tình, ngắn gọn.\n";
    $systemInstruction .= "Dữ liệu sản phẩm shop đang có:\n" . $productsData . "\n\n";
    $systemInstruction .= "Yêu cầu: Nếu nhắc đến sản phẩm, BẮT BUỘC phải ghi link dạng /products/[id].";

    // 3. Lấy lịch sử chat (Để AI hiểu ngữ cảnh cuộc hội thoại)
    $history = ChatMessage::where(function ($q) use ($userId, $guestToken) {
            if ($userId) $q->where('user_id', $userId);
            else $q->where('guest_token', $guestToken);
        })
        ->latest()
        ->limit(5)
        ->get()
        ->reverse();

    // 4. Chuẩn bị contents cho API
    $contents = [];
    
    if ($history->isEmpty()) {
        $contents[] = [
            "role" => "user",
            "parts" => [["text" => $systemInstruction]]
        ];
    }
    
    // Thêm lịch sử chat trước đó (nếu có)
    foreach ($history as $msg) {
        $contents[] = [
            "role" => $msg->sender === 'user' ? "user" : "model",
            "parts" => [["text" => $msg->message]]
        ];
    }
    
    // Thêm message hiện tại của user
    $contents[] = [
        "role" => "user",
        "parts" => [["text" => $request->message]]
    ];

    // 4. Gọi API Gemini
    $aiReplyText = "Hệ thống đang bận, vui lòng thử lại sau!";
    
    $apiKey = env('GOOGLE_GEMINI_API_KEY'); 
    
    $modelsToTry = [
        env('GEMINI_MODEL', 'gemini-2.0-flash-exp'), // Model từ env hoặc mặc định
        'gemini-2.0-flash-exp', // Model mới nhất (từ log thấy đã từng hoạt động)
        'gemini-2.0-flash', // Model từ log cũ
        'gemini-1.5-pro-latest',
        'gemini-1.5-flash-latest',
        'gemini-1.5-pro',
        'gemini-1.5-flash',
        'gemini-pro',
    ];
    
    // Thử cả v1 và v1beta
    $apiVersionsToTry = [
        env('GEMINI_API_VERSION', 'v1beta'),
        'v1beta',
        'v1',
    ];
    
    // Request payload chuẩn bị sẵn
    $requestPayload = [
        "contents" => $contents,
        "generationConfig" => [
            "temperature" => 1,
            "topP" => 0.95,
            "maxOutputTokens" => 8192,
        ]
    ];

    if (empty($apiKey)) {
        Log::error("Gemini API Key is missing in .env file. Please add GOOGLE_GEMINI_API_KEY to .env");
        $aiReplyText = "Hệ thống AI chưa được cấu hình. Vui lòng liên hệ admin.";
    } else {
        $response = null;
        $lastError = null;
        $triedModel = null;
        $triedApiVersion = null;
        $foundWorkingConfig = false;
        
        // Thử từng API version và model cho đến khi tìm được config hoạt động
        foreach ($apiVersionsToTry as $apiVersion) {
            if ($foundWorkingConfig) break;
            
            foreach ($modelsToTry as $modelName) {
                $triedModel = $modelName;
                $triedApiVersion = $apiVersion;
                $apiUrl = "https://generativelanguage.googleapis.com/{$apiVersion}/models/{$modelName}:generateContent?key={$apiKey}";
                
                try {
                    Log::debug("Gemini API Request", [
                        'url' => str_replace($apiKey, '***', $apiUrl),
                        'model' => $modelName,
                        'api_version' => $apiVersion,
                        'contents_count' => count($contents)
                    ]);

                    $response = Http::timeout(30)
                        ->withHeaders(['Content-Type' => 'application/json'])
                        ->post($apiUrl, $requestPayload);
                    
                    // Nếu thành công (status 200), dừng lại
                    if ($response->successful()) {
                        $foundWorkingConfig = true;
                        Log::info("Gemini API: Found working configuration", [
                            'model' => $modelName,
                            'api_version' => $apiVersion
                        ]);
                        break 2; // Break cả 2 loops
                    }
                    
                    // Nếu lỗi 404 (model không tìm thấy), thử model/version tiếp theo
                    if ($response->status() === 404) {
                        $lastError = "Model {$modelName} not found in {$apiVersion}";
                        Log::warning("Gemini API: Model not found, trying next", [
                            'model' => $modelName,
                            'api_version' => $apiVersion
                        ]);
                        continue; // Thử model tiếp theo
                    }
                    
                    if ($response->status() === 429) {
                        $errorBody = $response->json();
                        $lastError = $errorBody['error']['message'] ?? "Quota exceeded for model {$modelName}";
                        Log::warning("Gemini API: Quota exceeded", [
                            'model' => $modelName,
                            'api_version' => $apiVersion,
                            'error' => $lastError
                        ]);
                        break 2;
                    }
                    
                    break 2;
                    
                } catch (\Illuminate\Http\Client\ConnectionException $e) {
                    $lastError = "Connection error with model {$modelName} in {$apiVersion}: " . $e->getMessage();
                    Log::warning("Gemini API Connection Exception, trying next", [
                        'model' => $modelName,
                        'api_version' => $apiVersion,
                        'error' => $e->getMessage()
                    ]);
                    continue; // Thử model tiếp theo
                }
            }
        }
        
        try {
            $useFallback = false;
            
            if ($response && $response->successful()) {
                $data = $response->json();
                
                if (isset($data['candidates'][0]['content']['parts'][0]['text'])) {
                    $aiReplyText = trim($data['candidates'][0]['content']['parts'][0]['text']);
                    if (empty($aiReplyText) || mb_strlen($aiReplyText, 'UTF-8') < 3) {
                        $useFallback = true;
                        Log::warning("Gemini API returned empty response");
                    }
                } elseif (isset($data['candidates'][0]['content']['parts'][0])) {
                    $part = $data['candidates'][0]['content']['parts'][0];
                    $aiReplyText = is_string($part) ? trim($part) : trim($part['text'] ?? '');
                    if (empty($aiReplyText) || mb_strlen($aiReplyText, 'UTF-8') < 3) {
                        $useFallback = true;
                        Log::warning("Gemini API returned empty/invalid response");
                    }
                } else {
                    Log::warning("Gemini API response structure unexpected", ['response' => $data]);
                    $useFallback = true;
                }
            } else {
                $useFallback = true;
            }
            
            if ($useFallback) {
                Log::info("Using rule-based fallback for question", ['question' => $request->message]);
                $fallbackService = new ChatbotFallbackService();
                $fallbackResponse = $fallbackService->processQuestion($request->message);
                
                if (!$response || !$response->successful()) {
                    $statusCode = $response ? $response->status() : 0;
                    $errorBody = $response ? $response->json() : null;
                    $errorMessage = isset($errorBody['error']['message']) ? $errorBody['error']['message'] : ($lastError ?? "HTTP {$statusCode}: Unknown Error");
                    
                    Log::warning("Gemini API Error - Using fallback", [
                        'status' => $statusCode,
                        'error' => $errorMessage,
                        'last_tried_model' => $triedModel,
                        'last_tried_api_version' => $triedApiVersion,
                    ]);
                    
                    // Với lỗi 429, thêm thông báo nhưng vẫn dùng fallback
                    if ($statusCode === 429) {
                        $aiReplyText = $fallbackResponse ;
                    } else {
                        // Với các lỗi khác, chỉ dùng fallback
                        $aiReplyText = $fallbackResponse;
                    }
                } else {
                    // Response rỗng từ AI thành công
                    $aiReplyText = $fallbackResponse;
                }
            }
            
            // Nếu vẫn không có response (trường hợp hiếm), dùng fallback một lần nữa
            if (empty($aiReplyText) || mb_strlen(trim($aiReplyText), 'UTF-8') < 3) {
                $fallbackService = new ChatbotFallbackService();
                $aiReplyText = $fallbackService->processQuestion($request->message);
            }
        } catch (\Exception $e) {
            Log::error('Gemini Exception: ' . $e->getMessage(), [
                'trace' => $e->getTraceAsString()
            ]);
            $aiReplyText = "Có lỗi xảy ra khi xử lý yêu cầu của bạn. Vui lòng thử lại sau.";
        }
    }

    // 5. Lưu tin nhắn Bot vào DB
    $botMsg = ChatMessage::create([
        'user_id' => $userId,
        'guest_token' => $userId ? null : $guestToken,
        'sender' => 'bot',
        'message' => $aiReplyText,
    ]);

    // 6. Nếu có guest token mới, trả về cookie trong response
    $response = response()->json(['user' => $userMsg, 'bot' => $botMsg]);
    
    if (!$userId && $guestToken && !$request->cookie('chat_token')) {
        $response->cookie('chat_token', $guestToken, 60 * 24 * 180); // 180 ngày
    }
    
    return $response;
}
}