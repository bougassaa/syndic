<?php

namespace App\Repository;

use App\Entity\Cotisation;
use App\Entity\Tarif;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Cotisation>
 */
class CotisationRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Cotisation::class);
    }

    public function getSumOfYear(Tarif $tarif): float
    {
        return $this->createQueryBuilder('c')
            ->select('SUM(c.montant)')
            ->where('c.tarif = :tarif')
            ->setParameter('tarif', $tarif)
            ->getQuery()
            ->getSingleScalarResult();
    }

}
