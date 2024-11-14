<?php
#########################################################################
## @Name of file : Floor.php                                           ##
## @brief : Entity representing a floor                                ##
## @Function : Defines the properties and methods for the Floor entity ##
####                                                                   ##
## Uses Doctrine ORM to map Floor entity properties to the database.   ##
##                                                                     ##
#########################################################################

namespace App\Entity;

use App\Repository\FloorRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: FloorRepository::class)]
class Floor
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\ManyToOne(inversedBy: 'floors')]
    #[ORM\JoinColumn(nullable: false, onDelete: 'CASCADE')]
    private ?Building $IdBuilding = null;

    #[ORM\Column]
    private ?int $numberFloor = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getIdBuilding(): ?Building
    {
        return $this->IdBuilding;
    }

    public function setIdBuilding(?Building $IdBuilding): static
    {
        $this->IdBuilding = $IdBuilding;

        return $this;
    }

    public function getNumberFloor(): ?int
    {
        return $this->numberFloor;
    }

    public function setNumberFloor(int $numberFloor): static
    {
        $this->numberFloor = $numberFloor;

        return $this;
    }
}
