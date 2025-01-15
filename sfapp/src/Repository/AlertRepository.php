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
     * @return Alert[] Returns an array of Alert objects
     */
    public function findLastFiveAlertsByRoom(Room $room): array
    {
        $result = $this->createQueryBuilder('a')
            ->andWhere('a.IdRoom = :room') // Filtrer les alertes par salle
            ->setParameter('room', $room)
            ->orderBy('a.DateBegin', 'DESC') // Trier par date de début décroissante (les plus récentes d'abord)
            ->setMaxResults(5) // Limiter à 5 résultats
            ->getQuery()
            ->getResult();

        /** @var Alert[] $result */
        return $result;
    }

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
