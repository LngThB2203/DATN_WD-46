<?php
namespace App\Http\Controllers;

use App\Models\ChatMessage;
use App\Models\Product;
use App\Models\ProductVariant;
use App\Models\WarehouseProduct;
use App\Services\AiChatService;
use Illuminate\Http\Request;

class ChatbotController extends Controller
{
    protected $ai;

    public function __construct(AiChatService $ai)
    {
        $this->ai = $ai;
    }

    public function handle(Request $request)
    {
        $msg   = trim($request->message);
        $lower = mb_strtolower($msg, 'UTF-8');

        $this->saveLog('user', $msg);

        $response = [
            "reply" => "",
            "cards" => [],
        ];

        // 1) Tìm theo brand
        if (preg_match('/(chanel|dior|lancome|dg|d&g)/i', $lower, $m)) {
            $brand = $m[1];

            $products = Product::where('brand', 'like', "%{$brand}%")->get();

            if ($products->count()) {
                $response['reply'] = "Dưới đây là các sản phẩm của {$brand}:";

                $response['cards'] = $products->map(function ($p) {
                    return [
                        "id"    => $p->id,
                        "name"  => $p->name,
                        "price" => $p->sale_price ?: $p->price,
                        "image" => asset($p->image ?? 'no-image.jpg'),
                    ];
                });

                $this->saveLog('bot', $response['reply'], $response['cards']);

                return response()->json($response);
            }
        }

        // 2) Tìm theo giới tính
        if (str_contains($lower, 'nam') || str_contains($lower, 'nữ') || str_contains($lower, 'unisex')) {
            $gender = str_contains($lower, 'nam') ? 'male' :
            (str_contains($lower, 'nữ') ? 'female' : 'unisex');

            $variants = ProductVariant::where('gender', $gender)->get();

            $response['reply'] = "Dưới đây là một số mùi phù hợp cho {$gender}:";
            $response['cards'] = $variants->map(function ($v) {
                return [
                    "id"    => $v->product_id,
                    "name"  => $v->scent,
                    "price" => $v->price_adjustment ?: 0,
                    "image" => 'https://via.placeholder.com/200',
                ];
            });

            $this->saveLog('bot', $response['reply'], $response['cards']);

            return response()->json($response);
        }

        // 3) Tồn kho
        if (str_contains($lower, 'tồn kho') || str_contains($lower, 'còn hàng')) {
            if (preg_match('/\d+/', $lower, $m)) {
                $pid = $m[0];

                $stock = WarehouseProduct::where('product_id', $pid)->sum('quantity');

                $reply = "Sản phẩm ID {$pid} hiện còn {$stock} cái.";

                $this->saveLog('bot', $reply);

                return response()->json([
                    "reply" => $reply,
                    "cards" => [],
                ]);
            }
        }

        // 4) Giá sản phẩm
        if (preg_match('/giá.*?(\d+)/', $lower, $m)) {
            $price = $m[1];

            $products = Product::where('price', '<=', $price)->get();

            $response['reply'] = "Các sản phẩm dưới {$price} VND:";

            $response['cards'] = $products->map(function ($p) {
                return [
                    "id"    => $p->id,
                    "name"  => $p->name,
                    "price" => $p->sale_price ?: $p->price,
                    "image" => asset($p->image ?? 'no-image.jpg'),
                ];
            });

            $this->saveLog('bot', $response['reply'], $response['cards']);

            return response()->json($response);
        }

        // 5) fallback AI
        $final = $this->ai->ask($msg);

        $this->saveLog('bot', $final);

        return response()->json([
            "reply" => $final,
            "cards" => [],
        ]);
    }

    protected function saveLog($sender, $message, $cards = null)
    {
        ChatMessage::create([
            'user_id' => auth()->id(),
            'sender'  => $sender,
            'message' => $message,
            'payload' => $cards ? json_encode($cards) : null,
        ]);
    }
}
