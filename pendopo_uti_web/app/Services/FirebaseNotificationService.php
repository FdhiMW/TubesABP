<?php

namespace App\Services;

use Google\Auth\Credentials\ServiceAccountCredentials;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class FirebaseNotificationService
{
    public function send(string $fcmToken, string $title, string $body): void
    {
        $credentialsPath = base_path(env('FIREBASE_CREDENTIALS'));

        $credentials = new ServiceAccountCredentials(
            ['https://www.googleapis.com/auth/firebase.messaging'],
            $credentialsPath
        );

        $tokenData = $credentials->fetchAuthToken();

        $accessToken = $tokenData['access_token'];

        $projectId = env('FIREBASE_PROJECT_ID');

        $response = Http::withToken($accessToken)
            ->post(
                "https://fcm.googleapis.com/v1/projects/{$projectId}/messages:send",
                [
                    'message' => [
                        'token' => $fcmToken,
                        'notification' => [
                            'title' => $title,
                            'body'  => $body,
                        ],
                        'android' => [
                            'priority' => 'high',
                        ],
                    ],
                ]
            );

        Log::info('FCM RESPONSE', [
            'status' => $response->status(),
            'body' => $response->body(),
        ]);
    }
}