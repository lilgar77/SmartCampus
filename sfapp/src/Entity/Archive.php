<?php

namespace App\Entity;

use App\Repository\ArchiveRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: ArchiveRepository::class)]
class Archive
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(nullable: true)]
    private ?float $Temperature = null;

    #[ORM\Column(nullable: true)]
    private ?float $Humidity = null;

    #[ORM\Column(nullable: true)]
    private ?int $CO2 = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE)]
    private ?\DateTimeInterface $DateCapture = null;

    #[ORM\ManyToOne]
    #[ORM\JoinColumn(nullable: false)]
    private ?AcquisitionSystem $AcquisitionSystem = null;

    #[ORM\ManyToOne]
    #[ORM\JoinColumn(nullable: false)]
    private ?Room $Room = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getTemperature(): ?float
    {
        return $this->Temperature;
    }

    public function setTemperature(?float $Temperature): static
    {
        $this->Temperature = $Temperature;

        return $this;
    }

    public function getHumidity(): ?float
    {
        return $this->Humidity;
    }

    public function setHumidity(?float $Humidity): static
    {
        $this->Humidity = $Humidity;

        return $this;
    }

    public function getCO2(): ?int
    {
        return $this->CO2;
    }

    public function setCO2(?int $CO2): static
    {
        $this->CO2 = $CO2;

        return $this;
    }

    public function getDateCapture(): ?\DateTimeInterface
    {
        return $this->DateCapture;
    }

    public function setDateCapture(\DateTimeInterface $DateCapture): static
    {
        $this->DateCapture = $DateCapture;

        return $this;
    }

    public function getAcquisitionSystem(): ?AcquisitionSystem
    {
        return $this->AcquisitionSystem;
    }

    public function setAcquisitionSystem(?AcquisitionSystem $AcquisitionSystem): static
    {
        $this->AcquisitionSystem = $AcquisitionSystem;

        return $this;
    }

    public function getRoom(): ?Room
    {
        return $this->Room;
    }

    public function setRoom(?Room $Room): static
    {
        $this->Room = $Room;

        return $this;
    }
}
