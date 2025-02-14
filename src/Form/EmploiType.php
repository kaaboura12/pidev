<?php

namespace App\Form;

use App\Entity\Emploi;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\MoneyType;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use App\Entity\User;

class EmploiType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
        ->add('user', EntityType::class, [
            'class' => User::class,
            'choice_label' => 'id', 
            'label' => 'Votre ID Utilisateur',
            'placeholder' => 'Sélectionnez votre ID',
            'required' => true,
        ])
            ->add('titre', TextType::class, [
                'label' => 'Title',
                'constraints' => [
                    new Assert\NotBlank([
                        'message' => 'The title cannot be empty.'
                    ]),
                    new Assert\Length([
                        'min' => 3,
                        'minMessage' => 'The title must have at least {{ limit }} characters.'
                    ])
                ]
            ])
            ->add('description', TextareaType::class, [
                'label' => 'Description',
                'required' => false,
               'constraints' => [
                    new Assert\NotBlank([
                        'message' => 'The title cannot be empty.'
                    ]),
                    new Assert\Length([
                        'min' => 3,
                        'minMessage' => 'The title must have at least {{ limit }} characters.'
                    ])
                ]
            ])
            ->add('competences_requises', TextareaType::class, [
                'label' => 'Required Skills',
                'required' => false,
                'constraints' => [
                    new Assert\NotBlank([
                        'message' => 'Required skills cannot be empty.'
                    ])
                ]
            ])
            ->add('budget', MoneyType::class, [
                'label' => 'Budget',
                'required' => false,
                'constraints' => [
                    new Assert\PositiveOrZero([
                        'message' => 'The budget must be a positive number.'
                    ])
                ]
            ])
            ->add('lieu', TextType::class, [
                'label' => 'Location',
                'required' => false,
                'constraints' => [
                    new Assert\Length([
                        'max' => 255,
                        'maxMessage' => 'The location cannot exceed {{ limit }} characters.'
                    ])
                ]
            ])
            ->add('date_publication', DateTimeType::class, [
                'label' => 'Publication Date',
                'widget' => 'single_text',
            ])
            ->add('statut', ChoiceType::class, [
                'choices' => [
                    'Ouvert' => 'Ouvert',
                    'En cours' => 'En cours',
                    'Fermé' => 'Fermé',
                ],
                'expanded' => false,
                'multiple' => false,
                'constraints' => [
                    new Assert\Choice([
                        'choices' => ['Ouvert', 'En cours', 'Fermé'],
                        'message' => 'Please select a valid status.'
                    ])
                ]
            ])
            ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Emploi::class,
        ]);
    }
}