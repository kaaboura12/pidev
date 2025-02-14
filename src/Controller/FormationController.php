<?php

namespace App\Controller;

use App\Entity\Formation;
use App\Entity\Categorie;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\MoneyType;
use Symfony\Component\Form\Extension\Core\Type\UrlType;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;

final class FormationController extends AbstractController
{
    #[Route('/backformation', name: 'back_showformation')]
    public function show(EntityManagerInterface $entityManager): Response
    {
        $formations = $entityManager->getRepository(Formation::class)->findAll();
        
        return $this->render('backOffice/formation/formation.html.twig', [
            'formations' => $formations,
        ]);
    }

    #[Route('/new', name: 'back_newformation')]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $formation = new Formation();
        $formation->setDateCreation(new \DateTime());

        $form = $this->createFormBuilder($formation)
            ->add('titre', TextType::class, [
                'label' => 'Titre de la formation',
                'attr' => [
                    'class' => 'form-control form-control-lg',
                    'placeholder' => 'Entrez le titre de la formation'
                ]
            ])
            ->add('description', TextareaType::class, [
                'label' => 'Description',
                'required' => false,
                'attr' => [
                    'class' => 'form-control form-control-lg',
                    'rows' => 4,
                    'placeholder' => 'Décrivez le contenu de la formation...'
                ]
            ])
            ->add('date_debut', DateType::class, [
                'label' => 'Date de début',
                'widget' => 'single_text',
                'attr' => ['class' => 'form-control form-control-lg']
            ])
            ->add('date_fin', DateType::class, [
                'label' => 'Date de fin',
                'widget' => 'single_text',
                'attr' => ['class' => 'form-control form-control-lg']
            ])
            ->add('nbrpart', NumberType::class, [
                'label' => 'Nombre de participants',
                'attr' => [
                    'class' => 'form-control form-control-lg',
                    'min' => 1
                ]
            ])
            ->add('prix', MoneyType::class, [
                'label' => 'Prix',
                'currency' => 'TND',
                'attr' => ['class' => 'form-control form-control-lg']
            ])
            ->add('video', UrlType::class, [
                'label' => 'Lien vidéo',
                'required' => false,
                'attr' => [
                    'class' => 'form-control form-control-lg',
                    'placeholder' => 'URL de la vidéo (YouTube, Vimeo, etc.)'
                ]
            ])
            ->add('categorie', EntityType::class, [
                'class' => Categorie::class,
                'choice_label' => 'nom',
                'label' => 'Catégorie',
                'attr' => ['class' => 'form-control form-control-lg']
            ])
            ->add('save', SubmitType::class, [
                'label' => 'Créer la formation',
                'attr' => ['class' => 'btn btn-gradient-primary btn-lg font-weight-medium']
            ])
            ->getForm();

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($formation);
            $entityManager->flush();

            $this->addFlash('success', 'La formation a été créée avec succès!');
            return $this->redirectToRoute('back_showformation');
        }

        return $this->render('backOffice/formation/new.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    #[Route('/formation/edit/{id}', name: 'back_editformation')]
    public function edit(Request $request, EntityManagerInterface $entityManager, Formation $formation): Response
    {
        $form = $this->createFormBuilder($formation)
            ->add('titre', TextType::class, [
                'label' => 'Titre de la formation',
                'attr' => [
                    'class' => 'form-control form-control-lg',
                    'placeholder' => 'Entrez le titre de la formation'
                ]
            ])
            ->add('description', TextareaType::class, [
                'label' => 'Description',
                'required' => false,
                'attr' => [
                    'class' => 'form-control form-control-lg',
                    'rows' => 4,
                    'placeholder' => 'Décrivez le contenu de la formation...'
                ]
            ])
            ->add('date_debut', DateType::class, [
                'label' => 'Date de début',
                'widget' => 'single_text',
                'attr' => ['class' => 'form-control form-control-lg']
            ])
            ->add('date_fin', DateType::class, [
                'label' => 'Date de fin',
                'widget' => 'single_text',
                'attr' => ['class' => 'form-control form-control-lg']
            ])
            ->add('nbrpart', NumberType::class, [
                'label' => 'Nombre de participants',
                'attr' => [
                    'class' => 'form-control form-control-lg',
                    'min' => 1
                ]
            ])
            ->add('prix', MoneyType::class, [
                'label' => 'Prix',
                'currency' => 'TND',
                'attr' => ['class' => 'form-control form-control-lg']
            ])
            ->add('video', UrlType::class, [
                'label' => 'Lien vidéo',
                'required' => false,
                'attr' => [
                    'class' => 'form-control form-control-lg',
                    'placeholder' => 'URL de la vidéo (YouTube, Vimeo, etc.)'
                ]
            ])
            ->add('categorie', EntityType::class, [
                'class' => Categorie::class,
                'choice_label' => 'nom',
                'label' => 'Catégorie',
                'attr' => ['class' => 'form-control form-control-lg']
            ])
            ->add('save', SubmitType::class, [
                'label' => 'Mettre à jour la formation',
                'attr' => ['class' => 'btn btn-gradient-primary btn-lg font-weight-medium']
            ])
            ->getForm();

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            $this->addFlash('success', 'La formation a été mise à jour avec succès!');
            return $this->redirectToRoute('back_showformation');
        }

        return $this->render('backOffice/formation/edit.html.twig', [
            'form' => $form->createView(),
            'formation' => $formation
        ]);
    }

    #[Route('/formation/delete/{id}', name: 'back_deleteformation')]
    public function delete(EntityManagerInterface $entityManager, Formation $formation): Response
    {
        // Delete the formation
        $entityManager->remove($formation);
        $entityManager->flush();

        $this->addFlash('success', 'La formation a été supprimée avec succès!');
        return $this->redirectToRoute('back_showformation');
    }

    #[Route('/formation', name: 'front_formation')]
    public function frontShow(EntityManagerInterface $entityManager): Response
    {
        $formations = $entityManager->getRepository(Formation::class)->findAll();
        
        return $this->render('frontOffice/formation/formation.html.twig', [
            'formations' => $formations,
        ]);
    }
}
