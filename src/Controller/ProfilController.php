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
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Symfony\Component\String\Slugger\SluggerInterface;
use function Webmozart\Assert\Tests\StaticAnalysis\null;


#[Route('/profil', name : 'app_profil')]
final class ProfilController extends AbstractController
{
    #[Route('/details/{id}', name: '_details', requirements: ['id' => '\d+'])]
    #[IsGranted('ROLE_USER')]
    public function index(Participant $participant): Response
    {
        return $this->render('profil/detailsProfil.html.twig', [
            'participant' => $participant,
        ]);
    }

    #[Route('/delete/{id}', name: '_delete_admin', requirements: ['id' => '\d+'])]
    #[IsGranted('ROLE_ADMIN')]
    public function deleteAdmin(Participant $participant, EntityManagerInterface $em, Request $request, TokenStorageInterface $tokenStorage): Response
    {
        $this->isCsrfTokenValid('delete'.$participant->getId(), $request->get('token'));

//        $tokenStorage->setToken(null);
//        $request->getSession()->invalidate();

        $em->remove($participant);
        $em->flush();

        $this->addFlash('success','le profil à été supprimé ');
        return $this->redirectToRoute('app_admin_users');
    }

    #[Route('/delete', name: '_delete')]
    #[IsGranted('ROLE_USER')]
    public function delete(EntityManagerInterface $em, Request $request, TokenStorageInterface $tokenStorage): Response
    {
        $participant = $this->getUser();
        if ($this->isCsrfTokenValid('delete' . $participant->getId(), $request->get('token'))) {
            $em->remove($participant);
            $em->flush();
            $this->addFlash('success', 'le profil à été supprimé ');
            $tokenStorage->setToken(null);
            $request->getSession()->invalidate();
        }

        return $this->redirectToRoute('app_admin_users');
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

        $redirect = $request->query->get('redirect', 'app_admin_users');
        if ($redirect === 'app_profil_details') {
            return $this->redirectToRoute($redirect, ['id' => $participant->getId()]);
        }
        return $this->redirectToRoute($redirect);

    }
    #[Route('/unban/{id}', name: '_unban', requirements: ['id' => '\d+'])]
    #[ISGranted('ROLE_ADMIN')]
    public function unban(EntityManagerInterface $em,ParticipantRepository $pr, Request $request): Response
    {
        $participant=$pr->findOneBy(['id'=>$request->get('id')]);
        $participant->setactif();

        $em->persist($participant);
        $em->flush();

        $this->addFlash('success','le profil à été débanni ');
        return $this->redirectToRoute('app_admin_users');
    }

    #[Route('/update', name: '_update')]
    public function update(Request $request, EntityManagerInterface $em, SluggerInterface $slugger): Response
    {
        $user = $this->getUser();
        /* @var Participant $user */

        if (!$this->isCsrfTokenValid('update' . $user->getId(), $request->get('token'))) {
            throw $this->createAccessDeniedException('Invalid link token.');
        }

        $form = $this->createForm(EditProfilType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $file = $form->get('poster_file')->getData();

            if ($file instanceof UploadedFile) {
                $name = $slugger->slug($user->getNom()) . '-' . uniqid() . '.' . $file->guessExtension();
                $file->move('uploads', $name);
                $user->setImageProfil($name);
            }

            $em->persist($user);
            $em->flush();

            $this->addFlash('success', 'Le profil a été mis à jour');
            return $this->redirectToRoute('app_profil_details', ['id' => $user->getId()]);
        }

        return $this->render('profil/editProfil.html.twig', [
            'editProfilType' => $form->createView()
        ]);
    }

}
