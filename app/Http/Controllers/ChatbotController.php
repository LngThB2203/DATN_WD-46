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
    
    if (!$userId && !$guestToken) {
        $guestToken = 'guest_' . Str::random(32);
    }

    $userMsg = ChatMessage::create([
        'user_id' => $userId,
        'guest_token' => $userId ? null : $guestToken,
        'sender' => 'user',
        'message' => $request->message,
    ]);

    $productsData = DB::table('products')
    ->join('product_variants', 'products.id', '=', 'product_variants.product_id')
    ->join('variants_scents', 'product_variants.scent_id', '=', 'variants_scents.id')
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

    $history = ChatMessage::where(function ($q) use ($userId, $guestToken) {
            if ($userId) $q->where('user_id', $userId);
            else $q->where('guest_token', $guestToken);
        })
        ->latest()
        ->limit(5)
        ->get()
        ->reverse();

    $contents = [];
    
    if ($history->isEmpty()) {
        $contents[] = [
            "role" => "user",
            "parts" => [["text" => $systemInstruction]]
        ];
    }
    
    foreach ($history as $msg) {
        $contents[] = [
            "role" => $msg->sender === 'user' ? "user" : "model",
            "parts" => [["text" => $msg->message]]
        ];
    }
    
    $contents[] = [
        "role" => "user",
        "parts" => [["text" => $request->message]]
    ];

    $aiReplyText = "Hệ thống đang bận, vui lòng thử lại sau!";
    
    $apiKey = config('services.gemini.api_key');
    $defaultModel = config('services.gemini.default_model', 'gemini-2.0-flash-exp');
    $defaultApiVersion = config('services.gemini.default_api_version', 'v1beta');
    
    $modelsToTry = [
        env('GEMINI_MODEL', 'gemini-2.0-flash-exp'), 
        'gemini-2.0-flash-exp', 
        'gemini-2.0-flash',
        'gemini-1.5-pro-latest',
        'gemini-1.5-flash-latest',
        'gemini-1.5-pro',
        'gemini-1.5-flash',
        'gemini-pro',
    ];
    
    $apiVersionsToTry = [
        env('GEMINI_API_VERSION', 'v1beta'),
        'v1beta',
        'v1',
    ];
    
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
                    
                    if ($response->successful()) {
                        $foundWorkingConfig = true;
                        Log::info("Gemini API: Found working configuration", [
                            'model' => $modelName,
                            'api_version' => $apiVersion
                        ]);
                        break 2;
                    }
                    
                    if ($response->status() === 404) {
                        $lastError = "Model {$modelName} not found in {$apiVersion}";
                        Log::warning("Gemini API: Model not found, trying next", [
                            'model' => $modelName,
                            'api_version' => $apiVersion
                        ]);
                        continue;
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
                    continue; 
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
                $statusCode = $response ? $response->status() : 0;
                $errorBody = $response ? $response->json() : null;
                $lastError = $errorBody['error']['message'] ?? ($lastError ?? "HTTP {$statusCode}: Unknown Error");

                Log::warning("Gemini API Error - Using fallback", [
                    'status' => $statusCode,
                    'error' => $lastError,
                    'last_tried_model' => $triedModel,
                    'last_tried_api_version' => $triedApiVersion,
                ]);
            }

            // Kiểm tra nếu response rỗng hoặc không hợp lệ
            if (!$useFallback && (empty($aiReplyText) || mb_strlen(trim($aiReplyText), 'UTF-8') < 3)) {
                $useFallback = true;
                Log::warning("Gemini API returned empty/invalid response, using fallback");
            }

            // Sử dụng fallback nếu cần
            if ($useFallback || empty($aiReplyText) || mb_strlen(trim($aiReplyText), 'UTF-8') < 3) {
                $fallbackService = new ChatbotFallbackService();
                $aiReplyText = $fallbackService->processQuestion($request->message);
            }
        } catch (\Exception $e) {
            Log::error('Gemini Exception: ' . $e->getMessage(), [
                'trace' => $e->getTraceAsString()
            ]);
            $fallbackService = new ChatbotFallbackService();
            $aiReplyText = $fallbackService->processQuestion($request->message);
            if (empty($aiReplyText)) {
                $aiReplyText = "Có lỗi xảy ra khi xử lý yêu cầu của bạn. Vui lòng thử lại sau.";
            }
        }
    }

    $botMsg = ChatMessage::create([
        'user_id' => $userId,
        'guest_token' => $userId ? null : $guestToken,
        'sender' => 'bot',
        'message' => $aiReplyText,
    ]);

    $response = response()->json(['user' => $userMsg, 'bot' => $botMsg]);
    
    if (!$userId && $guestToken && !$request->cookie('chat_token')) {
        $response->cookie('chat_token', $guestToken, 60 * 24 * 180); 
    }
    
    return $response;
}
}