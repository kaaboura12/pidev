<?php

namespace App\Form;

use App\Entity\Article;
use App\Entity\Galerie;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ArticleType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('titre')
            ->add('description')
            ->add('prix')
            ->add('date_pub', null, [
                'widget' => 'single_text',
            ])
            ->add('disponible')
            ->add('nbrarticle')
            ->add('nbrlikes')
            ->add('contenu', FileType::class, [
                'label' => 'Image (Contenu)',
                'mapped' => false, // Cette donnée ne sera pas liée directement à la propriété 'contenu' de l'entité
                'required' => false,
                'attr' => ['class' => 'form-control']
            ])
            ->add('categorie', ChoiceType::class, [
                'choices' => [
                    'Peinture' => 'Peinture',
                    'Sculpture' => 'Sculpture',
                    'Photographie' => 'Photographie',
                    'DessinEtIllustration' => 'DessinEtIllustration',
                    'ArtNumerique' => 'ArtNumerique',
                    'Architecture' => 'Architecture',
                ],
                'placeholder' => 'Choisissez une catégorie',
                'attr' => ['class' => 'form-control'],
            ])
            ->add('galerie', EntityType::class, [
                'class' => Galerie::class,
                'choice_label' => 'id',
                'attr' => ['class' => 'form-control'],
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Article::class,
        ]);
    }
}
