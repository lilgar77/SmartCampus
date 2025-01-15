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
     * Find buildings by name using a partial match (case-sensitive).
     *
     * @param string $name The name or partial name of the building to search for.
     * @return Building[] Returns an array of Building objects matching the search criteria.
     */
    public function findBuildingByName(string $name): ?array
    {
        /** @var Building[] $buildings */
        $buildings = $this->createQueryBuilder('b')
            ->where('b.NameBuilding LIKE :NameBuilding')
            ->setParameter('NameBuilding', '%' . $name . '%')
            ->orderBy('b.NameBuilding', 'ASC') // Sort results alphabetically by building name
            ->getQuery()
            ->getResult();

        return $buildings;
    }

    /**
     * Sort all buildings alphabetically by name.
     *
     * @return Building[] Returns an array of Building objects sorted by name.
     */
    public function sortBuildings(): array
    {
        /** @var Building[] $building */
        $building = $this->createQueryBuilder('b')
            ->orderBy('b.NameBuilding', 'ASC') // Sort results alphabetically by building name
            ->getQuery()
            ->getResult();

        return $building;
    }
}