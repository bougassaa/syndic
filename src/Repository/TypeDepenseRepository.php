<?php

namespace App\Repository;

use App\Entity\Syndic;
use App\Entity\TypeDepense;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<TypeDepense>
 */
class TypeDepenseRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, TypeDepense::class);
    }

    /** @return TypeDepense[] */
    public function getSyndicTypeDepenses(Syndic $syndic): array
    {
        return $this->createQueryBuilder('t')
            ->where('t.syndic = :syndic')
            ->setParameter('syndic', $syndic)
            ->getQuery()
            ->getResult();
    }
}
