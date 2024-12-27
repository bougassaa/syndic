<?php

namespace App\Repository;

use App\Entity\Appartement;
use App\Entity\Proprietaire;
use App\Entity\Syndic;
use App\Entity\Tarif;
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
            ->where('b.syndic = :syndic')
            ->setParameter('syndic', $syndic)
            ->orderBy('p.isSystem', 'ASC')
            ->addOrderBy('CASE WHEN po.leaveAt IS NOT NULL THEN 1 ELSE 0 END', 'ASC')
            ->addOrderBy('po.appartement', 'ASC')
            ->getQuery()
            ->getResult();
    }

    public function getProprietaireInTarif(Tarif $tarif, Appartement $appartement): ?Proprietaire
    {
        return $this->createQueryBuilder('p')
            ->join('p.possessions', 'po')
            ->where('po.appartement = :appartement')
            ->andWhere('po.beginAt < :endTarif')
            ->setParameter('appartement', $appartement)
            ->setParameter('endTarif', $tarif->getFinPeriode())
            ->orderBy('po.beginAt', 'DESC')
            ->setMaxResults(1)
            ->getQuery()
            ->getOneOrNullResult();
    }
}
