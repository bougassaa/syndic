<?php

namespace App\EventListener;

use App\Entity\Possession;
use Doctrine\Bundle\DoctrineBundle\Attribute\AsEntityListener;
use Doctrine\ORM\Event\PostPersistEventArgs;
use Doctrine\ORM\Events;
use Psr\Log\LoggerInterface;

#[AsEntityListener(event: Events::postPersist, method: 'postPersist', entity: Possession::class)]
class PossessionListener
{

    public function __construct(private LoggerInterface $logger)
    {
    }

    public function postPersist(Possession $possession, PostPersistEventArgs $args): void
    {
        $manager = $args->getObjectManager();
        $possession->getAppartement()
            ->getPossessions()
            ->forAll(
                function (int $index, Possession $appartPossession) use ($possession, $manager) {
                    if ($appartPossession !== $possession) {
                        $appartPossession->setLeaveAt($possession->getBeginAt());
                        $manager->flush();
                    }
                }
            );
    }

}
