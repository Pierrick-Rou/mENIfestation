<?php

namespace App\Controller;
ini_set('date.timezone', 'Europe/Paris');


use App\DTO\FiltrageSortieDTO;
use App\Entity\Participant;
use App\Entity\Sortie;
use App\Enum\EtatSortie;
use App\Form\FiltreSortieType;
use App\Form\SortieType;

use App\Message\ReminderEmailMessage;

use App\Repository\EtatRepository;

use App\Repository\ParticipantRepository;
use App\Repository\SiteRepository;
use App\Repository\SortieRepository;

use App\Service\SortieService;

use App\Service\MailService;

use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Component\Messenger\Stamp\DelayStamp;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[Route('/sortie', name: 'app_sortie_')]
final class SortieController extends AbstractController
{

    #[Route('', name: 'home')]
    #[IsGranted('ROLE_USER')]

    public function index(Request                $request,
                          SortieRepository       $sortieRepository,
                          SiteRepository         $sR,
                          SortieService $sortieService,
                          EntityManagerInterface $em): Response

    {

        $filtrageSortieDTO = new FiltrageSortieDTO();
        $form = $this->createForm(FiltreSortieType::class, $filtrageSortieDTO);
        $form->handleRequest($request);

        /* @var Participant $user */
        $user = $this->getUser();


        $sortieList = $sortieRepository->findFilteredEvents($filtrageSortieDTO, $user);

        foreach ($sortieList as $sortie) {
            //gestion des états des sorties
            $sortieService->changementEtat($sortie, $em);
        }



        return $this->render('sortie/index.html.twig', [
            'sortieList' => $sortieList,
            'filtreForm'=>$form->createView()
        ]);
    }

    #[Route('/{id}', name: 'id', requirements: ['id' => '\d+'], methods: ['GET'])]
    public function detail(int $id, SortieRepository $sortieRepository, Sortie $sortieEntity, SortieService $sortieService, EntityManagerInterface $em): Response
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
        $sortieService->changementEtat($sortie, $em);



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

    public function inscription(
        int $id,
        SortieRepository $sortieRepository,
        ParticipantRepository $participantRepository,
        Sortie $sortieEntity,
        EntityManagerInterface $em,
        MessageBusInterface $bus,
        MailService $mailService
    ): Response {


        $user = $this->getUser();
        $sortie = $sortieRepository->find($id);

        if (!$sortie) {
            $this->addFlash('error', 'Sortie not found');
            return $this->redirectToRoute('app_sortie_id', ['id' => $id]);
        }

        if ($sortie->getEtat() !== EtatSortie::OUVERTE) {
            $this->addFlash('error', 'Vous ne pouvez pas vous inscrire à cet évènement');
            return $this->redirectToRoute('app_sortie_id', ['id' => $id]);
        }

        $isRegistered = $user && $sortieEntity->getParticipant()->contains($user);
        $participant = $participantRepository->find($user->getId());

        // INSCRIPTION
        if (!$isRegistered) {
            if ($sortie->getParticipant()->count() < $sortie->getNbInscriptionMax()) {
                $sortie->addParticipant($participant);
                $em->flush();

                $this->addFlash('success', 'Vous êtes inscrit à l\'évènement');
                $mailService->sendRegistrationMail($user->getEmail(), $sortie->getNom());

                $startDate = $sortie->getDateHeureDebut(); // DateTimeInterface (souvent DateTimeImmutable)
                // Aligner le fuseau de "now" sur celui de la date de début
                $nowTz = $startDate->getTimezone() ?? new \DateTimeZone(date_default_timezone_get());
                $now = new \DateTimeImmutable('now', $nowTz);

                // Calcul sécurisé du délai (en secondes), borné à >= 0 et casté en int
                $delaySeconds = (int) max(
                    0,
                    $startDate->getTimestamp() - $now->getTimestamp() - (48 * 3600)
                );

                if ($delaySeconds > 0) {

                    $bus->dispatch(
                        new ReminderEmailMessage(
                            $user->getEmail(),
                            $sortie->getNom(),
                            $sortie->getId(),
                            $user->getId()
                        ),
                        // Messenger attend des millisecondes (int)
                        [new DelayStamp($delaySeconds * 1000)]
                    );
                } else {
                    // Moins de 48h avant -> envoyer immédiatement
                    $mailService->sendReminderMail($user->getEmail(), $sortie->getNom(), $startDate);
                }


            } else {
                $this->addFlash('error', 'Nombre limite de participants atteint');
            }
        }

        // DESINSCRIPTION
        else {
            $sortie->removeParticipant($participant);
            $em->flush();

            $this->addFlash('success', 'Vous vous êtes désinscrit de l\'évènement');
            $mailService->sendUnregistrationMail($user->getEmail(), $sortie->getNom());

            // Note : Messenger ne permet pas d'annuler un message déjà dispatché.
            // Solution simple : le handler de ReminderEmailMessage peut vérifier que
            // l'utilisateur est toujours inscrit avant d'envoyer le mail.
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
            /* @var Participant $user */
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

    #[Route('/calendrier', name: '_cal')]
    public function calendrier(SortieRepository $sr): Response
    {
        setlocale(LC_TIME, 'fr_FR');
        $dateDuJour =  new \DateTime();
        $mois=$dateDuJour->format('F');
        $joursDansLeMois = $dateDuJour->format('t');
        $moisArr=range(1,$joursDansLeMois);


        $dayfunction=function($d): string
        {
            setlocale(LC_TIME, 'fr_FR');
            $date=new \DateTime();
            $m=$date->format('m');
            $y=$date->format('Y');
            return date('l',mktime(0,0,0,$m,$d,$y));
        };

        $m=$dateDuJour->format('m');
        $y=$dateDuJour->format('Y');
        $premierJour=date('w',mktime(0,0,0,$m,1,$y));
//        dd($jourAvant);

        $days=array_map($dayfunction,$moisArr);
        $sortiesDuMois=$sr->findByMonth();
        $arrName=$moisArr;
        $arrId=$moisArr;
        foreach ($sortiesDuMois as $sortie) {
            $arrName[$sortie->getDateHeureDebut()->format('d')]=$sortie->getNom();
            $arrId[$sortie->getDateHeureDebut()->format('d')]=$sortie->getId();
        }
//        dd($days);


        return $this->render('sortie/calendrier.html.twig', ['moisArr'=>$moisArr,
            'dateDuJour'=>$dateDuJour,
            'nbJours'=>$joursDansLeMois,
            'jours'=>$days,
            'mois'=>$mois,
            'events'=>$arrName,
            'eventsId'=>$arrId,
            'premierJour'=>$premierJour,
        ]);
    }

}
