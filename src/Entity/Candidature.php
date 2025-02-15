<?php

namespace App\Entity;

use App\Repository\CandidatureRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: CandidatureRepository::class)]
class Candidature
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\ManyToOne(targetEntity: User::class)]
    #[ORM\JoinColumn(name: "candidat", referencedColumnName: "user_id")]
    private ?User $candidat = null;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    private ?string $competences = null;

    #[ORM\Column(length: 20)]
    private string $disponibilite = 'À convenir';

    #[ORM\Column(type: Types::DECIMAL, precision: 10, scale: 2, nullable: true)]
    private ?string $tarif_horaire = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE)]
    private ?\DateTimeInterface $date_creation = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getCandidat(): ?User
    {
        return $this->candidat;
    }

    public function setCandidat(?User $candidat): static
    {
        $this->candidat = $candidat;
        return $this;
    }

    public function getCompetences(): ?string
    {
        return $this->competences;
    }

    public function setCompetences(?string $competences): static
    {
        $this->competences = $competences;
        return $this;
    }

    public function getDisponibilite(): string
    {
        return $this->disponibilite;
    }

    public function setDisponibilite(string $disponibilite): static
    {
        if (!in_array($disponibilite, ['Immédiate', 'À convenir'])) {
            throw new \InvalidArgumentException('Invalid disponibilite');
        }
        $this->disponibilite = $disponibilite;
        return $this;
    }

    public function getTarifHoraire(): ?string
    {
        return $this->tarif_horaire;
    }

    public function setTarifHoraire(?string $tarif_horaire): static
    {
        $this->tarif_horaire = $tarif_horaire;
        return $this;
    }

    public function getDateCreation(): ?\DateTimeInterface
    {
        return $this->date_creation;
    }

    public function setDateCreation(\DateTimeInterface $date_creation): static
    {
        $this->date_creation = $date_creation;
        return $this;
    }
} 