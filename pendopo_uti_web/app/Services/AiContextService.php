<?php

namespace App\Services;

use App\Models\Venue;
use Illuminate\Support\Str;

class AiContextService
{
    public function getAllActiveVenues(): array
    {
        return Venue::query()
            ->where('status', 'active')
            ->get([
                'id',
                'name',
                'description',
                'location',
                'capacity',
                'price_per_day',
                'facilities',
                'photos',
                'status',
            ])
            ->map(function ($venue) {
                return [
                    'id' => $venue->id,
                    'name' => $venue->name,
                    'description' => $venue->description,
                    'location' => $venue->location,
                    'capacity' => $venue->capacity,
                    'price_per_day' => $venue->price_per_day,
                    'facilities' => $venue->facilities,
                    'photos' => $venue->photos,
                    'status' => $venue->status,
                ];
            })
            ->toArray();
    }

    public function findVenueByQuestion(string $question): ?Venue
    {
        $keywords = $this->extractKeywords($question);

        if (empty($keywords)) {
            return null;
        }

        return Venue::query()
            ->where('status', 'active')
            ->where(function ($q) use ($keywords) {
                foreach ($keywords as $keyword) {
                    $q->orWhere('name', 'like', "%{$keyword}%")
                      ->orWhere('description', 'like', "%{$keyword}%")
                      ->orWhere('location', 'like', "%{$keyword}%");
                }
            })
            ->first();
    }

    public function venueToArray(Venue $venue): array
    {
        return [
            'id' => $venue->id,
            'name' => $venue->name,
            'description' => $venue->description,
            'location' => $venue->location,
            'capacity' => $venue->capacity,
            'price_per_day' => $venue->price_per_day,
            'facilities' => $venue->facilities,
            'photos' => $venue->photos,
            'status' => $venue->status,
        ];
    }

    private function extractKeywords(string $text): array
    {
        $text = Str::lower($text);

        $words = preg_split('/\s+/', $text) ?: [];
        $words = array_filter($words, fn ($w) => strlen($w) >= 4);

        return array_values(array_unique(array_slice($words, 0, 6)));
    }
}