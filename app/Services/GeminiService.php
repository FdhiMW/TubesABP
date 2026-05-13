<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use RuntimeException;

class GeminiService
{
    public function generateText(string $prompt, string $systemInstruction = ''): string
    {
        $model = config('services.gemini.model', 'gemini-2.5-flash');
        $apiKey = config('services.gemini.key');

        if (empty($apiKey)) {
            throw new RuntimeException('GOOGLE_GEMINI_API_KEY belum diisi di .env');
        }

        $url = "https://generativelanguage.googleapis.com/v1beta/models/{$model}:generateContent?key={$apiKey}";

        $payload = [
            'contents' => [
                [
                    'role' => 'user',
                    'parts' => [
                        ['text' => $prompt],
                    ],
                ],
            ],
        ];

        if (!empty($systemInstruction)) {
            $payload['systemInstruction'] = [
                'parts' => [
                    ['text' => $systemInstruction],
                ],
            ];
        }

        $response = Http::timeout(60)
            ->retry(2, 1000)
            ->post($url, $payload);

        if ($response->failed()) {
            throw new RuntimeException('Gemini error: ' . $response->body());
        }

        return data_get($response->json(), 'candidates.0.content.parts.0.text', '');
    }
}