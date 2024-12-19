<?php

namespace App\Repository;

use App\Entity\AcquisitionSystem;
use App\Model\EtatAS;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use App\Controller\RoomsController;
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
        $result =  $this->createQueryBuilder('a')
            ->andWhere('a.Name = :name')
            ->setParameter('name', $name)
            ->getQuery()
            ->getOneOrNullResult();

        if ($result instanceof AcquisitionSystem) {
            return $result;
        }

        return null;
    }

    /**
     * @return AcquisitionSystem[] Returns an array of AcquisitionSystem objects
     */
    public function findAvailableSystems(): array
    {
        $result = $this->createQueryBuilder('a')
            ->where('a.etat = :available')
            ->setParameter('available', EtatAS::Disponible)
            ->orderBy('a.Name', 'ASC') // Optionnel, pour trier les résultats
            ->getQuery()
            ->getResult();
        /** @var AcquisitionSystem[] $result */
        return $result;
    }

    /**
     * @return AcquisitionSystem[] Un tableau contenant des entités AcquisitionSystem avec un système d'acquisition "installé".
     */
    public function sortAcquisitionSystem() : array
    {
        /** @var AcquisitionSystem[] $acquisitionSystem */
        $acquisitionSystem = $this->createQueryBuilder('b')
            ->orderBy('b.Name', 'ASC')
            ->getQuery()
            ->getResult();
        return $acquisitionSystem;
    }

    public function findInstalledSystems(): array
    {
        $result = $this->createQueryBuilder('a')
            ->where('a.etat = :installed')
            ->setParameter('installed', EtatAS::Installer)
            ->orderBy('a.Name', 'ASC') // Optionnel, pour trier les résultats
            ->getQuery()
            ->getResult();
        /** @var AcquisitionSystem[] $result */
        return $result;
    }
}

