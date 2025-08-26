<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;

use App\Entity\Participant;
use Symfony\Component\Routing\Attribute\Route;


#[Route('/profil', name : 'profil')]
final class ProfilController extends AbstractController
{
    #[Route('/details/{id}', name: 'app_profil', requirements: ['id' => '\d+'])]
    public function index(Participant $participant): Response
    {

        return $this->render('profil/detailsProfil.html.twig', [
            'participant' => $participant,
        ]);
    }
}
