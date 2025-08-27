<?php

namespace App\Controller;

use App\Repository\LieuRepository;
use App\Repository\ParticipantRepository;
use App\Repository\SiteRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class AdminController extends AbstractController
{
    #[Route('/admin', name: 'app_admin')]
    public function index(ParticipantRepository $pr, SiteRepository $sr, LieuRepository $lr,): Response
    {
        $listeParticipants=$pr->findAll();
        return $this->render('admin/index.html.twig',['listeParticipants'=>$listeParticipants]);
    }


}
