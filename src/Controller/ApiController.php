<?php

namespace App\Controller;

use App\Entity\Group;
use App\Entity\Participant;
use App\Repository\GroupRepository;
use App\Repository\ParticipantRepository;
use Doctrine\ORM\EntityManager;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Serializer\SerializerInterface;
#[Route('/api', name : 'app_api')]
final class ApiController extends AbstractController
{
    #[Route('/getAllMembers', name: '_getAllMembers', methods: ['GET'])]
    public function getAll(
        Request $request,
        ParticipantRepository $pr,
        SerializerInterface $serializer,
        GroupRepository $gr // <-- AJOUT
    ): JsonResponse {
        $q = $request->query->get('q', '');
        $excludeGroupId = $request->query->getInt('excludeGroup', 0);

        // Récupérer tous les participants
        $participants = $pr->createQueryBuilder('p');

        if ($q) {
            $participants
                ->andWhere('p.nom LIKE :q OR p.email LIKE :q')
                ->setParameter('q', '%' . $q . '%');
        }

        // ... existing code ...
        if ($excludeGroupId > 0) {
            $group = $gr->find($excludeGroupId);
            if ($group) {
                // Attention: adapter 'p.groupe' au vrai nom de la relation dans Participant (probablement 'groupe')
                $participants
                    ->andWhere(':grp NOT MEMBER OF p.groupe')
                    ->setParameter('grp', $group);
            }
        }
        // ... existing code ...

        $result = $participants->getQuery()->getResult();

        $json = $serializer->serialize(
            $result,
            'json',
            ['groups' => 'participant:list']
        );

        return new JsonResponse($json, 200, [], true);
    }


    #[Route('addMemberToGroup/{idM}/{idG}', name: '_addMemberToGroup', requirements: ['idM' => '\d+', 'idG' => '\d+'], methods: ['GET'])]
    final function addMemberToGroup(ParticipantRepository $pr, GroupRepository $gR, SerializerInterface $serializer, Participant $p, Group $g, EntityManager $eM): void
    {
        $group = new Group();
        $member = new Participant();
        $group->addParticipant($member);
        $eM->persist($group);
        $eM->flush();
    }
}
