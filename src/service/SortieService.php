<?php

namespace App\Service;
use App\Entity\Sortie;
use App\Enum\EtatSortie;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityManagerInterface;
use Nette\Utils\DateTime;
ini_set('date.timezone', 'Europe/Paris');
class SortieService
{
public function changementEtat(Sortie $sortie, EntityManagerInterface $em){

    //convertion de toutes les dates/durée en "strtotime"

    $now = new \DateTime();
    $debut = $sortie->getDateHeureDebut();
    $dateLimiteInscription = $sortie->getDateLimiteInscription();

    // trasnformation de la durée (en time) en un interval
    $dureeTime = $sortie->getDuree();
    $dureeInterval = new \DateInterval(sprintf(
        'PT%dH%dM%dS',
        (int)$dureeTime->format('H'),
        (int)$dureeTime->format('i'),
        (int)$dureeTime->format('s')
    ));
    $fin = (clone $debut)->add($dureeInterval);


//    dd('NOW: '.$now->format('Y-m-d H:i:s'),'DEBUT: '.$debut->format('Y-m-d H:i:s'),'FIN: '.$fin->format('Y-m-d H:i:s'));

    //ne rentrer dans la boucle seulement si la sortie n'est pas terminée ou annullée
    if ($sortie->getEtat() !== EtatSortie::TERMINEE && $sortie->getEtat() !== EtatSortie::ANNULEE){

        if ($now < $dateLimiteInscription) {
            $sortie->setEtat(EtatSortie::OUVERTE);
        }elseif ($now > $dateLimiteInscription && $now < $debut) {
            $sortie->setEtat(EtatSortie::CLOTUREE);
        }elseif ( $now >= $debut && $now <= $fin){
            $sortie->setEtat(EtatSortie::EN_COURS);
        }elseif ($now > $fin){
            $sortie->setEtat(EtatSortie::TERMINEE);
        }

        $em->persist($sortie);
        $em->flush();


    }

}

}
