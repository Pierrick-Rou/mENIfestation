<?php

namespace App\Form;

use App\Entity\Group;
use App\Entity\Participant;

use App\Entity\Sortie;
use App\Repository\ParticipantRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ButtonType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class GroupType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('Name')
            ->add('Participants', EntityType::class, [
                'class' => Participant::class,
                'choice_label' => 'nom',
                'query_builder' => function (ParticipantRepository $pR) {
                    return $pR->createQueryBuilder('p')
                        ->orderBy('p.nom', 'ASC');
                },
                'attr' => [
                    'class' => 'group-select',
                ],
                // CHANGEMENTS IMPORTANTS
                'multiple' => true,
                'expanded' =>false,
                'by_reference' => false,


            ])
//            ->add('groupe_button', ButtonType::class, [
//                'label' => 'Créer Groupe',
//                'attr' => [
//                    'onclick' => "href='/group'"
//                ]
//            ])


        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Group::class,
        ]);
    }
}
