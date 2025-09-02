<?php

namespace App\Controller;

use App\Repository\SortieRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;


final class TestController extends AbstractController
{
    #[Route('/', name: 'app_test')]
    public function index(): Response
    {
        return $this->render('test/creer-ville.html.twig', [
            'controller_name' => 'TestController',
        ]);
    }

    #[Route('/test-admin', name: 'app_test_admin')]
    #[ISGranted('ROLE_ADMIN')]
    public function indexAdmin(): Response
    {
        return $this->render('test/index_admin.html.twig', [
            'controller_name' => 'TestController',
        ]);
    }



    #[Route('/testrepo', name: 'app_t')]
    public function t(SortieRepository $sr): Response
    {
        $t = $sr->findAll();
        dd($t);
        return $this->render('test/index.html.twig');
    }

    #[Route('/calendrier', name: 'app_cal')]
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


        return $this->render('test/calendrier.html.twig', ['moisArr'=>$moisArr,
                                                                'dateDuJour'=>$dateDuJour,
                                                                'nbJours'=>$joursDansLeMois,
                                                                'jours'=>$days,
                                                                'mois'=>$mois,
                                                                'events'=>$arrName,
                                                                'eventsId'=>$arrId,
                                                                'premierJour'=>$premierJour,
        ]);
    }

    #[Route('/test-olv', name: 'app_test_olv')]
    public function test(SortieRepository $sR): void
    {
        $sorties = $sR->findAll();
        $output = "";

        foreach ($sorties as $sortie) {
            $output .= sprintf(
                "- %s le %s à %s l'état est:%s (participants: %d). Organisé par :%s\n",
                $sortie->getNom(),
                $sortie->getDateHeureDebut()->format('d/m/Y H:i'),
                $sortie->getLieu()->getNom(),
                $sortie->getEtat()->value,
                count($sortie->getParticipant()),
                $sortie->getSite()->getNom()
            );
        }

        dd($output);
    }

}
