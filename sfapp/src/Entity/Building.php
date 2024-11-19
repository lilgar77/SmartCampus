<?php

namespace App\Entity;

use App\Repository\BuildingRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;

//Entity uniqueness
#[UniqueEntity(
    fields: ['NameBuilding','AdressBuilding'],
    message: 'Ce Batiment est déjà utilisée.'
)]
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

    #[ORM\OneToMany(targetEntity: Floor::class, mappedBy: 'IdBuilding')]
    private Collection $floors;

    #[ORM\OneToMany(targetEntity: Room::class, mappedBy: 'building')]
    private Collection $rooms;

    public function __construct()
    {
        $this->floors = new ArrayCollection();
    }

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

    /**
     * @return Collection<int, Floor>
     */
    public function getFloors(): Collection
    {
        return $this->floors;
    }

    public function addFloor(Floor $floor): self
    {
        if (!$this->floors->contains($floor)) {
            $this->floors[] = $floor;
            $floor->setIdBuilding($this);
        }

        return $this;
    }



    public function removeFloor(Floor $floor): self
    {
        if ($this->floors->removeElement($floor)) {
            if ($floor->getIdBuilding() === $this) {
                $floor->setIdBuilding(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, Room>
     */
    public function getRooms(): Collection
    {
        return $this->rooms;
    }

    public function addRoom(Room $room): self
    {
        if (!$this->floors->contains($room)) {
            $this->floors[] = $room;
            $room->setBuilding($this);
        }

        return $this;
    }

    public function removeRoom(Room $room): self
    {
        if ($this->rooms->removeElement($room)) {
            if ($room->getBuilding() === $this) {
                $room->setBuilding(null);
            }
        }

        return $this;
    }

    public function __toString(): string
    {
        return $this->NameBuilding;
    }
}
