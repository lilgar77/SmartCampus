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
    /**
     * @var array<string, array{dbname: string, nomsa: string}>
     * Stores a mapping of room names to their database and system identifiers.
     */
    private array $roomdb = [
        // Room database mapping
        'D205' => ['dbname' => 'sae34bdk1eq1', 'nomsa' => 'ESP-004'],
        'D206' => ['dbname' => 'sae34bdk1eq2', 'nomsa' => 'ESP-008'],
        'D207' => ['dbname' => 'sae34bdk1eq3', 'nomsa' => 'ESP-006'],
        'D204' => ['dbname' => 'sae34bdk2eq1', 'nomsa' => 'ESP-014'],
        'D203' => ['dbname' => 'sae34bdk2eq2', 'nomsa' => 'ESP-012'],
        'D303' => ['dbname' => 'sae34bdk2eq3', 'nomsa' => 'ESP-005'],
        'D304' => ['dbname' => 'sae34bdl1eq1', 'nomsa' => 'ESP-011'],
        'C101' => ['dbname' => 'sae34bdl1eq2', 'nomsa' => 'ESP-007'],
        'D109' => ['dbname' => 'sae34bdl1eq3', 'nomsa' => 'ESP-024'],
        'Secrétariat' => ['dbname' => 'sae34bdl2eq1', 'nomsa' => 'ESP-026'],
        'D001' => ['dbname' => 'sae34bdl2eq2', 'nomsa' => 'ESP-030'],
        'D002' => ['dbname' => 'sae34bdl2eq3', 'nomsa' => 'ESP-028'],
        'D004' => ['dbname' => 'sae34bdm1eq1', 'nomsa' => 'ESP-020'],
        'C004' => ['dbname' => 'sae34bdm1eq2', 'nomsa' => 'ESP-021'],
        'C007' => ['dbname' => 'sae34bdm1eq3', 'nomsa' => 'ESP-022'],
    ];

    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Room::class);
    }

    /**
     * @param string $name
     * @return array{dbname: string, nomsa: string}|array{} Returns an array with room db details or an empty array.
     */
    public function getRoomDb(string $name): array
    {
        return $this->roomdb[$name] ?? [];
    }

    /**
     * @return array<string, array{dbname: string, nomsa: string}> Returns the full room db details.
     */
    public function getRoomDbName(): array
    {
        return $this->roomdb;
    }

    /**
     * @param string $name
     * @return Room|null Finds a room by its name and returns it, or null if not found.
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
     * @param string $name
     * @return Room[] Returns an array of Room entities whose names start with the specified string.
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
     * @return Room[] Returns an array of Room entities with an acquisition system installed.
     */
    public function findRoomWithAs(Room $criteria): array
    {
        /** @var Room[] $room */
        $room = $this->createQueryBuilder('r')
            ->leftJoin('r.id_AS', 'acs')
            ->leftJoin('r.building', 'b')
            ->leftJoin('r.floor', 'f')
            ->andWhere('acs IS NOT NULL')
            ->andWhere('acs.etat = :etat')
            ->setParameter('etat', EtatAS::Installer)
            ->andWhere('f.numberFloor LIKE :floor')
            ->setParameter('floor', $criteria->getFloor() ? '%' . $criteria->getFloor()->getNumberFloor() . '%' : null)
            ->andWhere('b.NameBuilding LIKE :building')
            ->setParameter('building', $criteria->getBuilding() ? '%' . $criteria->getBuilding()->getNameBuilding() . '%' : null)
            ->orderBy('r.name', 'ASC')
            ->getQuery()
            ->getResult();

        return $room;
    }

    /**
     * @return Room[] Returns an array of Room entities with a system installed, regardless of other criteria.
     */
    public function findRoomWithAsInstalled(): array
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
     * @return Room[] Finds rooms based on specific criteria such as name, floor, or building.
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

        if ($criteria->getFloor() && $criteria->getFloor()->getNumberFloor()) {
            $queryBuilder
                ->andWhere('f.numberFloor LIKE :floor')
                ->setParameter('floor', '%' . $criteria->getFloor()->getNumberFloor() . '%');
        }

        if ($criteria->getBuilding() && $criteria->getBuilding()->getNameBuilding()) {
            $queryBuilder
                ->andWhere('b.NameBuilding LIKE :building')
                ->setParameter('building', '%' . $criteria->getBuilding()->getNameBuilding() . '%');
        }

        $result = $queryBuilder->orderBy('r.name', 'ASC')->getQuery()->getResult();

        // Cast the result to an array of Room entities
        /** @var Room[] $result */
        return $result;
    }

    /**
     * @return Room[] Finds rooms with an acquisition system installed, in a specific default configuration.
     */
    public function findRoomWithAsDefault(): array
    {
        /** @var Room[] $room */
        $room = $this->createQueryBuilder('r')
            ->leftJoin('r.id_AS', 'acs')
            ->leftJoin('r.building', 'b')
            ->leftJoin('r.floor', 'f')
            ->andWhere('acs IS NOT NULL')
            ->andWhere('acs.etat = :etat')
            ->setParameter('etat', EtatAS::Installer)
            ->andWhere('f.numberFloor LIKE :floor')
            ->setParameter('floor', 0)
            ->andWhere('b.NameBuilding LIKE :building')
            ->setParameter('building', 'informatique')
            ->orderBy('r.name', 'ASC')
            ->getQuery()
            ->getResult();

        return $room;
    }
}