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

    public function findAvailableSystems(): array
    {
        return $this->createQueryBuilder('a')
            ->where('a.etat = :available')
            ->setParameter('available', EtatAS::Disponible)
            ->orderBy('a.Name', 'ASC') // Optionnel, pour trier les résultats
            ->getQuery()
            ->getResult();
    }
}
