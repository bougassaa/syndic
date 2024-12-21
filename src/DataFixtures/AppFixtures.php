<?php

namespace App\DataFixtures;

use App\Entity\Appartement;
use App\Entity\Batiment;
use App\Entity\Proprietaire;
use App\Entity\Syndic;
use App\Entity\TypeDepense;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class AppFixtures extends Fixture
{

    private array $mapping = [
        'GH16' => [
            'A' => 25,
            'B' => 13,
            'C' => 14,
            'D' => 25
        ]
    ];


    public function load(ObjectManager $manager): void
    {
        $monAppartement = null;
        foreach ($this->mapping as $syndicName => $batiments) {
            $syndic = new Syndic();
            $syndic->setNom($syndicName);

            $manager->persist($syndic);
            foreach ($batiments as $batimentName => $noApparts) {
                $batiment = new Batiment();
                $batiment->setNom($batimentName);
                $batiment->setSyndic($syndic);

                $manager->persist($batiment);

                for ($noAppart = 1; $noAppart <= $noApparts; $noAppart++) {
                    $appartement = new Appartement();
                    $appartement->setNumero($noAppart);
                    $appartement->setBatiment($batiment);

                    $manager->persist($appartement);

                    if ($batimentName == 'B' && $noAppart == 9) {
                        $monAppartement = $appartement;
                    }
                }
            }
        }

        $proprietaire = new Proprietaire();
        $proprietaire->setNom('BOUGASSAA');
        $proprietaire->setPrenom('Amine');
        $proprietaire->setBeginAt(new \DateTime('2024-01-03'));
        $proprietaire->setAppartement($monAppartement);

        $manager->persist($proprietaire);

        $typeDepense = new TypeDepense();
        $typeDepense->setLabel('Salaire jardinier');
        $typeDepense->setMontant(1800);
        $typeDepense->setFrequence(TypeDepense::MENSUELLE);
        $typeDepense->setSyndic($syndic);

        $manager->persist($typeDepense);

        $manager->flush();
    }
}
