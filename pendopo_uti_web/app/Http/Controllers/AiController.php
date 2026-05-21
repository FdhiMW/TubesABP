<?php

namespace App\Http\Controllers;

use App\Models\Venue;
use App\Services\AiContextService;
use App\Services\GeminiService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class AiController extends Controller
{
    public function chat(Request $request, AiContextService $contextService, GeminiService $gemini)
    {
        Log::info('AI chat endpoint hit', $request->all());

        $validated = $request->validate([
            'question' => ['required', 'string', 'max:2000'],
        ]);

        $question = $validated['question'];
        $questionLower = strtolower($question);

        try {
            // 1) Kalau user minta daftar venue, jawab langsung dari DB
            if (
                str_contains($questionLower, 'venue apa saja') ||
                str_contains($questionLower, 'daftar venue') ||
                str_contains($questionLower, 'venue apa')
            ) {
                $venues = $contextService->getAllActiveVenues();

                if (empty($venues)) {
                    return response()->json([
                        'success' => true,
                        'answer' => 'Saat ini belum ada venue yang aktif di database.',
                    ]);
                }

                $answer = collect($venues)->map(function ($v) {
                    $price = is_numeric($v['price_per_day'] ?? null)
                        ? 'Rp ' . number_format((float) $v['price_per_day'], 0, ',', '.')
                        : ($v['price_per_day'] ?? '-');

                    return "- {$v['name']} | Kapasitas: {$v['capacity']} | Lokasi: {$v['location']} | Harga: {$price}";
                })->implode("\n");

                return response()->json([
                    'success' => true,
                    'answer' => "Berikut venue yang tersedia berdasarkan database:\n\n" . $answer,
                ]);
            }

            // 2) Coba cari venue yang relevan dari database
            $venue = $contextService->findVenueByQuestion($question);

            if ($venue) {
                $venueData = $contextService->venueToArray($venue);

                $prompt = "
DATA VENUE ASLI DARI DATABASE:
" . json_encode($venueData, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT) . "

Aturan:
- Hanya jawab berdasarkan data di atas.
- Jangan membuat nama venue baru.
- Jangan menambah harga, fasilitas, kapasitas, atau lokasi yang tidak ada di data.
- Jika ada data yang tidak tersedia, bilang 'data belum tersedia'.
- Gunakan Bahasa Indonesia yang singkat, jelas, dan sopan.

Pertanyaan user:
{$question}
";

                $answer = $gemini->generateText(
                    $prompt,
                    'Kamu adalah asisten booking wedding venue. Jawab hanya berdasarkan data yang diberikan.'
                );

                return response()->json([
                    'success' => true,
                    'answer' => $answer,
                    'context' => [
                        'venue' => $venueData,
                    ],
                ]);
            }

            // 3) Kalau tidak ketemu venue yang cocok, jangan ngarang
            return response()->json([
                'success' => true,
                'answer' => 'Saya belum menemukan venue yang cocok di database. Coba sebutkan nama venue atau kata kunci yang lebih spesifik.',
            ]);
        } catch (\Throwable $e) {
            Log::error('AI chat error: ' . $e->getMessage());

            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 500);
        }
    }
}