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

    public function getCurrentTarif(Syndic $syndic): ?Tarif
    {
        return $this->createQueryBuilder('t')
            ->where('t.syndic = :syndic')
            ->andWhere(':now BETWEEN t.debutPeriode AND t.finPeriode')
            ->setParameter('syndic', $syndic)
            ->setParameter('now', date('Y-m-d'))
            ->getQuery()
            ->getOneOrNullResult();
    }

    public function getMaxYearTarif(Syndic $syndic): ?Tarif
    {
        return $this->createQueryBuilder('t')
            ->where('t.syndic = :syndic')
            ->setParameter('syndic', $syndic)
            ->orderBy('t.finPeriode', 'DESC')
            ->setMaxResults(1)
            ->getQuery()
            ->getOneOrNullResult();
    }

    public function getMinYearTarif(Syndic $syndic): ?Tarif
    {
        return $this->createQueryBuilder('t')
            ->where('t.syndic = :syndic')
            ->setParameter('syndic', $syndic)
            ->orderBy('t.debutPeriode', 'ASC')
            ->setMaxResults(1)
            ->getQuery()
            ->getOneOrNullResult();
    }

    /** @return Tarif[] */
    public function getSyndicTarifs(Syndic $syndic): array
    {
        return $this->createQueryBuilder('t')
            ->where('t.syndic = :syndic')
            ->setParameter('syndic', $syndic)
            ->orderBy('t.debutPeriode', 'DESC')
            ->getQuery()
            ->getResult();
    }
}
