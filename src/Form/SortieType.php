<?php

namespace App\Form;

use App\Entity\Group;
use App\Entity\Lieu;
use App\Entity\Sortie;
use App\Entity\Ville;
use App\Repository\GroupRepository;
use App\Repository\LieuRepository;
use App\Repository\VilleRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ButtonType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Routing\RouterInterface;

class SortieType extends AbstractType
{

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $user = $options['user'];
        $builder
            ->add('nom')
            ->add('lieu', EntityType::class, [
                'class' => Lieu::class,
                'choice_label' => 'nom',
                'placeholder' => 'Choisissez un lieu',
                'required' => true,
                'query_builder' => function (LieuRepository $lr) use ($options) {
                    return $lr->createQueryBuilder('l')
                        ->join('l.Ville', 'v')
                        ->orderBy('l.nom', 'ASC');
                },
                'group_by' => function ($lieu) {
                    return $lieu->getVille()->getNom();
                },
                'attr' => [
                    'class' => 'lieu-select',
                    'data-ville-field' => '#sortie_lieu_ville'
                ]
            ])
            ->add('dateHeureDebut')
            ->add('duree')
            ->add('dateLimiteInscription')
            ->add('nbInscriptionMax')
            ->add('infosSortie', textareaType::class)
            ->add('groupes', EntityType::class, [
                'class' => Group::class,
                'choice_label' => 'name',
                // 'placeholder' n'est pas utilisé quand expanded=true
                'query_builder' => function (GroupRepository $gR) use ($options) {
                    $user = $options['user'];
                    return $gR->createQueryBuilder('g')
                        ->where(':user MEMBER OF g.participants')
                        ->setParameter('user', $user)
                        ->orderBy('g.Name', 'ASC');
                },
                'attr' => [
                    'class' => 'group-select',
                ],
                'multiple' => true,
                'expanded' => true,
                'by_reference' => false,
            ]);






    }


    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Sortie::class,
        ]);

        // On rend l'option 'user' obligatoire
        $resolver->setRequired('user');

        // On peut préciser le type attendu pour plus de sécurité
        $resolver->setAllowedTypes('user', ['App\Entity\Participant']);
    }

}
