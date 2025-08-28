<?php

namespace App\Controller;

use App\Entity\Participant;
use App\Form\RegistrationType;
use App\Form\UserFileType;
use App\Repository\LieuRepository;
use App\Repository\ParticipantRepository;
use App\Repository\SiteRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
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
    public function users(Request $request,ParticipantRepository $pr, SiteRepository $sr, LieuRepository $lr,): Response
    {
        $listeParticipants=$pr->findAll();

//      $users[] = new Participant();
        $form=$this->createForm(UserFileType::class);
        $form->handleRequest($request);

        return $this->render('admin/users.html.twig',['listeParticipants'=>$listeParticipants,'userfileType'=>$form]);

    }

    #[Route('/utilisateurs/fichier', name: '_users_file', methods: ['GET'])]
    public function usersfile(Request $request){
        $users[] = new Participant();
        return $this->render('admin/fileToUsers.html.twig');
    }

    #[Route('/utilisateurs/fichier/submit', name: '_users_file_submit', methods: ['POST'])]
    public function usersfilepost(Request $request){
        $users[] = new Participant();
        return $this->render('admin/fileToUsers.html.twig');
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
