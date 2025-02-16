<?php

namespace App\Controller;

use App\Entity\Formation;
use App\Entity\Categorie;
use App\Entity\Inscription;
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
use App\Form\FormationType;

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

        $form = $this->createForm(FormationType::class, $formation);
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
        
        // Si l'utilisateur est connecté, on récupère ses inscriptions
        $userInscriptions = [];
        if ($this->getUser()) {
            $userInscriptions = $entityManager->getRepository(Inscription::class)->findBy([
                'user' => $this->getUser()
            ]);
        }
        
        return $this->render('frontOffice/formation/formation.html.twig', [
            'formations' => $formations,
            'userInscriptions' => $userInscriptions
        ]);
    }

    #[Route('/formation/{id}/inscription', name: 'formation_inscription', methods: ['GET'])]
    public function inscription(Request $request, Formation $formation, EntityManagerInterface $entityManager): Response
    {
        // Vérifier si l'utilisateur est connecté
        if (!$this->getUser()) {
            $this->addFlash('error', 'Vous devez être connecté pour vous inscrire à une formation');
            return $this->redirectToRoute('app_login');
        }

        // Vérifier le token CSRF
        $submittedToken = $request->query->get('_token');
        if (!$this->isCsrfTokenValid('inscription' . $formation->getId(), $submittedToken)) {
            $this->addFlash('error', 'Token invalide');
            return $this->redirectToRoute('front_formation');
        }

        // Vérifier si l'utilisateur n'est pas déjà inscrit
        $existingInscription = $entityManager->getRepository(Inscription::class)->findOneBy([
            'user' => $this->getUser(),
            'formation' => $formation
        ]);

        if ($existingInscription) {
            $this->addFlash('warning', 'Vous êtes déjà inscrit à cette formation');
            return $this->redirectToRoute('front_formation');
        }

        // Vérifier s'il reste des places
        if ($formation->getNbrpart() <= 0) {
            $this->addFlash('error', 'Désolé, il n\'y a plus de places disponibles pour cette formation');
            return $this->redirectToRoute('front_formation');
        }

        // Créer l'inscription
        $inscription = new Inscription();
        $inscription->setUser($this->getUser());
        $inscription->setFormation($formation);
        $inscription->setDateInscription(new \DateTime());
        $inscription->setDateCreation(new \DateTime());
        $inscription->setStatut('Pending');

        // Décrémenter le nombre de places disponibles
        $formation->setNbrpart($formation->getNbrpart() - 1);

        $entityManager->persist($inscription);
        $entityManager->flush();

        $this->addFlash('success', 'Votre inscription a été enregistrée avec succès !');
        return $this->redirectToRoute('front_formation');
    }
}
