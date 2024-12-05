<?php

namespace App\Entity;

use App\Repository\InstallationRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: InstallationRepository::class)]
class Installation
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $Comment = null;

    #[ORM\ManyToOne(cascade: ['persist'])]
    private ?Room $room = null;

    #[ORM\ManyToOne(cascade: ['persist'])]
    private ?AcquisitionSystem $AS = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getComment(): ?string
    {
        return $this->Comment;
    }

    public function setComment(?string $Comment): static
    {
        $this->Comment = $Comment;

        return $this;
    }

    public function getRoom(): ?Room
    {
        return $this->room;
    }

    public function setRoom(?Room $room): static
    {
        $this->room = $room;

        return $this;
    }

    public function getAS(): ?AcquisitionSystem
    {
        return $this->AS;
    }

    public function setSA(?AcquisitionSystem $SA): static
    {
        $this->AS = $SA;

        return $this;
    }
}
