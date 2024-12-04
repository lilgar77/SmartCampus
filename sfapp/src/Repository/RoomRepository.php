<?php

namespace App\Repository;

use App\Entity\AcquisitionSystem;
use App\Entity\Room;
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
            return $result; // Retourne l'objet Room
        }

        return null; // Retourne null si ce n'est pas un Room
    }

    /**
     * @return Room[] Un tableau indexé contenant des entités Room.
     */
    public function findRoomWithAs(): array
    {
        $result = $this->createQueryBuilder('r')
            ->andWhere('r.id_AS IS NOT NULL')
            ->getQuery()
            ->getResult();


        /** @var Room[] $result */
        return $result;
    }


}
