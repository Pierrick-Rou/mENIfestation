<?php

namespace App\Controller;

use App\Repository\LieuRepository;
use App\Repository\ParticipantRepository;
use App\Repository\SiteRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/admin', name: 'app_admin')]
final class AdminController extends AbstractController
{

    #[Route('/', name: '_index')]
    public function index(): Response{
        return $this->render('admin/index.html.twig', []);
    }

    #[Route('/utilisateurs', name: '_users')]
    public function users(ParticipantRepository $pr, SiteRepository $sr, LieuRepository $lr,): Response
    {
        $listeParticipants=$pr->findAll();
        return $this->render('admin/users.html.twig',['listeParticipants'=>$listeParticipants]);

    }

    #[Route('/sites', name: '_sites')]
    public function sites(SiteRepository $sr): Response
    {
        $listeSites=$sr->findAll();
        return $this->render('admin/sites.html.twig',[
            'listeSites'=>$listeSites]);

    }

    #[Route('/lieux', name: '_lieux')]
    public function participants(LieuRepository $lr): Response
    {
        $listeLieux=$lr->findAll();
        return $this->render('admin/lieux.html.twig',[
            'listeLieux'=>$listeLieux]);

    }


}
