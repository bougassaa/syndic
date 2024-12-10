<?php

namespace App\Repository;

use App\Entity\Syndic;
use App\Entity\Tarif;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Tarif>
 */
class TarifRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Tarif::class);
    }

    public function getThisYearTarif(Syndic $syndic): ?Tarif
    {
        return $this->createQueryBuilder('t')
            ->select('t')
            ->where('t.year = :year')
            ->andWhere('t.syndic = :syndic')
            ->setParameter('year', date('Y'))
            ->setParameter('syndic', $syndic)
            ->getQuery()
            ->getOneOrNullResult();
    }
}
