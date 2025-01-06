<?php

namespace App\Repository;

use App\Entity\Alert;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Alert>
 *
 * @method Alert|null find($id, $lockMode = null, $lockVersion = null)
 * @method Alert|null findOneBy(array $criteria, array $orderBy = null)
 * @method Alert[]    findAll()
 * @method Alert[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class AlertRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Alert::class);
    }

//    /**
//     * @return Alert[] Returns an array of Alert objects
//     */
    public function findWithoutDateEnd(): array
    {
        return $this->createQueryBuilder('a')
            ->andWhere('a.DateEnd IS NULL')
            ->orderBy('a.DateBegin', 'ASC') // Vous pouvez modifier l'ordre si besoin
            ->getQuery()
            ->getResult();
    }

    public function findWithDateEnd(): array
    {
        return $this->createQueryBuilder('a')
            ->andWhere('a.DateEnd IS NOT NULL')
            ->orderBy('a.DateBegin', 'ASC') // Vous pouvez modifier l'ordre si besoin
            ->getQuery()
            ->getResult();
    }

//    public function findOneBySomeField($value): ?Alert
//    {
//        return $this->createQueryBuilder('a')
//            ->andWhere('a.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
