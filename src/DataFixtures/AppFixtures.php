<?php

namespace App\DataFixtures;

use App\Entity\Admin;
use App\Entity\Appartement;
use App\Entity\Batiment;
use App\Entity\Possession;
use App\Entity\Proprietaire;
use App\Entity\Syndic;
use App\Entity\TypeDepense;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class AppFixtures extends Fixture
{

    private array $mapping = [
        Syndic::GH_16 => [
            'A' => 25,
            'B' => 13,
            'C' => 14,
            'D' => 25
        ]
    ];

    public function __construct(private UserPasswordHasherInterface $userPasswordHasher)
    {
    }


    public function load(ObjectManager $manager): void
    {
        // super admin
        $admin = new Admin();
        $admin->setRoles(['ROLE_ADMIN', 'ROLE_SUPER_ADMIN']);
        $admin->setPassword(
            $this->userPasswordHasher->hashPassword($admin, 'amine2019')
        );

        // create doha proprietaire
        $doha = new Proprietaire();
        $doha->setNom('SOCIÉTÉ');
        $doha->setPrenom('Doha');
        $doha->setSystem(true);

        $manager->persist($doha);

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

                    // attach to doha
                    $possession = new Possession();
                    $possession->setAppartement($appartement);
                    $possession->setBeginAt(new \DateTime('2016-01-01'));
                    $doha->addPossession($possession);
                }
            }
        }

        $typeDepense = new TypeDepense();
        $typeDepense->setLabel('Salaire jardinier');
        $typeDepense->setMontant(1800);
        $typeDepense->setFrequence(TypeDepense::MENSUELLE);
        $typeDepense->setSyndic($syndic);

        $manager->persist($typeDepense);

        $manager->flush();
    }
}
