<?php

namespace App\Repository;

use App\Entity\Alert;
use App\Entity\Room;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

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

    public function findActiveAlertsByRoom(Room $room): array
    {
        return $this->createQueryBuilder('a')
            ->andWhere('a.IdRoom = :room')
            ->andWhere('a.DateEnd IS NULL')
            ->setParameter('room', $room)
            ->getQuery()
            ->getResult();
    }
}
