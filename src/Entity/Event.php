<?php

namespace App\Entity;

use App\Repository\EventRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: EventRepository::class)]
class Event
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(name: "idevent")]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $titre = null;

    #[ORM\Column(type: Types::TEXT)]
    private ?string $description = null;

    #[ORM\Column(type: Types::DATE_MUTABLE)]
    private ?\DateTimeInterface $dateEvenement = null;

    #[ORM\Column(length: 255)]
    private ?string $lieu = null;

    #[ORM\Column]
    private ?int $nombreBillets = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $image = null;

    #[ORM\Column(type: Types::TIME_MUTABLE)]
    private ?\DateTimeInterface $timestart = null;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    private ?string $event_mission = null;

    #[ORM\Column(type: Types::FLOAT, nullable: true)]
    private ?float $donation_objective = null;

    #[ORM\Column(type: Types::FLOAT)]
    private ?float $seatprice = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getTitre(): ?string
    {
        return $this->titre;
    }

    public function setTitre(string $titre): static
    {
        $this->titre = $titre;
        return $this;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(string $description): static
    {
        $this->description = $description;
        return $this;
    }

    public function getDateEvenement(): ?\DateTimeInterface
    {
        return $this->dateEvenement;
    }

    public function setDateEvenement(\DateTimeInterface $dateEvenement): static
    {
        $this->dateEvenement = $dateEvenement;
        return $this;
    }

    public function getLieu(): ?string
    {
        return $this->lieu;
    }

    public function setLieu(string $lieu): static
    {
        $this->lieu = $lieu;
        return $this;
    }

    public function getNombreBillets(): ?int
    {
        return $this->nombreBillets;
    }

    public function setNombreBillets(int $nombreBillets): static
    {
        $this->nombreBillets = $nombreBillets;
        return $this;
    }

    public function getImage(): ?string
    {
        return $this->image;
    }

    public function setImage(?string $image): static
    {
        $this->image = $image;
        return $this;
    }

    public function getTimestart(): ?\DateTimeInterface
    {
        return $this->timestart;
    }

    public function setTimestart(\DateTimeInterface $timestart): static
    {
        $this->timestart = $timestart;
        return $this;
    }

    public function getEventMission(): ?string
    {
        return $this->event_mission;
    }

    public function setEventMission(?string $event_mission): static
    {
        $this->event_mission = $event_mission;
        return $this;
    }

    public function getDonationObjective(): ?float
    {
        return $this->donation_objective;
    }

    public function setDonationObjective(?float $donation_objective): static
    {
        $this->donation_objective = $donation_objective;
        return $this;
    }

    public function getSeatprice(): ?float
    {
        return $this->seatprice;
    }

    public function setSeatprice(float $seatprice): static
    {
        $this->seatprice = $seatprice;
        return $this;
    }
} 