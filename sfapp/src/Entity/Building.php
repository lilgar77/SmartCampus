<?php

namespace App\Entity;

use App\Repository\BuildingRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: BuildingRepository::class)]
class Building
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $NameBuilding = null;

    #[ORM\Column(length: 255)]
    private ?string $AdressBuilding = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getNameBuilding(): ?string
    {
        return $this->NameBuilding;
    }

    public function setNameBuilding(string $NameBuilding): static
    {
        $this->NameBuilding = $NameBuilding;

        return $this;
    }

    public function getAdressBuilding(): ?string
    {
        return $this->AdressBuilding;
    }

    public function setAdressBuilding(string $AdressBuilding): static
    {
        $this->AdressBuilding = $AdressBuilding;

        return $this;
    }
}
