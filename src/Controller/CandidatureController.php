<?php

namespace App\Controller;

use App\Entity\Candidature;
use App\Form\CandidatureType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\ResponseHeaderBag;
use Symfony\Component\Routing\Annotation\Route;
use App\Entity\Emploi;
use App\Entity\CandidatureOffre;

class CandidatureController extends AbstractController
{
    private EntityManagerInterface $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    #[Route('/candidature/postuler/{emploiId}', name: 'candidature_postuler')]
    public function postuler(Request $request, int $emploiId): Response
    {
        $emploi = $this->entityManager->getRepository(Emploi::class)->find($emploiId);
    
        if (!$emploi) {
            throw $this->createNotFoundException('Emploi non trouvé.');
        }
    
        $candidature = new Candidature();
        $form = $this->createForm(CandidatureType::class, $candidature);
    
        $form->handleRequest($request);
    
        if ($form->isSubmitted() && $form->isValid()) {
            // Gestion du fichier CV
            $cvFile = $form->get('cv')->getData();
            if ($cvFile) {
                $uploadDirectory = $this->getParameter('cv_directory');
                if (!is_dir($uploadDirectory)) {
                    mkdir($uploadDirectory, 0777, true);
                }
    
                $newFilename = uniqid().'.'.$cvFile->guessExtension();
                $cvFile->move($uploadDirectory, $newFilename);
                $candidature->setCv($newFilename);
            }
    
            // Associer l'utilisateur connecté à la candidature
            $candidat = $this->getUser();
            if ($candidat) {
                $candidature->setCandidat($candidat);
            }
    
            // Persister la candidature
            $this->entityManager->persist($candidature);
    
            // Créer une nouvelle CandidatureOffre
            $candidatureOffre = new CandidatureOffre();
            $candidatureOffre->setCandidature($candidature);
            $candidatureOffre->setEmploi($emploi);
            $candidatureOffre->setStatut('En attente');
            $candidatureOffre->setDateAssociation(new \DateTime());
    
            // Persister la CandidatureOffre
            $this->entityManager->persist($candidatureOffre);
    
            // Enregistrer les modifications dans la base de données
            $this->entityManager->flush();
    
            $this->addFlash('success', 'Votre candidature a été soumise avec succès !');
            return $this->redirectToRoute('app_emploi');
        } elseif ($form->isSubmitted()) {
            $this->addFlash('danger', 'Erreur lors de la soumission de votre candidature. Veuillez vérifier les champs.');
        }
    
        return $this->render('frontOffice/candidature/postuler.html.twig', [
            'form' => $form->createView(),
            'emploi' => $emploi,
        ]);
    }


#[Route('/front/candidatures', name: 'front_candidatures')]
    public function listCandidatures1(): Response
    {
        $candidatures = $this->entityManager->getRepository(Candidature::class)->findAll();
        return $this->render('frontOffice/candidature/list.html.twig', [
            'candidatures' => $candidatures,
        ]);
    }


    #[Route('/front/candidature/delete/{id}', name: 'front_candidature_delete', methods: ['POST'])]
public function deleteCandidature2(int $id): Response
{
    $candidature = $this->entityManager->getRepository(Candidature::class)->find($id);

    if (!$candidature) {
        $this->addFlash('error', 'Candidature non trouvée.');
        return $this->redirectToRoute('front_candidatures');
    }

    // Supprimer les enregistrements liés dans CandidatureOffre
    $candidatureOffres = $this->entityManager->getRepository(CandidatureOffre::class)->findBy(['candidature' => $candidature]);
    foreach ($candidatureOffres as $candidatureOffre) {
        $this->entityManager->remove($candidatureOffre);
    }

    // Supprimer le fichier CV s'il existe
    $cvFilePath = $this->getParameter('cv_directory') . '/' . $candidature->getCv();
    if ($candidature->getCv() && file_exists($cvFilePath)) {
        unlink($cvFilePath);
    }

    // Supprimer la candidature
    $this->entityManager->remove($candidature);
    $this->entityManager->flush();

    $this->addFlash('success', 'Candidature supprimée avec succès.');
    return $this->redirectToRoute('front_candidatures');
}
    #[Route('/front/candidature/{id}', name: 'front_candidature_show', methods: ['GET'])]
    public function showCandidature1(int $id): Response
    {
        $candidature = $this->entityManager->getRepository(Candidature::class)->find($id);
    
        if (!$candidature) {
            $this->addFlash('error', 'Candidature non trouvée.');
            return $this->redirectToRoute('front_candidatures');
        }
    
        return $this->render('backOffice/candidature/show.html.twig', [
            'candidature' => $candidature,
        ]);
    }
    #[Route('/download-cv/{filename}', name: 'download_cv')]
public function downloadCv1(string $filename): Response
{
    $filePath = $this->getParameter('cv_directory') . '/' . $filename;

    if (!file_exists($filePath)) {
        throw $this->createNotFoundException('File not found.');
    }

    $response = new Response(file_get_contents($filePath));
    $disposition = $response->headers->makeDisposition(
        ResponseHeaderBag::DISPOSITION_ATTACHMENT,
        $filename
    );
    $response->headers->set('Content-Disposition', $disposition);

    $response->headers->set('Content-Type', mime_content_type($filePath));

    return $response;
}







    //back 


    #[Route('/backoffice/candidatures', name: 'backoffice_candidatures')]
    public function listCandidatures(): Response
    {
        $candidatures = $this->entityManager->getRepository(Candidature::class)->findAll();
        return $this->render('backOffice/candidature/list.html.twig', [
            'candidatures' => $candidatures,
        ]);
    }

    #[Route('/backoffice/candidature/delete/{id}', name: 'backoffice_candidature_delete', methods: ['POST'])]
    public function deleteCandidature(int $id): Response
    {
        $candidature = $this->entityManager->getRepository(Candidature::class)->find($id);

        if (!$candidature) {
            $this->addFlash('error', 'Candidature non trouvée.');
            return $this->redirectToRoute('backoffice_candidatures');
        }

        // Delete the CV file if it exists
        $cvFilePath = $this->getParameter('cv_directory') . '/' . $candidature->getCv();
        if ($candidature->getCv() && file_exists($cvFilePath)) {
            unlink($cvFilePath);
        }

        // Delete the candidature
        $this->entityManager->remove($candidature);
        $this->entityManager->flush();

        $this->addFlash('success', 'Candidature supprimée avec succès.');
        return $this->redirectToRoute('backoffice_candidatures');
    }
    #[Route('/backoffice/candidature/{id}', name: 'backoffice_candidature_show', methods: ['GET'])]
    public function showCandidature(int $id): Response
    {
        $candidature = $this->entityManager->getRepository(Candidature::class)->find($id);
    
        if (!$candidature) {
            $this->addFlash('error', 'Candidature non trouvée.');
            return $this->redirectToRoute('backoffice_candidatures');
        }
    
        return $this->render('backOffice/candidature/show.html.twig', [
            'candidature' => $candidature,
        ]);
    }
    #[Route('/download-cv/{filename}', name: 'download_cv')]
public function downloadCv(string $filename): Response
{
    $filePath = $this->getParameter('cv_directory') . '/' . $filename;

    if (!file_exists($filePath)) {
        throw $this->createNotFoundException('File not found.');
    }

    $response = new Response(file_get_contents($filePath));
    $disposition = $response->headers->makeDisposition(
        ResponseHeaderBag::DISPOSITION_ATTACHMENT,
        $filename
    );
    $response->headers->set('Content-Disposition', $disposition);

    $response->headers->set('Content-Type', mime_content_type($filePath));

    return $response;
}
}