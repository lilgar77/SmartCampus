<?php

namespace App\Entity;

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
    private ?string $mac_adress = null;

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
        return $this->mac_adress;
    }

    public function setMacAdress(string $mac_adress): static
    {
        $this->mac_adress = $mac_adress;

        return $this;
    }
}
