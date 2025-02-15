<?php

namespace App\Entity;

use App\Repository\EventRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;

#[ORM\Entity(repositoryClass: EventRepository::class)]
class Event
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: Types::INTEGER)]
    private ?int $idevent = null;


    #[ORM\Column(name: "titre", length: 255)]
    private ?string $titre = null;

    #[ORM\Column(name: "description", type: Types::TEXT)]
    private ?string $description = null;

    #[ORM\Column(name: "dateEvenement", type: 'datetime')]
    private ?\DateTimeInterface $dateEvenement = null;

    #[ORM\Column(name: "lieu", length: 255)]
    private ?string $lieu = null;

    #[ORM\Column(name: "nombreBillets")]
    private ?int $nombreBillets = null;

    #[ORM\Column(name: "image", length: 255, nullable: true)]
    private ?string $image = null;

    #[ORM\Column(name: "timestart", type: Types::TIME_MUTABLE)]
    private ?\DateTimeInterface $timestart = null;

    #[ORM\Column(name: "event_mission", type: Types::TEXT, nullable: true)]
    private ?string $eventMission = null;

    #[ORM\Column(name: "donation_objective", type: Types::FLOAT, nullable: true)]
    private ?float $donation_objective = null;

    #[ORM\Column(name: "seatprice", type: Types::FLOAT)]
    private ?float $seatprice = null;

    #[ORM\OneToMany(targetEntity: Donation::class, mappedBy: 'event')]
    private Collection $donations;

    #[ORM\OneToMany(mappedBy: 'event', targetEntity: Reservation::class)]
    private Collection $reservations;

    public function __construct()
    {
        $this->donations = new ArrayCollection();
        $this->reservations = new ArrayCollection();
    }

    public function getidevent(): ?int
    {
        return $this->idevent;
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

    public function setDateEvenement(?\DateTimeInterface $dateEvenement): self
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
        return $this->eventMission;
    }

    public function setEventMission(?string $eventMission): static
    {
        $this->eventMission = $eventMission;
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

    /**
     * @return Collection<int, Donation>
     */
    public function getDonations(): Collection
    {
        return $this->donations;
    }

    public function addDonation(Donation $donation): static
    {
        if (!$this->donations->contains($donation)) {
            $this->donations->add($donation);
            $donation->setidEvent($this);
        }
        return $this;
    }

    public function removeDonation(Donation $donation): static
    {
        if ($this->donations->removeElement($donation)) {
            if ($donation->getidEvent() === $this) {
                $donation->setidEvent(null);
            }
        }
        return $this;
    }

    /**
     * @return Collection<int, Reservation>
     */
    public function getReservations(): Collection
    {
        return $this->reservations;
    }

    public function addReservation(Reservation $reservation): self
    {
        if (!$this->reservations->contains($reservation)) {
            $this->reservations->add($reservation);
            $reservation->setEvent($this);
        }
        return $this;
    }

    public function removeReservation(Reservation $reservation): self
    {
        if ($this->reservations->removeElement($reservation)) {
            if ($reservation->getEvent() === $this) {
                $reservation->setEvent(null);
            }
        }
        return $this;
    }

    public function __toString(): string
    {
        // Adjust this to return the property you want to display
        return $this->titre ?? (string)$this->idevent;
    }
} 