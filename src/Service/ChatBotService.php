<?php

namespace App\Service;

use GuzzleHttp\Client;

class ChatBotService
{
    private Client $client;
    private string $apiKey;

    public function __construct(string $mistralApiKey)
    {
        $this->apiKey = $mistralApiKey;
        $this->client = new Client([
            'base_uri' => 'https://api.mistral.ai/v1/',
            'timeout'  => 30.0,
        ]);
    }

    public function ask(string $prompt): string
    {
        $response = $this->client->post('chat/completions', [
            'headers' => [
                'Authorization' => "Bearer {$this->apiKey}",
                'Content-Type'  => 'application/json',
            ],
            'json' => [
                'model' => 'mistral-small', // tu peux changer pour mistral-medium / mistral-large
                'messages' => [
                    ['role' => 'user', 'content' => $prompt],
                ],
            ],
        ]);

        $data = json_decode($response->getBody()->getContents(), true);

        return $data['choices'][0]['message']['content'] ?? '❌ Pas de réponse du bot';
    }
}
