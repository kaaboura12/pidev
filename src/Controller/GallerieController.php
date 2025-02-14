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

final class GallerieController extends AbstractController
{
    #[Route('/gallerie', name: 'app_gallerie')]
    public function index(ArticleRepository $ar): Response
    {
        $liste=$ar->findAll();
        return $this->render('frontOffice/gallerie/GallerieExplorer.html.twig', [
            'controller_name' => 'GallerieController',
            'liste' => $liste,
        ]);
    }
    #[Route('/Mygallerie', name: 'app_gallerie2')]
    public function Mygallerie(): Response
    {
        return $this->render('frontOffice/gallerie/GallerieMy.html.twig', [
            'controller_name' => 'GallerieController',
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
