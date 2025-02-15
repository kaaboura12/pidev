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

final class GallerieController extends AbstractController
{
    #[Route('/gallerie{page?1}', name: 'app_gallerie')]
    public function index(ArticleRepository $articleRepository, $page = 1): Response
    {
        $limit = 6; // nombre d'articles par page
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
    public function Mygallerie(int $id, GalerieRepository $gr, UserRepository $ur): Response
    {
        // Vérifier si l'utilisateur existe
        $user = $ur->find($id);
        if (!$user) {
            throw $this->createNotFoundException("Utilisateur non trouvé.");
        }
    
        // Trouver la galerie associée à cet utilisateur
        $galerie = $gr->findOneBy(['user' => $user]);
        
        return $this->render('frontOffice/gallerie/GallerieMy.html.twig', [
            'galerie' => $galerie,
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
