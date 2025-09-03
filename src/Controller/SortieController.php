<?php

namespace App\Controller;
ini_set('date.timezone', 'Europe/Paris');


use App\DTO\FiltrageSortieDTO;
use App\Entity\Commentaire;
use App\Entity\Lieu;
use App\Entity\Participant;
use App\Entity\Sortie;
use App\Entity\Ville;
use App\Enum\EtatSortie;
use App\Form\CommentaireType;
use App\Form\FiltreSortieType;
use App\Form\LieuType;
use App\Form\SortieType;
use App\Form\VilleType;
use App\Message\ReminderEmailMessage;
use App\Repository\EtatRepository;
use App\Repository\LieuRepository;
use App\Repository\ParticipantRepository;
use App\Repository\SiteRepository;
use App\Repository\SortieRepository;
use App\Service\MailService;
use App\Service\SortieService;
use DateTimeImmutable;
use DateTimeZone;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Component\Messenger\Stamp\DelayStamp;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Symfony\UX\Map\Bridge\Leaflet\LeafletOptions;
use Symfony\UX\Map\Bridge\Leaflet\Option\TileLayer;
use Symfony\UX\Map\InfoWindow;
use Symfony\UX\Map\Map;
use Symfony\UX\Map\Marker;
use Symfony\UX\Map\Point;

#[IsGranted('ROLE_USER')]
#[Route('/sortie', name: 'app_sortie_')]
final class SortieController extends AbstractController
{


    #[Route('', name: 'home')]
    public function index(Request                $request,
                          SortieRepository       $sortieRepository,
                          SiteRepository         $sR,
                          SortieService          $sortieService,
                          EntityManagerInterface $em): Response

    {

        $filtrageSortieDTO = new FiltrageSortieDTO();
        $form = $this->createForm(FiltreSortieType::class, $filtrageSortieDTO, [
            'user' => $this->getUser()
        ]);
        $form->handleRequest($request);

        /* @var Participant $user */
        $user = $this->getUser();


        $sortieList = $sortieRepository->findFilteredEventsWithMapData($filtrageSortieDTO, $user);

        $map = (new Map('default'))
            ->center(new Point(45.7534031, 4.8295061))
            ->zoom(6);

        foreach ($sortieList as $sortie) {
            if ($sortie->getLieu()->getLatitude() === null || $sortie->getLieu()->getLongitude() === null) {
                continue; // Protection si certains lieux sont mal définis
            }


            $position = new Point($sortie->getLieu()->getLatitude(), $sortie->getLieu()->getLongitude());
            $title = $sortie->getNom();

            $content = sprintf(
                '<h5><a href="%s">%s</a></h5><p>%s</p><p><strong>Lieu:</strong> %s, %s %s</p><p><strong>Date:</strong> %s</p>',
                htmlspecialchars('sortie/' . $sortie->getId()),
                htmlspecialchars("{$sortie->getNom()}"),
                nl2br(htmlspecialchars($sortie->getInfosSortie())),
                htmlspecialchars($sortie->getLieu()->getNom()),
                htmlspecialchars($sortie->getLieu()->getVille()->getCodePostal()),
                htmlspecialchars($sortie->getLieu()->getVille()->getNom()),
                $sortie->getDateHeureDebut()->format('d/m/Y H:i')
            );

            $map->addMarker(new Marker(
                position: $position,
                title: $title,
                infoWindow: new InfoWindow(content: $content)
            ));
        }

        $map->options((new LeafletOptions())
            ->tileLayer(new TileLayer(
                url: 'https://tile.openstreetmap.org/{z}/{x}/{y}.png',
                attribution: '© <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a>',
                options: ['maxZoom' => 19]
            ))
        );


        foreach ($sortieList as $sortie) {
            //gestion des états des sorties
            $sortieService->changementEtat($sortie, $em);
        }


        return $this->render('sortie/index.html.twig', [
            'sortieList' => $sortieList,
            'filtreForm' => $form->createView(),
            'map' => $map,
        ]);
    }

    #[Route('/{id}', name: 'id', requirements: ['id' => '\d+'], methods: ['GET', 'POST'])]
    public function detail(int                    $id,
                           SortieRepository       $sortieRepository,
                           SortieService          $sortieService,
                           EntityManagerInterface $em,
                           Request                $request): Response
    {
        $user = $this->getUser();

        $sortie = $sortieRepository->find($id);


        if (!$sortie) {
            $this->addFlash('error', 'Cette sortie n\'existe pas');
            return $this->redirectToRoute('app_sortie_home');
        }

        $isRegistered = false;
        if ($user) {
            $isRegistered = $sortie->getParticipant()->contains($user);
        }


        //gestion des états des sorties
        $sortieService->changementEtat($sortie, $em);

        $commentForm = $this->createForm(CommentaireType::class, $comment = new Commentaire());
        $commentForm->handleRequest($request);

        if ($commentForm->isSubmitted() && $commentForm->isValid()) {
            $comment->setSortie($sortie);
            $comment->setDate(new \DateTime());
            $comment->setAuteur($user);
            $em->persist($comment);
            $em->flush();
            return $this->redirectToRoute('app_sortie_id', ['id' => $id]);
        }

        $nbParticipant = $sortie->getParticipant()->count();
        $etat = $sortie->getEtat();
        $placeRestante = $sortie->getNbInscriptionMax() - $nbParticipant;

        $commentaires = $sortie->getCommentaires();


        return $this->render('sortie/sortiePage.html.twig', [
            'sortie' => $sortie,
            'isRegistered' => $isRegistered,
            'etat' => $etat,
            'nbParticipant' => $nbParticipant,
            'placeRestante' => $placeRestante,
            'commentaires' => $commentaires,
            'commentForm' => $commentForm->createView(),
        ]);
    }

    #[Route('/{id}/inscription', name: 'inscription', requirements: ['id' => '\d+'], methods: ['GET'])]
    public function inscription(
        int                    $id,
        SortieRepository       $sortieRepository,
        ParticipantRepository  $participantRepository,
        Sortie                 $sortieEntity,
        EntityManagerInterface $em,
        MessageBusInterface    $bus,
        MailService            $mailService
    ): Response
    {


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
                $nowTz = $startDate->getTimezone() ?? new DateTimeZone(date_default_timezone_get());
                $now = new DateTimeImmutable('now', $nowTz);

                // Calcul sécurisé du délai (en secondes), borné à >= 0 et casté en int
                $delaySeconds = (int)max(
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
                    $mailService->sendReminderMail($user->getEmail(), $sortie->getNom(), $sortie->getId(), $user->getNom(), $sortieRepository);
                }


            } else {
                $this->addFlash('error', 'Nombre limite de participants atteint');
            }
        } // DESINSCRIPTION
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


    #[Route('/create', name: 'create', methods: ['GET', 'POST'])]
    public function create(EtatRepository $er, SessionInterface $session): Response
    {


        // création des forms
        $sortie = new Sortie();
        $sortieForm = $this->createForm(SortieType::class, $sortie, [
            'user' => $this->getUser()
        ]);
        $lieu = new Lieu();
        $lieuForm = $this->createForm(LieuType::class, $lieu);

        return $this->render("sortie/sortieForm.html.twig", [
            "sortieForm" => $sortieForm,
            "lieuForm" => $lieuForm,

        ]);
    }

    #[Route('/validForm', name: 'validForm', methods: ['POST'])]
    public function validForm(Request $request, EntityManagerInterface $entityManager, Security $security): Response
    {
        $sortie = new Sortie();

        // 2. Crée le formulaire
        $form = $this->createForm(SortieType::class, $sortie, [
            'user' => $this->getUser(), // obligatoire
        ]);
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
            return $this->redirectToRoute('app_sortie_home',  [
                'user' => $this->getUser()
            ]);
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

    #[Route('/ajoutLieu', name: 'ajoutLieu', methods: ['GET', 'POST'])]
    public function ajoutLieu(LieuRepository $er, Request $request, EntityManagerInterface $em): JsonResponse
    {
        $lieu = new Lieu();
        $form = $this->createForm(LieuType::class, $lieu);
        $form->handleRequest($request);

        // mise en BDD
        if ($form->isSubmitted() && $form->isValid()) {
            $em->persist($lieu);
            $em->flush();

            //JsonResponse afin de pouvoir l'exploiter dans JavaScript (ajoutLieu.js)
            return new JsonResponse([
                'id' => $lieu->getId(),
                'nom' => $lieu->getNom(),
                'latitude' => $lieu->getLatitude(),
                'longitude' => $lieu->getLongitude(),
            ]);

        }
        return new JsonResponse([]);
    }

}
