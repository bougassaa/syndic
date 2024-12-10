<?php

namespace App\Repository;

use App\Entity\Appartement;
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

    public function getAppartementsProprietaires(): array
    {
        $result = $this->createQueryBuilder('a')
            ->select('a.id AS appartement, p.id AS proprietaire')
            ->join('a.proprietaires', 'p')
            ->where('p.leaveAt IS NULL')
            ->getQuery()
            ->getResult();

        $mapping = [];

        foreach ($result as $item) {
            $mapping[$item['appartement']] = $item['proprietaire'];
        }

        return $mapping;
    }
}
