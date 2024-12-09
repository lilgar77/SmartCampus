<?php

namespace App\Repository;

use App\Entity\AcquisitionSystem;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use App\Controller\RoomsController;
/**
 * @extends ServiceEntityRepository<AcquisitionSystem>
 *
 * @method AcquisitionSystem|null find($id, $lockMode = null, $lockVersion = null)
 * @method AcquisitionSystem|null findOneBy(array $criteria, array $orderBy = null)
 * @method AcquisitionSystem[]    findAll()
 * @method AcquisitionSystem[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class AcquisitionSystemRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, AcquisitionSystem::class);
    }

    public function findASByName($name): ?AcquisitionSystem
    {
        return $this->createQueryBuilder('a')
            ->andWhere('a.Name = :name')
            ->setParameter('name', $name)
            ->getQuery()
            ->getOneOrNullResult();
    }


//    /**
//     * @return AcquisitionSystem[] Returns an array of AcquisitionSystem objects
//     */
//    public function findByExampleField($value): array
//    {
//        return $this->createQueryBuilder('a')
//            ->andWhere('a.exampleField = :val')
//            ->setParameter('val', $value)
//            ->orderBy('a.id', 'ASC')
//            ->setMaxResults(10)
//            ->getQuery()
//            ->getResult()
//        ;
//    }

//    public function findOneBySomeField($value): ?AcquisitionSystem
//    {
//        return $this->createQueryBuilder('a')
//            ->andWhere('a.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
