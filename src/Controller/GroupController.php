<?php

namespace App\Controller;

use App\Entity\Group;
use App\Entity\Participant;
use App\Form\GroupType;
use App\Form\SortieType;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
#[Route('/group', name: 'app_group_')]
final class GroupController extends AbstractController
{
    #[Route('', name: 'index')]
    public function index(Request $request,
                          EntityManagerInterface $eM,
                          Security $s): Response
    {


        $group = new Group();
        $groupForm = $this->createForm(GroupType::class, $group);

        $groupForm->handleRequest($request);
        if ($groupForm->isSubmitted() && $groupForm->isValid()){
            $group->setGroupFounder($this->getUser());

            $founder = $s->getUser();
            /** var Participant $founder */
            $group->addParticipant($founder);
            $eM->persist($group);
            $eM->flush();
            $this->addFlash('success', 'Group created successfully');
            return $this->redirectToRoute('app_group_index');
        }
        return $this->render('group/index.html.twig', ['group_form' => $groupForm
        ]);
    }
    #[Route('/{id}', name: 'detail', requirements: ['id' => '\d+'], methods: ['GET'])]
    public function detail(int $id, EntityManagerInterface $eM): Response
    {
        $group = $eM->getRepository(Group::class)->find($id);
        return $this->render('group/detail.html.twig', ['group' => $group]);
    }
}
