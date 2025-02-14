<?php

namespace App\Form;

use App\Entity\Candidature;
use App\Entity\User;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\File;

class CandidatureType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            // Champ Candidat 
            ->add('candidat', EntityType::class, [
                'class' => User::class,
                'choice_label' => 'nom', 
                'label' => 'Nom du candidat',
                'attr' => ['class' => 'form-control'],
                'required' => true, 
            ])
            // Champ Compétences
            ->add('competences', TextareaType::class, [
                'label' => 'Compétences',
                'required' => true,
                'attr' => [
                    'class' => 'form-control',
                    'rows' => 3,
                    'placeholder' => 'Décrivez vos compétences',
                ],
            ])
            // Champ Lettre de motivation
            ->add('lettreMotivation', TextareaType::class, [
                'label' => 'Lettre de motivation',
                'required' => true, 
                'attr' => [
                    'class' => 'form-control',
                    'rows' => 5,
                    'placeholder' => 'Écrivez votre lettre de motivation',
                ],
            ])
            // Champ CV
            ->add('cv', FileType::class, [
                'label' => 'CV (PDF ou DOC)',
                'mapped' => false,
                'required' => true,
                'constraints' => [
                    new File([
                        'maxSize' => '1024k', 
                        'mimeTypes' => [
                            'application/pdf',
                            'application/msword',
                            'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
                        ],
                        'mimeTypesMessage' => 'Veuillez télécharger un fichier PDF ou DOC valide.',
                    ])
                ],
                'attr' => ['class' => 'form-control-file'],
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Candidature::class, 
        ]);
    }
}