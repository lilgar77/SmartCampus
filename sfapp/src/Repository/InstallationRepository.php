<?php

namespace App\Repository;

use App\Entity\Installation;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * Repository for the Installation entity.
 *
 * @extends ServiceEntityRepository<Installation>
 */
class InstallationRepository extends ServiceEntityRepository
{
    /**
     * Constructor to initialize the repository with the ManagerRegistry.
     *
     * @param ManagerRegistry $registry The Doctrine ManagerRegistry instance.
     */
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Installation::class);
    }
}