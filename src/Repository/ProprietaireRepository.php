<?php

namespace App\Repository;

use App\Entity\Proprietaire;
use App\Entity\Syndic;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Proprietaire>
 */
class ProprietaireRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Proprietaire::class);
    }

    /** @return Proprietaire[] */
    public function getSyndicProprietaires(Syndic $syndic): array
    {
        return $this->createQueryBuilder('p')
            ->join('p.possessions', 'po')
            ->join('po.appartement', 'a')
            ->join('a.batiment', 'b')
            ->where('po.leaveAt IS NULL')
            ->andWhere('b.syndic = :syndic')
            ->setParameter('syndic', $syndic)
            ->orderBy('po.appartement', 'ASC')
            ->getQuery()
            ->getResult();
    }

    public function getNumberOfProprietaires(Syndic $syndic): int
    {
        return count($this->getSyndicProprietaires($syndic));
    }
}
