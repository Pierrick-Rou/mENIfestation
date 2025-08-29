<?php

namespace App\Controller;

use App\Service\ChatBotService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class ChatBotController extends AbstractController
{
    #[Route('/chatbot', name: 'chatbot', methods: ['POST'])]
    public function chat(Request $request, ChatBotService $chatbot): JsonResponse
    {
        $data = json_decode($request->getContent(), true);
        $message = $data['message'] ?? '';

        $reply = $chatbot->ask($message);

        return new JsonResponse(['reply' => $reply]);

    }
}
