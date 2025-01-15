<?php

namespace App\Repository;

use App\Entity\Alert;
use App\Entity\Room;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Alert>
 */
class AlertRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Alert::class);
    }

    /**
     * Find all alerts without an end date.
     *
     * @return Alert[] Returns an array of Alert objects
     */
    public function findWithoutDateEnd(): array
    {
        $result = $this->createQueryBuilder('a')
            ->andWhere('a.DateEnd IS NULL')
            ->orderBy('a.DateBegin', 'ASC')
            ->getQuery()
            ->getResult();

        /** @var Alert[] $result */
        return $result;
    }

    /**
     * Find all alerts that have an end date.
     *
     * @return Alert[] Returns an array of Alert objects
     */
    public function findWithDateEnd(): array
    {
        $result = $this->createQueryBuilder('a')
            ->andWhere('a.DateEnd IS NOT NULL')
            ->orderBy('a.DateBegin', 'ASC')
            ->getQuery()
            ->getResult();

        /** @var Alert[] $result */
        return $result;
    }

    /**
     * Find all active alerts for a specific room.
     *
     * @param Room $room
     * @return Alert[] Returns an array of Alert objects
     */
    public function findActiveAlertsByRoom(Room $room): array
    {
        $result = $this->createQueryBuilder('a')
            ->andWhere('a.IdRoom = :room')
            ->andWhere('a.DateEnd IS NULL')
            ->setParameter('room', $room)
            ->getQuery()
            ->getResult();

        /** @var Alert[] $result */
        return $result;
    }

    /**
     * Find the last five alerts for a specific room, ordered by descending start date.
     *
     * @param Room $room
     * @return Alert[] Returns an array of Alert objects
     */
    public function findLastFiveAlertsByRoom(Room $room): array
    {
        $result = $this->createQueryBuilder('a')
            ->andWhere('a.IdRoom = :room') // Filter alerts by room
            ->setParameter('room', $room)
            ->orderBy('a.DateBegin', 'DESC') // Order by start date descending (most recent first)
            ->setMaxResults(5) // Limit to 5 results
            ->getQuery()
            ->getResult();

        /** @var Alert[] $result */
        return $result;
    }

    /**
     * Find all alerts for a specific room.
     *
     * @param Room $room
     * @return Alert[] Returns an array of Alert objects
     */
    public function findAlertsByRoom(Room $room): array
    {
        $result = $this->createQueryBuilder('a')
            ->andWhere('a.IdRoom = :room')
            ->setParameter('room', $room)
            ->getQuery()
            ->getResult();

        /** @var Alert[] $result */
        return $result;
    }
}
