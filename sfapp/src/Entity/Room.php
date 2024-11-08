<?php

namespace App\Entity;

use App\Repository\RoomRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: RoomRepository::class)]
class Room
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $name = null;

    #[ORM\OneToOne(inversedBy: 'room', cascade: ['persist', 'remove'])]
    private ?AcquisitionSystem $id_AS = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): static
    {
        $this->name = $name;

        return $this;
    }

    public function getIdAS(): ?AcquisitionSystem
    {
        return $this->id_AS;
    }

    public function setIdAS(?AcquisitionSystem $id_AS): static
    {
        $this->id_AS = $id_AS;

        return $this;
    }
}
