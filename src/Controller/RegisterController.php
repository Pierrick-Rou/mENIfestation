<?php

namespace App\Controller;

use App\Entity\Participant;
use App\Entity\Site;
use App\Form\RegistrationType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\String\Slugger\SluggerInterface;

final class RegisterController extends AbstractController
{
    #[Route('/register', name: 'app_register')]
    public function register(Request $request, UserPasswordHasherInterface $userPasswordHasher, Security $security, EntityManagerInterface $em,SluggerInterface $slugger): Response
    {

        $user = new Participant();


        $form = $this->createForm(RegistrationType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $plainPassword = $form->get('password')->getData();
            $user->setPassword($userPasswordHasher->hashPassword($user, $plainPassword));


            $file = $form->get('poster_file')->getData();

            if ($file instanceof UploadedFile) {
                    $name = $slugger->slug($user->getNom()).'-'. uniqid().'. '.$file->guessExtension();
                    $file->move('\public\uploads', $name);
                    $user->setImageProfil($name);

            }
            $em->persist($user);
            $em->flush();

            return $security->login($user, 'form_login', 'main');
        }

        return $this->render('registration/register.html.twig', [
            'registrationType' => $form,
        ]);
    }
}
