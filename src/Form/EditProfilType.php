<?php

namespace App\Form;

use App\Entity\Participant;
use App\Entity\Site;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\TelType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Validator\Constraints\File;

class EditProfilType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('nom', TextType::class, [
                'label' => 'Nom',
                'constraints' => [
                    new Assert\NotBlank([
                        'message' => 'Veuillez renseigner votre nom',
                    ]),
                ]
            ])
            ->add('prenom', TextType::class, [
                'label' => 'Prenom',
                'constraints' => [
                    new Assert\NotBlank([
                        'message' => 'Veuillez renseigner votre prenom',
                    ]),
                ]])
            ->add('email', EmailType::class, [
                'label' => 'Email',
                'required' => true,
                'constraints' => [
                    new Assert\Email([
                        'message' => 'Email non valide',
                    ]),
                    new Assert\NotBlank([
                        'message' => 'Veuillez saisir un email',
                    ])
                ]
            ])
            ->add('telephone', TelType::class, [
                'label' => 'Telephone',
                'required' => true,
                'constraints' => [
                    new Assert\Regex([
                        'pattern' => '/^(0|\+33)[0-9]{9}$/',
                        'message' => 'Numéro de téléphone non valide'
                    ])
                ]])
            ->add('site', EntityType::class, [
                'class' => Site::class,          // ton entité
                'choice_label' => 'nom',         // propriété à afficher dans le select
                'placeholder' => 'Choisissez un site', // optionnel
                'required' => true,
            ])
            ->add('poster_file', FileType::class, [
                'required' => false,
                'mapped' => false,
                'constraints' => [
                    new File([
                            'maxSize' => '2M',
                            'mimeTypes' => [
                                'image/jpeg',
                                'image/png',
                                'image/webp',
                            ],
                        ]
                    )]
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Participant::class,
        ]);
    }
}
