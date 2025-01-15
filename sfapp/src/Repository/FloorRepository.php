<?php

namespace App\Repository;

use App\Entity\AcquisitionSystem;
use App\Entity\Floor;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Floor>
 */
class FloorRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Floor::class);
    }

    /**
     * Find a floor by its floor number.
     *
     * @param string $numberFloor The floor number to search for.
     * @return Floor|null Returns a Floor entity if found, or null if not.
     */
    public function findFloorByNumber(string $numberFloor): ?Floor
    {
        $result = $this->createQueryBuilder('f')
            ->andWhere('f.numberFloor = :numberFloor') // Match floor by its number
            ->setParameter('numberFloor', $numberFloor)
            ->getQuery()
            ->getOneOrNullResult();

        if ($result instanceof Floor) {
            return $result;
        }

        return null;
    }

    /**
     * Find floors based on a building's criteria.
     *
     * @param Floor $criteria A Floor entity containing criteria such as the building name.
     * @return Floor[] Returns an array of Floor entities that match the criteria.
     */
    public function findFloorByBuilding(Floor $criteria): array
    {
        $queryBuilder = $this->createQueryBuilder('f')
            ->leftJoin('f.IdBuilding', 'b'); // Join with the building entity

        // Filter by building name if it's provided
        if ($criteria->getIdBuilding() && $criteria->getIdBuilding()->getNameBuilding()) {
            $queryBuilder
                ->andWhere('b.NameBuilding LIKE :building') // Match by building name
                ->setParameter('building', '%' . $criteria->getIdBuilding()->getNameBuilding() . '%');
        }

        $result = $queryBuilder->getQuery()->getResult();

        /** @var Floor[] $result */
        return $result; // Return the results as an array of Floor entities
    }
}