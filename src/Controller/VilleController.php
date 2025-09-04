<?php

namespace App\Controller;

use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use App\DTO\VilleDTO;
use App\Entity\Ville;
use App\Form\VilleType;
use App\Repository\VilleRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/ville', name: 'app_ville_')]
final class VilleController extends AbstractController
{
    #[Route('/creer', name: 'creer')]
    public function index(Request $request, EntityManagerInterface $em, SessionInterface $session): Response
    {
        $ville = new Ville();
        $villeForm = $this->createForm(VilleType::class, $ville);
        $villeForm->handleRequest($request);

        if ($villeForm->isSubmitted() && $villeForm->isValid()) {
            $em->persist($ville);
            $em->flush();

            $this->addFlash('success', 'Ville ajoutée');

            $session->set('reopen_modal', true);
            return $this->redirectToRoute('app_sortie_create');

        }

        return $this->render('ville/creer-ville.html.twig', [
            'villeForm' => $villeForm->createView(),
        ]);
    }
}
