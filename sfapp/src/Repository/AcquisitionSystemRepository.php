<?php

namespace App\Repository;

use App\Entity\AcquisitionSystem;
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
}
