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
    private array $batiments = ['A', 'B', 'C', 'D'];

    private array $appartements = [1, 2, 3, 4, 5, 6, 7, 8, 9, 10];

    public function load(ObjectManager $manager): void
    {
        $syndic = new Syndic();
        $syndic->setNom('GH16');

        $manager->persist($syndic);

        $monAppartement = null;
        foreach ($this->batiments as $name) {
            $batiment = new Batiment();
            $batiment->setNom($name);
            $batiment->setSyndic($syndic);

            $manager->persist($batiment);

            foreach ($this->appartements as $number) {
                $appartement = new Appartement();
                $appartement->setNumero($number);
                $appartement->setBatiment($batiment);

                $manager->persist($appartement);

                if ($name == 'B' && $number == 9) {
                    $monAppartement = $appartement;
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
