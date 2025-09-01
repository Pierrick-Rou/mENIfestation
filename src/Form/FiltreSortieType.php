<?php

namespace App\Form;

use App\DTO\FiltrageSortieDTO;
use App\Entity\Site;
use App\Entity\Ville;
use App\Enum\EtatSortie;
use App\Repository\SiteRepository;
use App\Repository\VilleRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;
use Symfony\Component\OptionsResolver\OptionsResolver;

class FiltreSortieType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('nomSortie', TextType::class, ['required' => false])
            ->add('site', EntityType::class, [
                'required' => false,
                'placeholder' => 'Tous les syndicats',
                'class' => Site::class,
                'choice_label' => 'Nom',
                'multiple' => false,
                'by_reference' => false,
                'query_builder' => function (SiteRepository $sr) {
                    return $sr->createQueryBuilder('s')
                        ->orderBy('s.nom', 'ASC');
                }
            ])
            ->add('dateDebut', DateTimeType::class, ['required' => false])
            ->add('dateFin', DateTimeType::class, ['required' => false])
            ->add('ville', EntityType::class, [
                'required' => false,
                'placeholder' => 'Toutes les villes',
                'class' => Ville::class,
                'choice_label' => 'Nom',
                'multiple' => false,
                'by_reference' => false,
                'query_builder' => function (VilleRepository $vr) {
                    return $vr->createQueryBuilder('v')
                        ->select('v')
                        ->groupBy('v.nom')
                        ->orderBy('v.nom', 'ASC');
                }
            ])
            ->add('organisateur', CheckboxType::class, [
                'required' => false,
                'attr' => [
                    'class' => 'checkbox'
                ]
            ])
            ->add('inscrit', CheckboxType::class, ['required' => false])
            ->add('nonInscrit', CheckboxType::class, ['required' => false])
            ->add('etat', ChoiceType::class, [
                'required' => false,
                'choices' => EtatSortie::choices(),
                'placeholder' => 'Tous les statuts',
            ])
            ->add('submit', SubmitType::class, [
                'label' => 'Filtrer'
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => FiltrageSortieDTO::class,
            'method' => 'GET',
        ]);
    }
}
