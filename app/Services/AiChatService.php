<?php
namespace App\Services;

use Exception;
use OpenAI;

class AiChatService
{
    protected $client;
    protected $model;

    public function __construct()
    {
        $this->model = env('OPENAI_MODEL', 'gpt-4o-mini');

        if (env('OPENAI_API_KEY')) {
            $this->client = OpenAI::client(env('OPENAI_API_KEY'));
        } else {
            $this->client = null;
        }
    }

    public function ask(string $prompt): string
    {
        if (! $this->client) {
            return "Mình chưa được bật chế độ AI. Bạn có thể hỏi về sản phẩm, thương hiệu, mùi hương, giá hoặc tồn kho nhé!";
        }

        try {
            $response = $this->client->chat()->create([
                'model'    => $this->model,
                'messages' => [
                    ['role' => 'system', 'content' => 'Bạn là chatbot tư vấn nước hoa cho shop.'],
                    ['role' => 'user', 'content' => $prompt],
                ],
            ]);

            return trim($response->choices[0]->message->content ?? '');
        } catch (Exception $e) {
            return "Hiện tại hệ thống AI đang gặp sự cố. Bạn vui lòng thử lại sau!";
        }
    }
}
