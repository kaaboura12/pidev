<?php

namespace App\Controller;

use App\Entity\Categorie;
use App\Entity\Formation;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;

final class CategorieController extends AbstractController
{
    #[Route('/backcategorie', name: 'back_showcategorie')]
    public function show(Request $request, EntityManagerInterface $entityManager): Response
    {
        $categories = $entityManager->getRepository(Categorie::class)->findAll();
        
        // Create new category instance
        $categorie = new Categorie();
        $categorie->setDateCreation(new \DateTime());
        
        // Create form
        $form = $this->createFormBuilder($categorie)
            ->add('nom', TextType::class, [
                'label' => 'Nom de la catégorie',
                'attr' => [
                    'class' => 'form-control form-control-lg',
                    'placeholder' => 'Entrez le nom de la catégorie'
                ]
            ])
            ->add('description', TextareaType::class, [
                'label' => 'Description de la catégorie',
                'required' => false,
                'attr' => [
                    'class' => 'form-control form-control-lg',
                    'rows' => 4,
                    'placeholder' => 'Décrivez brièvement cette catégorie...'
                ]
            ])
            ->add('date_creation', DateType::class, [
                'label' => 'Date de création',
                'widget' => 'single_text',
                'attr' => [
                    'class' => 'form-control form-control-lg',
                    'readonly' => true,
                    'style' => 'background-color: #f8f9fa;'
                ],
                'data' => new \DateTime()
            ])
            ->add('save', SubmitType::class, [
                'label' => 'Ajouter la catégorie',
                'attr' => [
                    'class' => 'btn btn-gradient-primary btn-lg font-weight-medium'
                ]
            ])
            ->getForm();

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($categorie);
            $entityManager->flush();

            $this->addFlash('success', 'La catégorie a été ajoutée avec succès!');
            return $this->redirectToRoute('back_showcategorie');
        }
        
        return $this->render('backOffice/formation/categorie.html.twig', [
            'categories' => $categories,
            'form' => $form->createView(),
        ]);
    }

    #[Route('/backcategorie/edit/{id}', name: 'back_editcategorie')]
    public function edit(Request $request, EntityManagerInterface $entityManager, int $id): Response
    {
        $categorie = $entityManager->getRepository(Categorie::class)->find($id);
        
        if (!$categorie) {
            throw $this->createNotFoundException('Catégorie non trouvée');
        }
        
        $form = $this->createFormBuilder($categorie)
            ->add('nom', TextType::class, [
                'label' => 'Nom de la catégorie',
                'attr' => [
                    'class' => 'form-control form-control-lg',
                    'placeholder' => 'Entrez le nom de la catégorie'
                ]
            ])
            ->add('description', TextareaType::class, [
                'label' => 'Description de la catégorie',
                'required' => false,
                'attr' => [
                    'class' => 'form-control form-control-lg',
                    'rows' => 4,
                    'placeholder' => 'Décrivez brièvement cette catégorie...'
                ]
            ])
            ->add('date_creation', DateType::class, [
                'label' => 'Date de création',
                'widget' => 'single_text',
                'attr' => [
                    'class' => 'form-control form-control-lg'
                ]
            ])
            ->add('save', SubmitType::class, [
                'label' => 'Enregistrer les modifications',
                'attr' => [
                    'class' => 'btn btn-gradient-primary btn-lg font-weight-medium'
                ]
            ])
            ->getForm();

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();
            
            $this->addFlash('success', 'La catégorie a été modifiée avec succès!');
            return $this->redirectToRoute('back_showcategorie');
        }
        
        return $this->render('backOffice/formation/edit_categorie.html.twig', [
            'form' => $form->createView(),
            'categorie' => $categorie
        ]);
    }

    #[Route('/backcategorie/delete/{id}', name: 'back_deletecategorie')]
    public function delete(EntityManagerInterface $entityManager, Categorie $categorie): Response
    {
        // Check if category has related formations
        $formationsCount = $entityManager->getRepository(Formation::class)
            ->count(['categorie' => $categorie]);
            
        if ($formationsCount > 0) {
            $this->addFlash('error', 'Impossible de supprimer cette catégorie car elle contient des formations.');
            return $this->redirectToRoute('back_showcategorie');
        }

        $entityManager->remove($categorie);
        $entityManager->flush();

        $this->addFlash('success', 'La catégorie a été supprimée avec succès!');
        return $this->redirectToRoute('back_showcategorie');
    }
}
