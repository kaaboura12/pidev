<?php

namespace App\Entity;

use App\Repository\CandidatureOffreRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: CandidatureOffreRepository::class)]
class CandidatureOffre
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\ManyToOne(targetEntity: Candidature::class)]
    #[ORM\JoinColumn(name: "candidature_id", referencedColumnName: "id")]
    private ?Candidature $candidature = null;

    #[ORM\ManyToOne(targetEntity: Emploi::class)]
    #[ORM\JoinColumn(name: "emploi_id", referencedColumnName: "id")]
    private ?Emploi $emploi = null;

    #[ORM\Column(length: 20)]
    private string $statut = 'En attente';

    #[ORM\Column(type: Types::DATETIME_MUTABLE)]
    private ?\DateTimeInterface $date_association = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getCandidature(): ?Candidature
    {
        return $this->candidature;
    }

    public function setCandidature(?Candidature $candidature): static
    {
        $this->candidature = $candidature;
        return $this;
    }

    public function getEmploi(): ?Emploi
    {
        return $this->emploi;
    }

    public function setEmploi(?Emploi $emploi): static
    {
        $this->emploi = $emploi;
        return $this;
    }

    public function getStatut(): string
    {
        return $this->statut;
    }

    public function setStatut(string $statut): static
    {
        if (!in_array($statut, ['En attente', 'Sélectionnée', 'Rejetée'])) {
            throw new \InvalidArgumentException('Invalid status');
        }
        $this->statut = $statut;
        return $this;
    }

    public function getDateAssociation(): ?\DateTimeInterface
    {
        return $this->date_association;
    }

    public function setDateAssociation(\DateTimeInterface $date_association): static
    {
        $this->date_association = $date_association;
        return $this;
    }
} 