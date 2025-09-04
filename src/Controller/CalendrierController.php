<?php

namespace App\Controller;

use App\DTO\FiltrageSortieDTO;
use App\Entity\Participant;
use App\Repository\SortieRepository;
use App\Service\CalendrierService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;


final class CalendrierController extends AbstractController
{

    #[Route('/calendrier', name: 'app_cal')]
    public function calendrier(CalendrierService $calendrierService, FiltrageSortieDTO $filtrageSortieDTO): Response
    {
        /* @var Participant $user */
        $user = $this->getUser();
        $data = $calendrierService->getCalendarData($filtrageSortieDTO, $user);

        return $this->render('calendrier/calendrier.html.twig', $data);
    }
}
