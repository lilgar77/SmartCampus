<?php

namespace App\Repository;

use App\Entity\AcquisitionSystem;
use App\Entity\Building;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;


/**
 * @extends ServiceEntityRepository<Building>
 */
class BuildingRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Building::class);
    }

    /**
     * @param string $name
     * @return Building[]
     */
    public function findBuildingByName(string $name): ?array
    {
        /** @var Building[] $buildings */
        $buildings = $this->createQueryBuilder('b')
            ->where('b.NameBuilding LIKE :NameBuilding')
            ->setParameter('NameBuilding', '%' . $name . '%')
           ->orderBy('b.NameBuilding', 'ASC')
            ->getQuery()
            ->getResult();

        return $buildings;
    }

    /**
     * @return Building[] Un tableau contenant des entités Building avec un système d'acquisition "installé".
     */
    public function sortBuildings() : array
    {
        /** @var Building[] $building */
        $building = $this->createQueryBuilder('b')
            ->orderBy('b.NameBuilding', 'ASC')
            ->getQuery()
            ->getResult();
        return $building;
    }
public function findBuildingByNameAndPlace(string $name, string $adresse): ?Building
{
    return $this->createQueryBuilder('b')
        ->where('b.NameBuilding = :name')
        ->andWhere('b.AdressBuilding = :adresse') // Assurez-vous que `place` est le bon nom de champ
        ->setParameter('name', $name)
        ->setParameter('adresse', $adresse)
        ->getQuery()
        ->getOneOrNullResult();
}

}
