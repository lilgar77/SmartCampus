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
     * @return Building|null
     */
    public function findBuildingByName(string $name): ?Building
    {
        $result = $this->createQueryBuilder('b')
            ->andWhere('b.NameBuilding = :NameBuilding')
            ->setParameter('NameBuilding', $name)
            ->getQuery()
            ->getOneOrNullResult();

        if ($result instanceof Building) {
            return $result;
        }

        return null;
    }

//    /**
//     * @return Building[] Returns an array of Building objects
//     */
//    public function findByExampleField($value): array
//    {
//        return $this->createQueryBuilder('b')
//            ->andWhere('b.exampleField = :val')
//            ->setParameter('val', $value)
//            ->orderBy('b.id', 'ASC')
//            ->setMaxResults(10)
//            ->getQuery()
//            ->getResult()
//        ;
//    }

//    public function findOneBySomeField($value): ?Building
//    {
//        return $this->createQueryBuilder('b')
//            ->andWhere('b.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
