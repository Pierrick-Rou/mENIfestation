<?php

namespace App\Controller;

use App\Entity\Participant;
use App\Form\RegistrationType;
use App\Form\UserFileType;
use App\Repository\LieuRepository;
use App\Repository\ParticipantRepository;
use App\Repository\SiteRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/admin', name: 'app_admin')]
final class AdminController extends AbstractController
{

    #[Route('/', name: '_index')]
    public function index(): Response{
        return $this->render('admin/creer-ville.html.twig', []);
    }

    #[Route('/utilisateurs', name: '_users')]
    public function users(Request $request,ParticipantRepository $pr, SiteRepository $sr, LieuRepository $lr,): Response
    {
        $listeParticipants=$pr->findAll();

        return $this->render('admin/users.html.twig',['listeParticipants'=>$listeParticipants]);

    }

    #[Route('/utilisateurs/fichier', name: '_users_file')]
    public function usersfile(Request $request,UserPasswordHasherInterface $userPasswordHasher,ParticipantRepository $pr,EntityManagerInterface $em,SiteRepository $sr){

        $form = $this->createFormBuilder()
            ->add('file', FileType::class, [])
            ->add('submit', SubmitType::class)
            ->getForm();
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $userfile=$form->get('file')->getData();
//            dd($userfile);
            $open = fopen($userfile, "r");
            $header=fgetcsv($open,1000,',' );
            //on retire les charactères BOM liés à la lecture du fichier
            $header[0] = str_replace("\xEF\xBB\xBF", '', $header[0]);
//            dd($header);
            while(($row=fgetcsv($open,1000,',' )) !== false){
                $data = array_combine($header, $row);

                $participant=new Participant();
                $participant->setNom($data['nom']);
                $participant->setPrenom($data['prenom']);
                $participant->setEmail($data['email']);
                $participant->setSite($sr->find($data['site_id']));
                $participant->setPassword($userPasswordHasher->hashPassword($participant, $data['password']));
                $participant->setTelephone($data['telephone']);
                $em->persist($participant);
                $em->flush();
            }


            return $this->redirectToRoute('app_admin_users');
        }

        return $this->render('admin/fileToUsers.html.twig',['form'=>$form->createView()]);
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
