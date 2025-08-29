<?php

namespace App\Controller;


use App\DTO\FiltrageSortieDTO;
use App\Entity\Participant;
use App\Entity\Sortie;
use App\Enum\EtatSortie;
use App\Form\FiltreSortieType;
use App\Form\RegistrationType;
use App\Form\SortieType;
use App\Repository\ParticipantRepository;
use App\Repository\SiteRepository;
use App\Repository\EtatRepository;
use App\Service\MailService;
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
    public function index(Request $request,
                          SortieRepository $sortieRepository,
                          SiteRepository $sR,
                          EntityManagerInterface $em): Response
    {
        $filtrageSortieDTO = new FiltrageSortieDTO();
        $form = $this->createForm(FiltreSortieType::class, $filtrageSortieDTO);
        $form->handleRequest($request);

        /* @var Participant $user*/
        $user = $this->getUser();


        $sortieList = $sortieRepository->findFilteredEvents($filtrageSortieDTO, $user);

        foreach ($sortieList as $sortie) {
            //gestion des états des sorties

            //convertion de toutes les dates/durée en "strtotime"
            $now = time();
            $debut = strtotime($sortie->getDateHeureDebut()->format('y-m-d H:i:s'));
            $duree = $sortie->getDuree()->format('H:i:s');
            $dateLimiteInscription = strtotime($sortie->getDateLimiteInscription()->format('y-m-d H:i:s'));
            $fin = strtotime("+$duree",$debut);

            //ne rentrer dans la boucle seulement si la sortie n'est pas terminée ou annullée
            if ($sortie->getEtat() !== EtatSortie::TERMINEE && $sortie->getEtat() !== EtatSortie::ANNULEE){

                if ($now < $dateLimiteInscription) {
                    $sortie->setEtat(EtatSortie::OUVERTE);
                }elseif ($now > $dateLimiteInscription && $now < $debut) {
                    $sortie->setEtat(EtatSortie::CLOTUREE);
                }elseif ( $now > $debut && $now < $fin){
                    $sortie->setEtat(EtatSortie::EN_COURS);
                }elseif ($now > $fin){
                    $sortie->setEtat(EtatSortie::TERMINEE);
                }

                $em->persist($sortie);
                $em->flush();

            }
        }


        return $this->render('sortie/index.html.twig', [
            'sortieList' => $sortieList,
            'filtreForm'=>$form->createView()
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



        //gestion des états des sorties

        //convertion de toutes les dates/durée en "strtotime"
        $now = time();
        $debut = strtotime($sortie->getDateHeureDebut()->format('y-m-d H:i:s'));
        $duree = $sortie->getDuree()->format('H:i:s');
        $dateLimiteInscription = strtotime($sortie->getDateLimiteInscription()->format('y-m-d H:i:s'));
        $fin = strtotime("+$duree",$debut);

        //ne rentrer dans la boucle seulement si la sortie n'est pas terminée ou annullée
        if ($sortie->getEtat() !== EtatSortie::TERMINEE && $sortie->getEtat() !== EtatSortie::ANNULEE){

            if ($now < $dateLimiteInscription) {
                $sortie->setEtat(EtatSortie::OUVERTE);
            }elseif ($now > $dateLimiteInscription && $now < $debut) {
                $sortie->setEtat(EtatSortie::CLOTUREE);
            }elseif ( $now > $debut && $now < $fin){
                $sortie->setEtat(EtatSortie::EN_COURS);
            }elseif ($now > $fin){
                $sortie->setEtat(EtatSortie::TERMINEE);
            }

        }


        $nbParticipant = $sortie->getParticipant()->count();
        $etat = $sortie->getEtat();
        $placeRestante = $sortie->getNbInscriptionMax() - $nbParticipant;


        return $this->render('sortie/sortiePage.html.twig', [
            'sortie' => $sortie,
            'isRegistered' => $isRegistered,
            'etat' => $etat,
            'nbParticipant' => $nbParticipant,
            'placeRestante' => $placeRestante,
        ]);
    }

    #[Route('/{id}/inscription', name: 'inscription', requirements: ['id' => '\d+'], methods: ['GET'])]
    public function inscription(int $id,
                                SortieRepository $sortieRepository,
                                ParticipantRepository $participantRepository,
                                Sortie $sortieEntity,
                                EntityManagerInterface $em,

                                MailService $mailService): Response
    {

        $user = $this->getUser();
        $sortie = $sortieRepository->find($id);

        if (!$sortie) {
            $this->addFlash('error', 'Sortie not found');
        }

        if ($sortie->getEtat() === EtatSortie::OUVERTE) {

            $isRegistered = false;
            if ($user && $sortie) {
                $isRegistered = $sortieEntity->getParticipant()->contains($user);
            }

            $participant = $participantRepository->find($user->getId());
            if (!$isRegistered) {
                if ($sortie->getParticipant()->count() < $sortie->getNbInscriptionMax()) {
                    $sortie->addParticipant($participant);

                    $em->persist($sortie);
                    $em->flush();
                    $this->addFlash('success', 'Vous êtes inscrit à l\'évènement');
                    $mailService->sendRegistrationMail($user->getEmail(), $sortie->getNom());
                } else {
                    $this->addFlash('error', 'Nombre limite de participant atteint');
                }


            } else if ($isRegistered) {
                $sortie->removeParticipant($participant);

                $em->persist($sortie);
                $em->flush();

                $this->addFlash('success', 'Vous vous êtes désinscrit de l\'évènement');
                $mailService->sendUnregistrationMail($user->getEmail(), $sortie->getNom());
            }
        } else {
            $this->addFlash('error', 'Vous ne pouvez pas vous inscrire a cet évènement');
        }

        return $this->redirectToRoute('app_sortie_id', ['id' => $id]);
    }


    #[Route('/create', name: 'create', methods: ['GET','POST'])]
    public function create(EtatRepository $er): Response
    {
        $sortie = new Sortie();
        $sortieForm = $this->createForm(SortieType::class, $sortie);
        return $this->render("sortie/sortieForm.html.twig", [
            "sortieForm" => $sortieForm
        ]);
    }
    #[Route('/validForm', name: 'validForm', methods: ['POST'])]
    public function validForm(Request $request, EntityManagerInterface $entityManager, Security $security): Response
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
            $etat = EtatSortie::CREEE;
            $sortie->setEtat($etat);
            $sortie->addParticipant($user);

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
    #[Route('/{id}/delete', name: 'delete', methods: ['GET'])]
    public function delete(Sortie $sortie, EntityManagerInterface $em): Response
    {
        $em->remove($sortie);
        $em->flush();
        return $this->redirectToRoute('app_sortie_home');
    }

}
