<?php

namespace App\Repository;

use App\Entity\Depense;
use App\Entity\Syndic;
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
    public function getDepensesPerYear(int|false $year, Syndic $syndic)
    {
        $qb = $this->createQueryBuilder('d');

        if ($year !== false) {
            $qb->where('YEAR(d.paidAt) = :year')
                ->setParameter('year', $year);
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

    public function getFirstOldDepense(Syndic $syndic): ?Depense
    {
        return $this->createQueryBuilder('d')
            ->andWhere('d.syndic = :syndic')
            ->setParameter('syndic', $syndic)
            ->orderBy('d.paidAt', 'ASC')
            ->setMaxResults(1)
            ->getQuery()
            ->getOneOrNullResult();
    }

    public function getLastNewDepense(Syndic $syndic): ?Depense
    {
        return $this->createQueryBuilder('d')
            ->andWhere('d.syndic = :syndic')
            ->setParameter('syndic', $syndic)
            ->orderBy('d.paidAt', 'DESC')
            ->setMaxResults(1)
            ->getQuery()
            ->getOneOrNullResult();
    }
}
