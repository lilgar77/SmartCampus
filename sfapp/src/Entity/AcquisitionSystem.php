<?php

namespace App\Entity;

use App\Model\EtatAS;
use App\Repository\AcquisitionSystemRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: AcquisitionSystemRepository::class)]
class AcquisitionSystem
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column]
    private ?int $temperature = null;

    #[ORM\Column]
    private ?int $CO2 = null;

    #[ORM\Column]
    private ?int $humidity = null;

    #[ORM\Column(length: 255)]
    private ?string $wording = null;

    #[ORM\Column(length: 255)]
    private ?string $macAdress = null;

    #[ORM\Column(enumType: EtatAS::class)]
    private ?EtatAS $etat = null;

    #[ORM\OneToOne(mappedBy: 'id_AS', cascade: ['persist', 'remove'])]
    private ?Room $room = null;


    public function getId(): ?int
    {
        return $this->id;
    }

    public function getTemperature(): ?int
    {
        return $this->temperature;
    }

    public function setTemperature(int $temperature): static
    {
        $this->temperature = $temperature;

        return $this;
    }

    public function getCO2(): ?int
    {
        return $this->CO2;
    }

    public function setCO2(int $CO2): static
    {
        $this->CO2 = $CO2;

        return $this;
    }

    public function getHumidity(): ?int
    {
        return $this->humidity;
    }

    public function setHumidity(int $humidity): static
    {
        $this->humidity = $humidity;

        return $this;
    }

    public function getWording(): ?string
    {
        return $this->wording;
    }

    public function setWording(string $wording): static
    {
        $this->wording = $wording;

        return $this;
    }

    public function getMacAdress(): ?string
    {
        return $this->macAdress;
    }

    public function setMacAdress(string $macAdress): static
    {
        $this->macAdress = $macAdress;

        return $this;
    }

    public function getEtat(): ?EtatAS
    {
        return $this->etat;
    }

    public function setEtat(EtatAS $etat): static
    {
        $this->etat = $etat;

        return $this;
    }

    public function getRoom(): ?Room
    {
        return $this->room;
    }

    public function setRoom(?Room $room): static
    {
        // unset the owning side of the relation if necessary
        if ($room === null && $this->room !== null) {
            $this->room->setIdAS(null);
        }

        // set the owning side of the relation if necessary
        if ($room !== null && $room->getIdAS() !== $this) {
            $room->setIdAS($this);
        }

        $this->room = $room;

        return $this;
    }
    public function __toString(): string
    {
        return $this->etat;
    }

}
