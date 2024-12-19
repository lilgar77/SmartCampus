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
            ->setMaxResults(1)
            ->getQuery()
            ->getOneOrNullResult();

        if ($result instanceof Building) {
            return $result;
        }

        return null;
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
