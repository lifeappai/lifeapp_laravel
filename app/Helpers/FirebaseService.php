<?php

namespace App\Helpers;

use Google\Client;
use GuzzleHttp\Client as GuzzleClient;

class FirebaseService
{
    protected $projectId;
    protected $httpClient;
    protected $accessToken;

    public function __construct()
    {
        $this->projectId = env('FCM_PROJECT_ID');

        $client = new Client();
        $client->setAuthConfig(env('GOOGLE_APPLICATION_CREDENTIALS'));
        $client->addScope('https://www.googleapis.com/auth/firebase.messaging');

        $this->accessToken = $client->fetchAccessTokenWithAssertion()['access_token'];

        $this->httpClient = new GuzzleClient([
            'base_uri' => "https://fcm.googleapis.com/",
            'headers'  => [
                'Authorization' => 'Bearer ' . $this->accessToken,
                'Content-Type'  => 'application/json',
            ]
        ]);
    }

    public function sendNotification($deviceToken, $title, $body, $data = [])
    {
        // 🔑 Ensure all values are strings
        $stringifiedData = [];
        foreach ($data as $key => $value) {
            $stringifiedData[(string)$key] = is_array($value) ? json_encode($value) : (string)$value;
        }

        $payload = [
            "message" => [
                "token" => $deviceToken,
                "notification" => [
                    "title" => $title,
                    "body"  => $body,
                ],
                "data" => $stringifiedData, 
            ],
        ];

        $url = "v1/projects/{$this->projectId}/messages:send";

        $response = $this->httpClient->post($url, [
            'json' => $payload
        ]);

        return $response->getBody()->getContents();
    }

}
