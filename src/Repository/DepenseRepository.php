<?php

namespace App\Repository;

use App\Entity\Depense;
use App\Entity\Syndic;
use App\Entity\Tarif;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Depense>
 */
class DepenseRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Depense::class);
    }

    /** @return Depense[] */
    public function getDepensesPerPeriode(?Tarif $tarifPeriode, Syndic $syndic): array
    {
        $qb = $this->createQueryBuilder('d');

        if ($tarifPeriode) {
            $qb->where('d.paidAt BETWEEN :start AND :end')
                ->setParameter('start', $tarifPeriode->getDebutPeriode())
                ->setParameter('end', $tarifPeriode->getFinPeriode());
        }

        return $qb->andWhere('d.syndic = :syndic')
            ->setParameter('syndic', $syndic)
            ->orderBy('d.paidAt', 'DESC')
            ->addOrderBy('d.id',  'DESC')
            ->getQuery()
            ->getResult();
    }

    public function getTotalDepensesPerYear(int $year, Syndic $syndic): float
    {
        return $this->createQueryBuilder('d')
            ->select('SUM(d.montant)')
            ->where('YEAR(d.paidAt) = :year')
            ->andWhere('d.syndic = :syndic')
            ->setParameter('year', $year)
            ->setParameter('syndic', $syndic)
            ->getQuery()
            ->getSingleScalarResult() ?? 0;
    }
}
