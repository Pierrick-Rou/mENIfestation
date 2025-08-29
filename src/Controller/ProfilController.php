<?php

namespace App\Controller;

use App\Form\EditProfilType;
use App\Form\RegistrationType;
use App\Repository\ParticipantRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

use App\Entity\Participant;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Symfony\Component\String\Slugger\SluggerInterface;


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


    public function delete(Participant $participant, EntityManagerInterface $em, Request $request, TokenStorageInterface $tokenStorage): Response
    {
        $this->isCsrfTokenValid('delete'.$participant->getId(), $request->get('token'));
        $em->remove($participant);
        $em->flush();

        $this->addFlash('success','le profil à été supprimé ');
        return $this->redirectToRoute('app_home');
    }

    #[Route('/ban/{id}', name: '_ban', requirements: ['id' => '\d+'])]
    #[ISGranted('ROLE_ADMIN')]
    public function ban(EntityManagerInterface $em,ParticipantRepository $pr, Request $request): Response
    {
        $participant=$pr->findOneBy(['id'=>$request->get('id')]);
        $participant->setInactif();
//        dd($participant);
        $em->persist($participant);
        $em->flush();

        $this->addFlash('success','le profil à été banni ');
        return $this->redirectToRoute('app_home');
    }

    #[Route('/update/{id}', name: '_update', requirements: ['id' => '\d+'])]
    public function update(Participant $participant, Request $request, EntityManagerInterface $em, SluggerInterface $slugger): Response
    {

        $form = $this->createForm(EditProfilType::class, $participant);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $file = $form->get('poster_file')->getData();

            if ($file instanceof UploadedFile) {
                $name = $slugger->slug($participant->getNom()).'-'. uniqid().'.'.$file->guessExtension();
                $file->move('D:\mENIfestation\public\uploads', $name);
                $participant->setImageProfil($name);

            }
            $em->persist($participant);
            $em->flush();

            $this->addFlash('succes', 'le profil a été mis à jour');
            return $this->redirectToRoute('app_profil_details', ['id' => $participant->getId()]);
        }

        return $this->render('profil/editProfil.html.twig', [
            'editProfilType' => $form->createView()
        ]);
        }

}
