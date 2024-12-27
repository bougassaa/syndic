<?php

namespace App\Repository;

use App\Entity\Garage;
use App\Entity\Syndic;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Garage>
 */
class GarageRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Garage::class);
    }

    /** @return Garage[] */
    public function getSyndicGarages(Syndic $syndic): array
    {
        return $this->createQueryBuilder('g')
            ->where('g.syndic = :syndic')
            ->setParameter('syndic', $syndic)
            ->orderBy('g.nom', 'ASC')
            ->getQuery()
            ->getResult();
    }
}
