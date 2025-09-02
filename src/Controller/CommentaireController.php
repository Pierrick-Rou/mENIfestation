<?php

namespace App\Controller;

use App\Entity\Sortie;
use App\Repository\CommentaireRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class CommentaireController extends AbstractController
{
    #[Route('/commentaire/{id}', name: 'app_commentaire')]
    public function commentBySortie(int $id, CommentaireRepository $cr): Response
    {
        $commentaires = $cr->findBySortie($id);
        return $this->render('commentaire/index.html.twig', [
            'commentaires' => $commentaires
        ]);
    }
}
