<?php

namespace App\Controller;

use App\Entity\Emploi;
use App\Form\EmploiType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;
use App\Entity\User;

class EmploiController extends AbstractController
{
    private EntityManagerInterface $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    #[Route('/emploi', name: 'app_emploi')]
    public function index(): Response
    {
        $emplois = $this->entityManager->getRepository(Emploi::class)->findAll();

        return $this->render('frontOffice/emploi/index.html.twig', [
            'emplois' => $emplois,
        ]);
    }
// src/Controller/EmploiController.php
#[Route('/user/{user_id}/emplois', name: 'user_emplois')]
    public function userEmplois(int $user_id): Response
    {
        // Fetch the user by user_id
        $user = $this->entityManager->getRepository(User::class)->find($user_id);

        if (!$user) {
            throw $this->createNotFoundException('Utilisateur non trouvé pour l\'ID ' . $user_id);
        }

        // Fetch jobs posted by the user
        $emplois = $this->entityManager->getRepository(Emploi::class)->findBy(['user' => $user]);

        // Get the currently logged-in user
        $currentUser = $this->getUser();

        // Ensure the user is logged in and is an instance of User
        

        // Render the template with the user's jobs and the current user's ID
        return $this->render('frontOffice/emploi/user_emplois.html.twig', [
            'emplois' => $emplois,
            'user' => $user,
        ]);
    }
    #[Route('/emploi/add', name: 'emploi_add')]
public function add(Request $request): Response
{
    $emploi = new Emploi();
    $form = $this->createForm(EmploiType::class, $emploi);
    $form->handleRequest($request);

    if ($form->isSubmitted() && $form->isValid()) {
        // La date de publication est définie automatiquement
        $emploi->setDatePublication(new \DateTime());

        // Persister et enregistrer l'entité
        $this->entityManager->persist($emploi);
        $this->entityManager->flush();

        $this->addFlash('success', 'Offre d\'emploi ajoutée avec succès !');
        return $this->redirectToRoute('app_emploi');
    }

    return $this->render('frontOffice/emploi/add.html.twig', [
        'form' => $form->createView(),
    ]);
}
    #[Route('/emploii/{id}', name: 'emploi_show2')]
    public function show2(int $id): Response
    {
        $emploi = $this->entityManager->getRepository(Emploi::class)->find($id);

        if (!$emploi) {
            throw $this->createNotFoundException('Aucune offre trouvée pour l\'id ' . $id);
        }

        return $this->render('frontOffice/emploi/show2.html.twig', [
            'emploi' => $emploi,
        ]);
    }
    #[Route('/emploi/{id}', name: 'emploi_show')]
    public function show(int $id): Response
    {
        $emploi = $this->entityManager->getRepository(Emploi::class)->find($id);

        if (!$emploi) {
            throw $this->createNotFoundException('Aucune offre trouvée pour l\'id ' . $id);
        }

        return $this->render('frontOffice/emploi/show.html.twig', [
            'emploi' => $emploi,
        ]);
    }

    #[Route('/emploi/edit/{id}', name: 'emploi_edit', methods: ['GET', 'POST'])]
    public function edit(int $id, Request $request): Response
    {
        $emploi = $this->entityManager->getRepository(Emploi::class)->find($id);

        if (!$emploi) {
            throw $this->createNotFoundException('Aucune offre trouvée pour l\'id ' . $id);
        }

        $user = $this->getUser();
        if ($emploi->getUser() !== $user) {
            throw new AccessDeniedException('Vous ne pouvez modifier que vos propres offres d\'emploi.');
        }

        $form = $this->createForm(EmploiType::class, $emploi);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->entityManager->flush();

            $this->addFlash('success', 'Offre d\'emploi mise à jour avec succès !');
            return $this->redirectToRoute('app_emploi');
        }

        return $this->render('frontOffice/emploi/edit.html.twig', [
            'form' => $form->createView(),
            'emploi' => $emploi,
        ]);
    }

    #[Route('/emploi/delete/{id}', name: 'emploi_delete', methods: ['POST'])]
    public function delete(int $id, Request $request): Response
    {
        $emploi = $this->entityManager->getRepository(Emploi::class)->find($id);

        if (!$emploi) {
            throw $this->createNotFoundException('Aucune offre trouvée pour l\'id ' . $id);
        }

        $user = $this->getUser();
        if ($emploi->getUser() !== $user) {
            throw new AccessDeniedException('Vous ne pouvez supprimer que vos propres offres d\'emploi.');
        }

        if ($this->isCsrfTokenValid('delete' . $emploi->getId(), $request->request->get('_token'))) {
            $this->entityManager->remove($emploi);
            $this->entityManager->flush();

            $this->addFlash('success', 'Offre d\'emploi supprimée avec succès !');
        } else {
            $this->addFlash('error', 'Token CSRF invalide.');
        }

        return $this->redirectToRoute('app_emploi');
    }

#[Route('/mes-emplois', name: 'mes_emplois')]
public function mesEmplois(): Response
{
    // Get the currently logged-in user
    $user = $this->getUser();

    // Ensure the user is logged in
    if (!$user instanceof User) {
        throw new AccessDeniedException('Vous devez être connecté pour accéder à cette page.');
    }

    // Fetch jobs posted by the logged-in user
    $emplois = $this->entityManager->getRepository(Emploi::class)->findBy(['user' => $user]);

    // Render the template with the user's jobs
    return $this->render('emploi/mes_emplois.html.twig', [
        'emplois' => $emplois,
    ]);
}


//backoffice




#[Route('/backoffice/emploi', name: 'backoffice_emploi')]
public function backofficeIndex(): Response
{
    $emplois = $this->entityManager->getRepository(Emploi::class)->findAll();

    return $this->render('backOffice/emploi/index.html.twig', [
        'emplois' => $emplois,
    ]);
}

#[Route('/backoffice/emploi/edit/{id}', name: 'backoffice_emploi_edit', methods: ['GET', 'POST'])]
public function edit_back(int $id, Request $request): Response
{
    $emploi = $this->entityManager->getRepository(Emploi::class)->find($id);

    if (!$emploi) {
        throw $this->createNotFoundException('Aucune offre trouvée pour l\'id ' . $id);
    }

    $form = $this->createForm(EmploiType::class, $emploi);
    $form->handleRequest($request);

    if ($form->isSubmitted() && $form->isValid()) {
        $this->entityManager->flush();

        $this->addFlash('success', 'Offre d\'emploi mise à jour avec succès !');
        return $this->redirectToRoute('backoffice_emploi');
    }

    return $this->render('backOffice/emploi/edit.html.twig', [
        'form' => $form->createView(),
        'emploi' => $emploi,
    ]);
}

#[Route('/backoffice/emploi/delete/{id}', name: 'backoffice_emploi_delete', methods: ['POST'])]
public function delete_back(int $id): Response
{
    $emploi = $this->entityManager->getRepository(Emploi::class)->find($id);

    if (!$emploi) {
        $this->addFlash('error', 'Aucune offre trouvée pour l\'id ' . $id);
        return $this->redirectToRoute('backoffice_emploi');
    }

    // Delete the entity
    $this->entityManager->remove($emploi);
    $this->entityManager->flush();

    $this->addFlash('success', 'Offre d\'emploi supprimée avec succès !');
    return $this->redirectToRoute('backoffice_emploi');
}
// src/Controller/EmploiController.php

#[Route('/backoffice/emploi/add', name: 'backoffice_emploi_add')]
public function addBack(Request $request): Response
{
    $emploi = new Emploi();
    $form = $this->createForm(EmploiType::class, $emploi);
    $form->handleRequest($request);

    if ($form->isSubmitted() && $form->isValid()) {
        $user = $this->getUser(); 
        $emploi->setUser($user);
        $emploi->setDatePublication(new \DateTime());

        $this->entityManager->persist($emploi);
        $this->entityManager->flush();

        $this->addFlash('success', 'Offre d\'emploi ajoutée avec succès !');
        return $this->redirectToRoute('backoffice_emploi');
    }

    return $this->render('backOffice/emploi/add.html.twig', [
        'form' => $form->createView(),
    ]);
}
#[Route('/backoffice/emploi/show/{id}', name: 'backoffice_emploi_show', methods: ['GET'])]
public function show_back(int $id): Response
{
    $emploi = $this->entityManager->getRepository(Emploi::class)->find($id);

    if (!$emploi) {
        throw $this->createNotFoundException('Aucune offre trouvée pour l\'id ' . $id);
    }

    return $this->render('backOffice/emploi/show.html.twig', [
        'emploi' => $emploi,
    ]);
}

}