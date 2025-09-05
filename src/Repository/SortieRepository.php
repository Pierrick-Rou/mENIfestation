<?php

namespace App\Repository;

use App\DTO\FiltrageSortieDTO;
use App\Entity\Participant;
use App\Entity\Sortie;
use DateTime;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Sortie>
 */
class SortieRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Sortie::class);
    }

    public function findByMonth(): array
    {

        return $this->createQueryBuilder('s')
            ->andWhere('s.dateHeureDebut > :dateDebut')
            ->andWhere('s.dateHeureDebut < :dateFin')
            ->setParameter('dateDebut', date('Y-m-01'))
            ->setParameter('dateFin', date('Y-m-01', strtotime('+1 month')))
            ->orderBy('s.dateHeureDebut', 'ASC')
            ->getQuery()
            ->getResult();
    }

    public function findFilteredEventsWithMapData(FiltrageSortieDTO $filtreDTO, Participant $user): array
    {
        $qb = $this->createQueryBuilder('s')
            ->join('s.lieu', 'l')
            ->join('l.Ville', 'v')
            ->where('s.dateHeureDebut > :oneMonthAgo')
            ->setParameter('oneMonthAgo', new \DateTime('-1 month'))
            ->orderBy('s.dateHeureDebut', 'ASC');

        // Filtres
        if ($filtreDTO->getNomSortie()) {
            $qb->andWhere('s.nom LIKE :nom')
                ->setParameter('nom', "%{$filtreDTO->getNomSortie()}%");
        }

        if ($filtreDTO->getSite()) {
            $qb->andWhere('s.site = :site')
                ->setParameter('site', $filtreDTO->getSite());
        }

        if ($filtreDTO->getDateDebut()) {
            $qb->andWhere('s.dateHeureDebut >= :dateDebut')
                ->setParameter('dateDebut', $filtreDTO->getDateDebut());
        }

        if ($filtreDTO->getDateFin()) {
            $qb->andWhere('s.dateHeureDebut <= :dateFin')
                ->setParameter('dateFin', $filtreDTO->getDateFin());
        }

        if ($filtreDTO->getVille()) {
            $qb->andWhere('v.nom = :ville')
                ->setParameter('ville', $filtreDTO->getVille()->getNom());
        }
        if (!$filtreDTO->getGroupes()->isEmpty()) {
            // Adapter 's.groupes' si le nom de l'association diffère dans l'entité Sortie
            $qb->join('s.groupes', 'g')
                ->andWhere('g IN (:groupes)')
                ->setParameter('groupes', $filtreDTO->getGroupes()->toArray());
        }


        if ($filtreDTO->getOrganisateur()) {
            $qb->andWhere('s.organisateur = :organisateur')
                ->setParameter('organisateur', $user);
        }

        if ($filtreDTO->getInscrit() && !$filtreDTO->getNonInscrit()) {
            $qb->leftJoin('s.participant', 'p')
                ->andWhere(':participant MEMBER OF s.participant')
                ->setParameter('participant', $user);
        } elseif (!$filtreDTO->getInscrit() && $filtreDTO->getNonInscrit()) {
            $qb->leftJoin('s.participant', 'p2')
                ->andWhere(':participant NOT MEMBER OF s.participant')
                ->setParameter('participant', $user);
        }

        if ($filtreDTO->getEtat()) {
            $qb->andWhere('s.etat = :etat')
                ->setParameter('etat', $filtreDTO->getEtat());
        }


        return $qb->getQuery()->getResult();
    }
    public function findParticipantByNameInSortie(int $sortieId, string $nom): ?Participant
    {
        return $this->createQueryBuilder('s')
            ->join('s.participant', 'p')
            ->andwhere('s.id = :sortieId')
            ->andWhere('p.nom = :nom')
            ->setParameter('sortieId', $sortieId)
            ->setParameter('nom', $nom)
            ->getQuery()
            ->getOneOrNullResult();
    }


    //    /**
    //     * @return Sortie[] Returns an array of Sortie objects
    //     */
    //    public function findByExampleField($value): array
    //    {
    //        return $this->createQueryBuilder('s')
    //            ->andWhere('s.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->orderBy('s.id', 'ASC')
    //            ->setMaxResults(10)
    //            ->getQuery()
    //            ->getResult()
    //        ;
    //    }

    //    public function findOneBySomeField($value): ?Sortie
    //    {
    //        return $this->createQueryBuilder('s')
    //            ->andWhere('s.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->getQuery()
    //            ->getOneOrNullResult()
    //        ;
    //    }
}
