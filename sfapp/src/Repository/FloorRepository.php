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
     * @param string $numberFloor
     * @return Floor|null
     */
    public function findFloorByNumber(string $numberFloor): ?Floor
    {
        $result = $this->createQueryBuilder('f')
            ->andWhere('f.numberFloor = :numberFloor')
            ->setParameter('numberFloor', $numberFloor)
            ->getQuery()
            ->getOneOrNullResult();

        if ($result instanceof Floor) {
            return $result;
        }

        return null;
    }

    /**
     * @param Floor $criteria
     * @return Floor[]
     */
    public function findFloorByBuilding(Floor $criteria): array
    {
        $queryBuilder = $this->createQueryBuilder('f')
            ->leftJoin('f.IdBuilding', 'b');

        if ($criteria->getIdBuilding() && $criteria->getIdBuilding()->getNameBuilding()) {
            $queryBuilder
                ->andWhere('b.NameBuilding LIKE :building')
                ->setParameter('building', '%' . $criteria->getIdBuilding()->getNameBuilding() . '%');
        }
        $result = $queryBuilder->getQuery()->getResult();
        // Execute the query and return the results as an array
        /** @var Floor[] $result */
        return $result;
    }
}
