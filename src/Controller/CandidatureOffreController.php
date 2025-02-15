<?php

namespace App\Controller;

use App\Entity\CandidatureOffre;
use App\Entity\Candidature;
use App\Entity\Emploi;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class CandidatureOffreController extends AbstractController
{
    private EntityManagerInterface $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    #[Route('/backoffice/candidature-offre', name: 'backoffice_candidature_offre_list')]
    public function list(): Response
    {
        $candidatureOffres = $this->entityManager->getRepository(CandidatureOffre::class)->findAll();

        return $this->render('backOffice/candidature_offre/list.html.twig', [
            'candidatureOffres' => $candidatureOffres,
        ]);
    }

    #[Route('/backoffice/candidature-offre/{id}', name: 'backoffice_candidature_offre_show', methods: ['GET'])]
    public function show(int $id): Response
    {
        $candidatureOffre = $this->entityManager->getRepository(CandidatureOffre::class)->find($id);

        if (!$candidatureOffre) {
            throw $this->createNotFoundException('CandidatureOffre non trouvée.');
        }

        return $this->render('backOffice/candidature_offre/show.html.twig', [
            'candidatureOffre' => $candidatureOffre,
        ]);
    }

    #[Route('/backoffice/candidature-offre/{id}/update-status', name: 'backoffice_candidature_offre_update_status', methods: ['POST'])]
    public function updateStatus(int $id, Request $request): Response
    {
        $candidatureOffre = $this->entityManager->getRepository(CandidatureOffre::class)->find($id);

        if (!$candidatureOffre) {
            throw $this->createNotFoundException('CandidatureOffre non trouvée.');
        }

        $newStatus = $request->request->get('status');
        $candidatureOffre->setStatut($newStatus);

        $this->entityManager->flush();

        $this->addFlash('success', 'Statut de la candidature mis à jour avec succès.');
        return $this->redirectToRoute('backoffice_candidature_offre_show', ['id' => $id]);
    }
}