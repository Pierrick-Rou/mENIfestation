<?php

namespace App\Controller;

use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

use App\Entity\Participant;
use Symfony\Component\Routing\Attribute\Route;


#[Route('/profil', name : 'app_profil')]
final class ProfilController extends AbstractController
{
    #[Route('/details/{id}', name: '_details', requirements: ['id' => '\d+'])]
    public function index(Participant $participant): Response
    {

        return $this->render('profil/detailsProfil.html.twig', [
            'participant' => $participant,
        ]);
    }

    #[Route('/delete/{id}', name: '_delete', requirements: ['id' => '\d+'])]

    public function delete(Participant $participant, EntityManagerInterface $em, Request $request): Response
    {
        $this->isCsrfTokenValid('delete'.$participant->getId(), $request->get('token'));
        $em->remove($participant);
        $em->flush();

        $this->addFlash('success','le profil à été supprimé ');
        return $this->redirectToRoute('app_register');
    }

}
