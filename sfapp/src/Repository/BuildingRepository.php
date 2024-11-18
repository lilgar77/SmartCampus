<?php

namespace App\Repository;

use App\Entity\AcquisitionSystem;
use App\Entity\Building;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Building>
 *
 * @method Building|null find($id, $lockMode = null, $lockVersion = null)
 * @method Building|null findOneBy(array $criteria, array $orderBy = null)
 * @method Building[]    findAll()
 * @method Building[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class BuildingRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Building::class);
    }

    public function findBuildingByName($name): ?Building
    {
        return $this->createQueryBuilder('b')
            ->andWhere('b.NameBuilding = :NameBuilding')
            ->setParameter('NameBuilding', $name)
            ->getQuery()
            ->getOneOrNullResult();
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
