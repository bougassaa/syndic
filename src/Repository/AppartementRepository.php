<?php

namespace App\Repository;

use App\Entity\Appartement;
use App\Entity\Syndic;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Appartement>
 */
class AppartementRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Appartement::class);
    }

    /** @return Appartement[] */
    public function getSyndicAppartements(Syndic $syndic)
    {
        return $this->createQueryBuilder('a')
            ->join('a.batiment', 'b')
            ->where('b.syndic = :syndic')
            ->setParameter('syndic', $syndic)
            ->orderBy('b.nom', 'ASC')
            ->addOrderBy('a.numero', 'ASC')
            ->getQuery()
            ->getResult();
    }
}
