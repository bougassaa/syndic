<?php

namespace App\Repository;

use App\Entity\Batiment;
use App\Entity\Syndic;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Batiment>
 */
class BatimentRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Batiment::class);
    }

    /** @return Batiment[] */
    public function getSyndicBatiments(Syndic $syndic): array
    {
        return $this->createQueryBuilder('b')
            ->where('b.syndic = :syndic')
            ->setParameter('syndic', $syndic)
            ->orderBy('b.nom', 'ASC')
            ->getQuery()
            ->getResult();
    }
}
