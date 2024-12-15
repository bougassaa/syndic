<?php

namespace App\Repository;

use App\Entity\Depense;
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

    public function getDepensesPerYear(int|false $year)
    {
        $qb = $this->createQueryBuilder('d');

        if ($year !== false) {
            $qb->where('YEAR(d.paidAt) = :year')
                ->setParameter('year', $year);
        }

        return $qb->orderBy('d.paidAt', 'DESC')
            ->addOrderBy('d.id',  'DESC')
            ->getQuery()
            ->getResult();
    }

    public function getFirstOldDepense(): ?Depense
    {
        return $this->createQueryBuilder('d')
            ->orderBy('d.paidAt', 'ASC')
            ->setMaxResults(1)
            ->getQuery()
            ->getOneOrNullResult();
    }

    public function getLastNewDepense(): ?Depense
    {
        return $this->createQueryBuilder('d')
            ->orderBy('d.paidAt', 'DESC')
            ->setMaxResults(1)
            ->getQuery()
            ->getOneOrNullResult();
    }
}
