<?php

namespace App\Controller;


use App\Entity\Sortie;
use App\Form\SortieType;
use App\Repository\ParticipantRepository;
use App\Repository\SiteRepository;
use App\Repository\EtatRepository;
use Doctrine\ORM\EntityManagerInterface;

use App\Repository\SortieRepository;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/sortie', name: 'app_sortie_')]
final class SortieController extends AbstractController
{

    #[Route('', name: 'home')]
    public function index(Request $request, SortieRepository $sortieRepository, SiteRepository $sR): Response
    {
        $siteId = $request->query->get('site');

        $sites = $sR->findAll();

        if ($siteId) {
            $sortieList = $sortieRepository->findBy(['site' => $siteId]);
        } else {
            $sortieList = $sortieRepository->findAll();
        }

        return $this->render('sortie/index.html.twig', [
            'sortieList' => $sortieList,
            'sites' => $sites,
            'siteId' => $siteId,
        ]);
    }

    #[Route('/{id}', name: 'id', requirements: ['id' => '\d+'], methods: ['GET'])]
    public function detail(int $id, SortieRepository $sortieRepository, Sortie $sortieEntity): Response
    {
        $user = $this->getUser();
        $sortie = $sortieRepository->find($id);

        if (!$sortie) {
            $this->addFlash('error', 'Sortie not found');
        }

        $isRegistered = false;
        if ($user && $sortie) {
            $isRegistered = $sortieEntity->getParticipant()->contains($user);
        }

//        $now = new \DateTime('now');
//        switch ($now){
//            case $now->format('Y-m-d') === $sortie->getDateHeureDebut()->format('Y-m-d'):
//
//        }




        return $this->render('sortie/sortiePage.html.twig', [
            'sortie' => $sortie,
            'isRegistered' => $isRegistered
        ]);
    }

    #[Route('/{id}/inscription', name: 'inscription', requirements: ['id' => '\d+'], methods: ['GET'])]
    public function inscription(int $id,
                                SortieRepository $sortieRepository,
                                ParticipantRepository $participantRepository,
                                Sortie $sortieEntity,
                                EntityManagerInterface $em): Response
    {

        $user = $this->getUser();
        $sortie = $sortieRepository->find($id);

        if (!$sortie) {
            $this->addFlash('error', 'Sortie not found');
        }

        $isRegistered = false;
        if ($user && $sortie) {
            $isRegistered = $sortieEntity->getParticipant()->contains($user);
        }

        $participant = $participantRepository->find($user->getId());
        if (!$isRegistered) {
            $sortie->addParticipant($participant);


            $em->persist($sortie);
            $em->flush();
            $this->addFlash('success', 'Vous êtes inscrit à l\'évènement');
        } else if ($isRegistered) {
            $sortie->removeParticipant($participant);

            $em->persist($sortie);
            $em->flush();

            $this->addFlash('success', 'Vous vous êtes désinscrit de l\'évènement');
        }



        return $this->redirectToRoute('app_sortie_id', ['id' => $id]);
    }


    #[Route('/create', name: 'create', methods: ['GET','POST'])]
    public function create(EtatRepository $er): Response
    {
        $sortie = new Sortie();
        $sortie->setEtat($er->find(1));
        $sortieForm = $this->createForm(SortieType::class, $sortie);
        return $this->render("sortie/sortieForm.html.twig", [
            "sortieForm" => $sortieForm
        ]);
    }
    #[Route('/validForm', name: 'validForm', methods: ['POST'])]
    public function validForm(Request $request, EntityManagerInterface $entityManager): Response
    {
        $sortie = new Sortie();

        // 2. Crée le formulaire
        $form = $this->createForm(SortieType::class, $sortie);

        // 3. Gère la soumission du formulaire
        $form->handleRequest($request);

        // 4. Si le formulaire est soumis et valide
        if ($form->isSubmitted() && $form->isValid()) {
            // 5. Enregistre en base de données
            $user = $security->getUser();

            $userSite = $user->getSite();
            $sortie->setOrganisateur($user);
            $sortie->setSite($userSite);
            $etat = $eR->find(1);
            $sortie->setEtat($etat);


            $entityManager->persist($sortie);
            $entityManager->flush();

            // 6. Redirige vers une autre page (ex: liste des sorties)
            return $this->redirectToRoute('app_sortie_home');
        }

        // 7. Affiche le formulaire
        return $this->render('sortie/new.html.twig', [
            'form' => $form->createView(),
        ]);
    }

}
