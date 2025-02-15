<?php

namespace App\Entity;

use App\Repository\DonationRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: DonationRepository::class)]
#[ORM\Table(name: 'donation')]
class Donation
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(name: 'iddon', type: Types::INTEGER)]
    private ?int $iddon = null;

    #[ORM\Column(name: 'donorname', length: 255)]
    private ?string $donorname = null;

    #[ORM\Column(name: 'email', length: 255)]
    private ?string $email = null;

    #[ORM\Column(name: 'montant', type: Types::FLOAT)]
    private ?float $montant = null;

    #[ORM\Column(name: 'date', type: Types::DATE_MUTABLE)]
    private ?\DateTimeInterface $date = null;

    #[ORM\Column(name: 'payment_method', length: 255)]
    private ?string $payment_method = null;

    #[ORM\Column(name: 'num_tlf', length: 20, nullable: true)]
    private ?string $num_tlf = null;

    #[ORM\ManyToOne(targetEntity: Event::class, inversedBy: 'donations')]
    #[ORM\JoinColumn(name: 'idevent', referencedColumnName: 'idevent', nullable: false)]
    private ?Event $idevent = null;

    #[ORM\ManyToOne(targetEntity: User::class)]
    #[ORM\JoinColumn(name: 'userid', referencedColumnName: 'user_id', nullable: true)]
    private ?User $userid = null;

    public function getiddon(): ?int
    {
        return $this->iddon;
    }

    public function getDonorname(): ?string
    {
        return $this->donorname;
    }

    public function setDonorname(string $donorname): static
    {
        $this->donorname = $donorname;
        return $this;
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(string $email): static
    {
        $this->email = $email;
        return $this;
    }

    public function getMontant(): ?float
    {
        return $this->montant;
    }

    public function setMontant(float $montant): static
    {
        $this->montant = $montant;
        return $this;
    }

    public function getDate(): ?\DateTimeInterface
    {
        return $this->date;
    }

    public function setDate(\DateTimeInterface $date): static
    {
        $this->date = $date;
        return $this;
    }

    public function getpayment_method(): ?string
    {
        return $this->payment_method;
    }

    public function setpayment_method(string $payment_method): static
    {
        $this->payment_method = $payment_method;
        return $this;
    }

    public function getnum_tlf(): ?string
    {
        return $this->num_tlf;
    }

    public function setnum_tlf(?string $num_tlf): static
    {
        $this->num_tlf = $num_tlf;
        return $this;
    }

    public function getidevent(): ?Event
    {
        return $this->idevent;
    }

    public function setidevent(?Event $idevent): static
    {
        $this->idevent = $idevent;
        return $this;
    }

    public function getUser(): ?User
    {
        return $this->userid;
    }

    public function setUser(?User $userid): static
    {
        $this->userid = $userid;
        return $this;
    }
}