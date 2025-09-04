<?php

namespace App\Service;

use App\DTO\FiltrageSortieDTO;
use App\Entity\Participant;
use App\Repository\SortieRepository;
use DateTime;

class CalendrierService
{
    public function __construct(
        private SortieRepository $sortieRepository
    ) {}

    public function getCalendarData(FiltrageSortieDTO $filtrageSortieDTO, Participant $user): array
    {
        setlocale(LC_TIME, 'fr_FR');
        $dateDuJour = $date ?? new DateTime();

        $mois = $dateDuJour->format('F');
        $joursDansLeMois = $dateDuJour->format('t');
        $moisArr = range(1, $joursDansLeMois);

        $dayFunction = function ($d) use ($dateDuJour): string {
            $m = $dateDuJour->format('m');
            $y = $dateDuJour->format('Y');
            return date('l', mktime(0, 0, 0, $m, $d, $y));
        };

        $m = $dateDuJour->format('m');
        $y = $dateDuJour->format('Y');
        $premierJour = date('w', mktime(0, 0, 0, $m, 1, $y));

        $days = array_map($dayFunction, $moisArr);
        $sortiesDuMois = $this->sortieRepository->findFilteredEventsWithMapData($filtrageSortieDTO, $user);

        $arrName = $moisArr;
        $arrId = $moisArr;

        foreach ($sortiesDuMois as $sortie) {
            $dayIndex = (int)$sortie->getDateHeureDebut()->format('d');
            $arrName[$dayIndex - 1] = $sortie->getNom();
            $arrId[$dayIndex - 1] = $sortie->getId();
        }

        return [
            'moisArr'     => $moisArr,
            'dateDuJour'  => $dateDuJour,
            'nbJours'     => $joursDansLeMois,
            'jours'       => $days,
            'mois'        => $mois,
            'events'      => $arrName,
            'eventsId'    => $arrId,
            'premierJour' => $premierJour,
        ];
    }
}
