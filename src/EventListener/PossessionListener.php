<?php

namespace App\EventListener;

use App\Entity\Possession;
use Doctrine\Bundle\DoctrineBundle\Attribute\AsEntityListener;
use Doctrine\ORM\Event\PostPersistEventArgs;
use Doctrine\ORM\Events;

#[AsEntityListener(event: Events::postPersist, method: 'postPersist', entity: Possession::class)]
class PossessionListener
{

    public function postPersist(Possession $possession, PostPersistEventArgs $args): void
    {
        $manager = $args->getObjectManager();
        $possession->getAppartement()
            ->getPossessions()
            ->forAll(
                function (int $index, Possession $appartPossession) use ($possession, $manager) {
                    if ($appartPossession !== $possession && !$possession->getLeaveAt()) {
                        $appartPossession->setLeaveAt($possession->getBeginAt());
                        $manager->flush();
                    }
                }
            );
    }

}
