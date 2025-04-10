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
     * Find an Acquisition System by its name.
     *
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
     * Get all available acquisition systems.
     *
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
     * Find acquisition systems based on filters.
     *
     * @param array<string, mixed> $data
     * @return AcquisitionSystem[]
     */
    public function findByFilters(array $data): array
    {
        $queryBuilder = $this->createQueryBuilder('a');

        // Filter by status
        if (!empty($data['etat'])) {
            $queryBuilder->where('a.etat = :etat')
                ->setParameter('etat', $data['etat']);
        }

        // Filter by system name
        if (!empty($data['Name'] && is_string($data['Name']))) {
            $queryBuilder->andWhere('a.Name LIKE :Name')
                ->setParameter('Name', '%' . $data['Name'] . '%');
        }

        $result = $queryBuilder->orderBy('a.Name', 'ASC')->getQuery()->getResult();

        /** @var AcquisitionSystem[] $result */
        return $result;
    }

    /**
     * Get all installed acquisition systems.
     *
     * @return AcquisitionSystem[] Returns an array of AcquisitionSystem objects
     */
    public function findInstalledSystems(): array
    {
        $result = $this->createQueryBuilder('a')
            ->where('a.etat = :installed')
            ->setParameter('installed', EtatAS::Installer)
            ->orderBy('a.Name', 'ASC') // Optional, to sort the results
            ->getQuery()
            ->getResult();

        /** @var AcquisitionSystem[] $result */
        return $result;
    }
}