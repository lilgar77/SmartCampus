<?php

namespace App\Repository;

use App\Entity\AcquisitionSystem;
use App\Model\EtatAS;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<AcquisitionSystem>
 */
class AcquisitionSystemRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, AcquisitionSystem::class);
    }

    /**
     * @param string $name
     * @return AcquisitionSystem|null
     */
    public function findASByName(string $name): ?AcquisitionSystem
    {
        $result = $this->createQueryBuilder('a')
            ->andWhere('a.Name = :name')
            ->setParameter('name', $name)
            ->getQuery()
            ->getOneOrNullResult();

        return $result instanceof AcquisitionSystem ? $result : null;
    }

    /**
     * @return AcquisitionSystem[] Returns an array of AcquisitionSystem objects
     */
    public function findAvailableSystems(): array
    {
        $result = $this->createQueryBuilder('a')
            ->where('a.etat = :available')
            ->setParameter('available', EtatAS::Disponible)
            ->orderBy('a.Name', 'ASC')
            ->getQuery()
            ->getResult();

        /** @var AcquisitionSystem[] $result */
        return $result;
    }

    /**
     * @param array<string, mixed> $data
     * @return AcquisitionSystem[]
     */
    public function findByFilters(array $data): array
    {
        $queryBuilder = $this->createQueryBuilder('a');

        // Filtrer par état
        if (!empty($data['etat'])) {
            $queryBuilder->where('a.etat = :etat')
                ->setParameter('etat', $data['etat']);
        }

        // Filtrer par nom du SA
        if (!empty($data['Name'] && is_string($data['Name']))) {
            $queryBuilder->andWhere('a.Name LIKE :Name')
                ->setParameter('Name', '%' . $data['Name'] . '%');
        }

        $result = $queryBuilder->orderBy('a.Name', 'ASC')->getQuery()->getResult();

        /** @var AcquisitionSystem[] $result */
        return $result;
    }

}

