<?php

namespace App\Controller;

use App\Repository\SortieRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class SortieController extends AbstractController
{
    #[Route('/sortie', name: 'app_sortie')]
    public function index(SortieRepository $sortieRepository): Response
    {
        $sortieList = $sortieRepository->findAll();

        return $this->render('sortie/index.html.twig', [
            'sortieList' => $sortieList,
        ]);
    }
}
