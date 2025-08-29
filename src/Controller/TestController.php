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
        return $this->render('test/index.html.twig', [
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





    #[Route('/calendrier', name: 'app_cal')]
    public function calendrier(SortieRepository $sr): Response
    {
        $dateDuJour =  new \DateTime();
        $mois=$dateDuJour->format('m');
        $joursDansLeMois = $dateDuJour->format('t');
        $moisArr=range(1,$joursDansLeMois);
        $sortiesDuMois=$sr->findByMonth();
        $arr=$moisArr;
        foreach ($sortiesDuMois as $sortie) {
//            $event=[];
//            $event['nom']=$sortie->getNom();
//            $event['id']=$sortie->getId();
            $arr[$sortie->getDateHeureDebut()->format('d')]=$sortie->getNom();
//            $moisArr[$sortie->getDateHeureDebut()->format('d')-1]=$arr;
        }
//        dd($arr);


        return $this->render('test/calendrier.html.twig', ['moisArr'=>$moisArr,'dateDuJour'=>$dateDuJour, 'nbJours'=>$joursDansLeMois, 'mois'=>$mois, 'events'=>$arr
        ]);
    }

}
