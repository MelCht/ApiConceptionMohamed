<?php

namespace App\Entity;
use App\Entity\Country;

use App\Repository\AnimalRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: AnimalRepository::class)]
class Animal
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $Nom = null;

    #[ORM\Column(nullable: true)]
    private ?float $tailleMoyenne = null;

    #[ORM\Column(nullable: true)]
    private ?float $dureeVieMoyenne = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $artMartial = null;

    #[ORM\Column(nullable: true)]
    private ?string $numeroTelephone = null;

    #[ORM\ManyToOne(inversedBy: 'animals')]
    private ?Country $country = null;


    public function getId(): ?int
    {
        return $this->id;
    }

    public function getNom(): ?string
    {
        return $this->Nom;
    }

    public function setNom(?string $Nom): self
    {
        $this->Nom = $Nom;

        return $this;
    }

    public function getTailleMoyenne(): ?float
    {
        return $this->tailleMoyenne;
    }

    public function setTailleMoyenne(?float $tailleMoyenne): self
    {
        $this->tailleMoyenne = $tailleMoyenne;

        return $this;
    }


    public function getDureeVieMoyenne(): ?float
    {
        return $this->dureeVieMoyenne;
    }

    public function setDureeVieMoyenne(?float $dureeVieMoyenne): self
    {
        $this->dureeVieMoyenne = $dureeVieMoyenne;

        return $this;
    }

    public function getArtMartial(): ?string
    {
        return $this->artMartial;
    }

    public function setArtMartial(?string $artMartial): self
    {
        $this->artMartial = $artMartial;

        return $this;
    }

    public function getNumeroTelephone(): ?string
    {
        return $this->numeroTelephone;
    }

    public function setNumeroTelephone(?string $numeroTelephone): self
    {
        $this->numeroTelephone = $numeroTelephone;

        return $this;
    }

    public function getCountry(): ?String
    {
        return $this->country->getNom();
    }

    public function setCountry(?Country $country): self
    {
        $this->country = $country;

        return $this;
    }
}
