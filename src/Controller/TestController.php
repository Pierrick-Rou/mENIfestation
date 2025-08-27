<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

final class TestController extends AbstractController
{
    #[Route('/', name: 'app_test')]
    public function index(): Response
    {
        return $this->render('test/index.html.twig', [
            'controller_name' => 'TestController',
        ]);
    }

    #[Route('/test-admin', name: 'app_test_admin')]
    #[ISGranted('ROLE_ADMIN')]
    public function indexAdmin(): Response
    {
        return $this->render('test/index_admin.html.twig', [
            'controller_name' => 'TestController',
        ]);
    }



    #[Route('/profil', name: 'app_profil')]
    public function profil(): Response
    {
        return $this->render('test/index_profil.html.twig', [
            'controller_name' => 'TestController',
        ]);
    }


}
