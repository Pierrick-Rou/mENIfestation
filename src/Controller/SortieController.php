<?php

namespace App\Controller;


use App\Entity\Sortie;
use App\Form\SortieType;
use App\Repository\SiteRepository;
use App\Repository\EtatRepository;
use Doctrine\ORM\EntityManagerInterface;

use App\Repository\SortieRepository;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/sortie', name: 'app_sortie_')]
final class SortieController extends AbstractController
{

    #[Route('', name: 'home')]
    public function index(Request $request, SortieRepository $sortieRepository, SiteRepository $sR): Response
    {
        // Récupère l'id du site choisi depuis l'URL : ?site=3
        $siteId = $request->query->get('site');

        // Récupère tous les sites pour remplir le select
        $sites = $sR->findAll();

        // Si un site est choisi, on filtre les sorties
        if ($siteId) {
            $sortieList = $sortieRepository->findBy(['site' => $siteId]);
        } else {
            $sortieList = $sortieRepository->findAll();
        }

        return $this->render('sortie/index.html.twig', [
            'sortieList' => $sortieList,
        ]);
    }

    #[Route('/id/{id}', name: 'id', requirements: ['id' => '\d+'])]
    public function detail(int $id, SortieRepository $sortieRepository): Response
    {
        $sortie = $sortieRepository->find($id);

        if (!$sortie) {
            $this->addFlash('error', 'Sortie not found');
        }
        return $this->render('sortie/sortiePage.html.twig', [
            'sortie' => $sortie
        ]);
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
