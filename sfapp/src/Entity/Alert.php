<?php

namespace App\Entity;

use App\Repository\AlertRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use App\Model\AlertType;

#[ORM\Entity(repositoryClass: AlertRepository::class)]
class Alert
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE)]
    private ?\DateTimeInterface $DateBegin = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE, nullable: true)]
    private ?\DateTimeInterface $DateEnd = null;

    #[ORM\ManyToOne(inversedBy: 'alerts')]
    private ?AcquisitionSystem $IdSA = null;

    #[ORM\ManyToOne(inversedBy: 'alerts')]
    private ?Room $IdRoom = null;

    #[ORM\Column(enumType: AlertType::class)] // Utilisation de l'enum
    private ?AlertType $type = null;

    #[ORM\Column(length: 255)]
    private ?string $description = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getDateBegin(): ?\DateTimeInterface
    {
        return $this->DateBegin;
    }

    public function setDateBegin(\DateTimeInterface $DateBegin): static
    {
        $this->DateBegin = $DateBegin;

        return $this;
    }

    public function getDateEnd(): ?\DateTimeInterface
    {
        return $this->DateEnd;
    }

    public function setDateEnd(?\DateTimeInterface $DateEnd): static
    {
        $this->DateEnd = $DateEnd;

        return $this;
    }

    public function getType(): ?AlertType
    {
        return $this->type;
    }

    public function setType(AlertType $type): static
    {
        $this->type = $type;

        return $this;
    }

    public function getIdSA(): ?AcquisitionSystem
    {
        return $this->IdSA;
    }

    public function setIdSA(?AcquisitionSystem $IdSA): static
    {
        $this->IdSA = $IdSA;

        return $this;
    }

    public function getIdRoom(): ?Room
    {
        return $this->IdRoom;
    }

    public function setIdRoom(?Room $IdRoom): static
    {
        $this->IdRoom = $IdRoom;

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

    public function closeAlert(): static
    {
        if ($this->DateEnd === null) {
            $date = new \DateTime('now', new \DateTimeZone('Europe/Paris'));
            $this->DateEnd = $date; // Définit la date de fin comme la date actuelle
        }
        return $this;
    }
}
