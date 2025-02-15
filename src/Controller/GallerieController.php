<?php

namespace App\Controller;

use App\Entity\Article;
use App\Entity\Galerie;
use App\Form\GalerieType;
use App\Repository\ArticleRepository;
use App\Repository\GalerieRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use App\Repository\UserRepository;
use App\Entity\User;
use App\Form\GalerieFrontType;
use App\Form\ArticleFrontType;
use Symfony\Component\HttpFoundation\File\Exception\FileException;

final class GallerieController extends AbstractController
{
    #[Route('/gallerie{page?1}', name: 'app_gallerie')]
    public function index(ArticleRepository $articleRepository, $page = 1): Response
    {
        $limit = 5; // nombre d'articles par page
        $offset = ($page - 1) * $limit;
        
        // Récupérer le nombre total d'articles
        $total = $articleRepository->count([]);
        
        // Calculer le nombre total de pages
        $pages = ceil($total / $limit);
        
        // Récupérer les articles pour la page courante
        $articles = $articleRepository->findBy([], ['date_pub' => 'DESC'], $limit, $offset);
        
        return $this->render('frontOffice/gallerie/GallerieExplorer.html.twig', [
            'liste' => $articles,
            'page' => $page,
            'pages' => $pages
        ]);
    }

    #[Route('/gallerie/{cat}/{page?1}', name: 'cat_article')]
    public function showByCategory(ArticleRepository $articleRepository, $cat, $page = 1): Response
    {
        $limit = 6;
        $offset = ($page - 1) * $limit;
        
        // Récupérer le nombre total d'articles de cette catégorie
        $total = $articleRepository->count(['categorie' => $cat]);
        
        // Calculer le nombre de pages
        $pages = ceil($total / $limit);
        
        // Récupérer les articles de la catégorie pour la page courante
        $articles = $articleRepository->findBy(
            ['categorie' => $cat],
            ['date_pub' => 'DESC'],
            $limit,
            $offset
        );
        
        return $this->render('frontOffice/gallerie/GallerieExplorer.html.twig', [
            'liste' => $articles,
            'page' => $page,
            'pages' => $pages
        ]);
    }

    #[Route('/Mygallerie/{id}', name: 'ma_galerie', requirements: ['id' => '\d+'])]
    public function Mygallerie(int $id, Request $request, GalerieRepository $gr, UserRepository $ur, EntityManagerInterface $em): Response
    {
        // Vérifier si l'utilisateur existe
        $user = $ur->find($id);
        if (!$user) {
            throw $this->createNotFoundException("Utilisateur non trouvé.");
        }

        // Vérifier si l'utilisateur connecté accède à sa propre galerie
        if ($this->getUser() !== $user) {
            throw $this->createAccessDeniedException('Vous ne pouvez pas accéder à cette galerie.');
        }

        // Trouver la galerie associée à cet utilisateur
        $galerie = $gr->findOneBy(['user' => $user]);
        
        // Créer le formulaire pour le modal de création
        $newGalerie = new Galerie();
        $newGalerie->setUser($this->getUser());
        $newGalerie->setDatecreation(new \DateTime());
        $createForm = $this->createForm(GalerieFrontType::class, $newGalerie);
        
        // Créer le formulaire pour le modal de modification si une galerie existe
        $editForm = null;
        if ($galerie) {
            $editForm = $this->createForm(GalerieFrontType::class, $galerie);
            $editForm->handleRequest($request);
            
            if ($editForm->isSubmitted() && $editForm->isValid()) {
                $em->flush();
                $this->addFlash('success', 'Votre galerie a été modifiée avec succès !');
                return $this->redirectToRoute('ma_galerie', ['id' => $id]);
            }
        }
        
        // Gérer le formulaire de création
        $createForm->handleRequest($request);
        if ($createForm->isSubmitted() && $createForm->isValid()) {
            $em->persist($newGalerie);
            $em->flush();
            $this->addFlash('success', 'Votre galerie a été créée avec succès !');
            return $this->redirectToRoute('ma_galerie', ['id' => $id]);
        }
        
        return $this->render('frontOffice/gallerie/GallerieMy.html.twig', [
            'galerie' => $galerie,
            'form' => $createForm->createView(),
            'editForm' => $editForm ? $editForm->createView() : null
        ]);
    }
    
    #[Route('/create-galerie', name: 'create_galerie_front')]
    public function createGalerieFront(Request $request, EntityManagerInterface $em): Response
    {
        $galerie = new Galerie();
        $galerie->setUser($this->getUser());
        $galerie->setDatecreation(new \DateTime());
        
        $form = $this->createForm(GalerieType::class, $galerie);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em->persist($galerie);
            $em->flush();
            $this->addFlash('success', 'Votre galerie a été créée avec succès !');
            return $this->redirectToRoute('ma_galerie', ['id' => $this->getUser()->getId()]);
        }

        return $this->render('frontOffice/gallerie/GalerieCreate.html.twig', [
            'form' => $form->createView()
        ]);
    }

    #[Route('/edit-galerie/{id}', name: 'edit_galerie_front')]
    public function editGalerieFront(int $id, Request $request, GalerieRepository $gr, EntityManagerInterface $em): Response
    {
        $galerie = $gr->find($id);
        
        if (!$galerie) {
            throw $this->createNotFoundException('Galerie non trouvée');
        }
        
        if ($galerie->getUser() !== $this->getUser()) {
            throw $this->createAccessDeniedException('Vous n\'êtes pas autorisé à modifier cette galerie');
        }

        $form = $this->createForm(GalerieType::class, $galerie);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em->flush();
            $this->addFlash('success', 'Votre galerie a été modifiée avec succès !');
            return $this->redirectToRoute('ma_galerie', ['id' => $this->getUser()->getId()]);
        }

        return $this->render('frontOffice/gallerie/GalerieEdit.html.twig', [
            'form' => $form->createView(),
            'galerie' => $galerie
        ]);
    }

    #[Route('/delete-galerie/{id}', name: 'delete_galerie_front')]
    public function deleteGalerieFront(int $id, GalerieRepository $gr, EntityManagerInterface $em): Response
    {
        $galerie = $gr->find($id);
        
        if (!$galerie) {
            throw $this->createNotFoundException('Galerie non trouvée');
        }
        
        // Vérifier si l'utilisateur est connecté et est propriétaire de la galerie
        if (!$this->getUser() || $galerie->getUser() !== $this->getUser()) {
            throw $this->createAccessDeniedException('Vous n\'êtes pas autorisé à supprimer cette galerie');
        }
        
        // Récupérer l'ID de l'utilisateur avant de supprimer la galerie
        $userId = $this->getUser()->getId();
        
        try {
            $em->remove($galerie);
            $em->flush();
            $this->addFlash('success', 'Votre galerie a été supprimée avec succès');
        } catch (\Exception $e) {
            $this->addFlash('error', 'Une erreur est survenue lors de la suppression de la galerie');
            return $this->redirectToRoute('ma_galerie', ['id' => $userId]);
        }
        
        return $this->redirectToRoute('ma_galerie', ['id' => $userId]);
    }

    #[Route('/galerie/{id}/articles', name: 'articles_galerie')]
    public function articlesGalerie(int $id, GalerieRepository $gr, ArticleRepository $ar): Response
    {
        $galerie = $gr->find($id);
        
        if (!$galerie) {
            throw $this->createNotFoundException('Galerie non trouvée');
        }
        
        // Récupérer les articles de la galerie
        $articles = $ar->findBy(['galerie' => $galerie], ['date_pub' => 'DESC']);
        
        return $this->render('frontOffice/gallerie/GalerieArticles.html.twig', [
            'galerie' => $galerie,
            'articles' => $articles
        ]);
    }

    #[Route('/article/delete/{id}', name: 'front_delete_article')]
    public function deleteArticleFront(int $id, ArticleRepository $ar, EntityManagerInterface $em): Response
    {
        $article = $ar->find($id);
        
        if (!$article) {
            throw $this->createNotFoundException('Article non trouvé');
        }
        
        // Vérifier si l'utilisateur est propriétaire de la galerie
        if (!$this->getUser() || $article->getGalerie()->getUser() !== $this->getUser()) {
            throw $this->createAccessDeniedException('Vous n\'êtes pas autorisé à supprimer cet article');
        }
        
        // Récupérer l'ID de la galerie avant de supprimer l'article
        $galerieId = $article->getGalerie()->getId();
        
        try {
            $em->remove($article);
            $em->flush();
            $this->addFlash('success', 'Article supprimé avec succès');
        } catch (\Exception $e) {
            $this->addFlash('error', 'Une erreur est survenue lors de la suppression de l\'article');
        }
        
        return $this->redirectToRoute('articles_galerie', ['id' => $galerieId]);
    }
    //edit article front
    #[Route('/article/edit/{id}', name: 'edit_article_front')]
    public function editArticleFront(Request $request, Article $article, EntityManagerInterface $em): Response
    {
        // Vérifier si l'utilisateur est autorisé à modifier cet article
        if (!$this->getUser() || $article->getGalerie()->getUser() !== $this->getUser()) {
            throw $this->createAccessDeniedException('Vous n\'êtes pas autorisé à modifier cet article');
        }

        $form = $this->createForm(ArticleFrontType::class, $article);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // Gérer l'upload de la nouvelle image si elle existe
            $imageFile = $form->get('contenu')->getData();
            if ($imageFile) {
                $newFilename = uniqid().'.'.$imageFile->guessExtension();
                try {
                    $imageFile->move(
                        $this->getParameter('images_directory'),
                        $newFilename
                    );
                    // Supprimer l'ancienne image si elle existe
                    if ($article->getContenu()) {
                        $oldFilePath = $this->getParameter('images_directory').'/'.$article->getContenu();
                        if (file_exists($oldFilePath)) {
                            unlink($oldFilePath);
                        }
                    }
                    $article->setContenu($newFilename);
                } catch (FileException $e) {
                    $this->addFlash('error', 'Une erreur est survenue lors de l\'upload de l\'image');
                }
            }

            $em->flush();
            $this->addFlash('success', 'Article modifié avec succès');
            return $this->redirectToRoute('articles_galerie', ['id' => $article->getGalerie()->getId()]);
        }

        return $this->render('frontOffice/gallerie/EditArticle.html.twig', [
            'form' => $form->createView(),
            'article' => $article
        ]);
    }

    //add article front
    #[Route('/article/add/{galerieId}', name: 'add_article_front')]
    public function addArticleFront(Request $request, EntityManagerInterface $em, int $galerieId): Response
    {
        // Récupérer la galerie
        $galerie = $em->getRepository(Galerie::class)->find($galerieId);
        
        // Vérifier si la galerie existe et si l'utilisateur est autorisé
        if (!$galerie) {
            throw $this->createNotFoundException('Galerie non trouvée');
        }
        
        if (!$this->getUser() || $galerie->getUser() !== $this->getUser()) {
            throw $this->createAccessDeniedException('Vous n\'êtes pas autorisé à ajouter un article dans cette galerie');
        }

        $article = new Article();
        $article->setGalerie($galerie);
        $article->setDatePub(new \DateTime());
        $article->setNbrlikes(0);
        $article->setDisponible(true);

        $form = $this->createForm(ArticleFrontType::class, $article);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // Gérer l'upload de l'image
            $imageFile = $form->get('contenu')->getData();
            if ($imageFile) {
                $newFilename = uniqid().'.'.$imageFile->guessExtension();
                try {
                    $imageFile->move(
                        $this->getParameter('images_directory'),
                        $newFilename
                    );
                    $article->setContenu($newFilename);
                } catch (FileException $e) {
                    $this->addFlash('error', 'Une erreur est survenue lors de l\'upload de l\'image');
                }
            }

            $em->persist($article);
            $em->flush();
            
            $this->addFlash('success', 'Article ajouté avec succès');
            return $this->redirectToRoute('articles_galerie', ['id' => $galerieId]);
        }

        return $this->render('frontOffice/gallerie/AddArticle.html.twig', [
            'form' => $form->createView(),
            'galerie' => $galerie
        ]);
    }

    //BACK OFFICE **************************************************************************************************************
    //AFFICHAGE Gallerie
    #[Route('/backShowgallerie', name: 'back_showgallerie')]
    public function ShowGallerie(GalerieRepository $gr): Response
    {
        $list=$gr->findAll();
        return $this->render ('backOffice/gallerie/GallerieShow.html.twig', 
        [
           'liste' => $list,
        ]);
    }
    //AJOUT Gallerie
    #[Route('/backAddgallerie', name: 'ajouter_galerie')]
    public function ajouterGallerie(Request $request,EntityManagerInterface $em)
    {
        $galerie = new Galerie();
        $form = $this->createForm(GalerieType::class, $galerie);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $em->persist($galerie);
            $em->flush();
            return $this->redirectToRoute('back_showgallerie');
        }
        return $this->render('backOffice/gallerie/GallerieAdd.html.twig', [
            'form' => $form,
            'titre'=>"Ajouter",
        ]);
    }
    //MODIFIER gallerie
    #[Route('/backEditgallerie{id}', name: 'edit_galerie')]
    public function modifierGallerie($id,Request $request,EntityManagerInterface $em,GalerieRepository $gr)
    {
        $galerie=$gr->find($id);
        $form = $this->createForm(GalerieType::class, $galerie);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $em->persist($galerie);
            $em->flush();
            return $this->redirectToRoute('back_showgallerie');
        }
        return $this->render('backOffice/gallerie/GallerieAdd.html.twig', [
            'form' => $form,
            'titre'=>"Modifier",
        ]);
    }
    //DELETE gallerie
    #[Route('/backDelgallerie{id}',name:'delete_galerie')]
    public function DeleteGallerie($id,GalerieRepository $gr,EntityManagerInterface $em)
    {
        $galerie=$gr->find($id);
        $em->remove($galerie);
        $em->flush();
        return $this->redirectToRoute('back_showgallerie');
    }
}
