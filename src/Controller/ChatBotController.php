<?php

namespace App\Controller;

use App\Repository\SortieRepository;
use App\Service\ChatBotService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class ChatBotController extends AbstractController
{
    #[Route('/chatbot', name: 'chatbot', methods: ['POST'])]
    public function chat(Request $request, ChatBotService $chatbot, SortieRepository $sR): JsonResponse
    {
        global $logger;
        $data = json_decode($request->getContent(), true);
        $message = $data['message'] ?? '';

        $systemPrompt = "Tu es un assistant qui répond aux utilisateurs.
        Tu ne fais pas des réponses de plus de 200 caractères. Tu ne prends pas d'initiatives.
        Tu connais les informations suivantes pour répondre si on t'interroge sur les sorties, actions ou événements:\n";

        $eventData = $this->sortieToText($sR);

        $messagePlusData =   $systemPrompt . $eventData . "\n\nUtilisateur : " . $message;
        $reply = $chatbot->ask($messagePlusData);

        return new JsonResponse(['reply' => $reply]);

    }

    private function sortieToText(SortieRepository $sR): string
    {

        $sorties = $sR->findAll();
        $output = "";

        foreach ($sorties as $sortie) {
            $output .= sprintf(
                "- %s le %s à %s l'état est:%s (participants: %d). Organisé par :%s\n",
                $sortie->getNom(),
                $sortie->getDateHeureDebut()->format('d/m/Y H:i'),
                $sortie->getLieu()->getNom(),
                $sortie->getEtat()->value,
                count($sortie->getParticipant()),
                $sortie->getSite()->getNom()
            );
        }

        return $output;
    }
}
