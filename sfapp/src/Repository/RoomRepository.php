<?php

namespace App\Repository;

use App\Entity\Room;
use App\Model\EtatAS;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;


/**
 * @extends ServiceEntityRepository<Room>
 */
class RoomRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Room::class);
    }

    /**
     * @param string $name
     * @return Room|null
     */
    public function findRoomByName(string $name): ?Room
    {
        $result = $this->createQueryBuilder('r')
            ->andWhere('r.name = :name')
            ->setParameter('name', $name)
            ->getQuery()
            ->getOneOrNullResult();

        if ($result instanceof Room) {
            return $result;
        }

        return null;
    }

    /**
     * Trouve les entités Room dont le nom commence par une chaîne donnée.
     *
     * @param string $name La chaîne utilisée pour rechercher les entités.
     * @return Room[] Un tableau indexé contenant des entités Room.
     */
    public function findByNameStartingWith(string $name): array
    {
        /** @var Room[] $rooms */
        $rooms = $this->createQueryBuilder('r')
            ->where('r.name LIKE :name')
            ->setParameter('name', $name . '%')
            ->orderBy('r.name', 'ASC')
            ->getQuery()
            ->getResult();

        return $rooms;
    }


    /**
     * @return Room[] Un tableau contenant des entités Room avec un système d'acquisition "installé".
     */
    public function findRoomWithAs(): array
    {
        /** @var Room[] $room */
        $room = $this->createQueryBuilder('r')
            ->leftJoin('r.id_AS', 'acs')
            ->andWhere('acs IS NOT NULL')
            ->andWhere('acs.etat = :etat')
            ->setParameter('etat', EtatAS::Installer)
            ->orderBy('r.name', 'ASC')
            ->getQuery()
            ->getResult();

        return $room;
    }

    /**
     * @param Room $criteria
     * @return Room[]
     */
    public function findByCriteria(Room $criteria): array
    {
        $queryBuilder = $this->createQueryBuilder('r')
            ->leftJoin('r.building', 'b')
            ->leftJoin('r.floor', 'f'); // Join the Floor entity

        if ($criteria->getName()) {
            $queryBuilder->andWhere('r.name LIKE :name')
                ->setParameter('name', '%' . $criteria->getName() . '%');
        }

        if ($criteria->getFloor()) {
            $queryBuilder
                ->andWhere('f.numberFloor LIKE :floor')
                ->setParameter('floor', '%' . $criteria->getFloor()->getNumberFloor() . '%');
        }

        if ($criteria->getBuilding()) {
            $queryBuilder
                ->andWhere('b.NameBuilding LIKE :building')
                ->setParameter('building', '%' . $criteria->getBuilding()->getNameBuilding() . '%');
        }

        $result = $queryBuilder->orderBy('r.name', 'ASC')->getQuery()->getResult();

        // Cast the result to an array of Room entities
        /** @var Room[] $result */
        return $result;
    }

}
