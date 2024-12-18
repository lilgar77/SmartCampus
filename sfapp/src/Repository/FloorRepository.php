<?php

namespace App\Repository;

use App\Entity\AcquisitionSystem;
use App\Entity\Floor;
use App\Entity\Room;
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
     * @return Floor[] Un tableau contenant des entités Floor avec un système d'acquisition "installé".
     */
    public function sortFloors() : array
    {
        /** @var Floor[] $floor */
        $floor = $this->createQueryBuilder('f')
            ->orderBy('LENGTH(f.numberFloor)', 'ASC') // Trier d'abord par longueur (pour gérer les nombres)
            ->addOrderBy('f.numberFloor', 'ASC')
            ->getQuery()
            ->getResult();
        return $floor;
    }


//    /**
//     * @return Floor[] Returns an array of Floor objects
//     */
//    public function findByExampleField($value): array
//    {
//        return $this->createQueryBuilder('f')
//            ->andWhere('f.exampleField = :val')
//            ->setParameter('val', $value)
//            ->orderBy('f.id', 'ASC')
//            ->setMaxResults(10)
//            ->getQuery()
//            ->getResult()
//        ;
//    }

//    public function findOneBySomeField($value): ?Floor
//    {
//        return $this->createQueryBuilder('f')
//            ->andWhere('f.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
