<?php

namespace App\Service;

use App\Entity\Syndic;
use App\Repository\SyndicRepository;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpFoundation\Session\SessionInterface;

class SyndicSessionResolver
{

    private SessionInterface $session;

    public function __construct(private SyndicRepository $syndicRepository, RequestStack $requestStack)
    {
        $this->session = $requestStack->getSession();
    }

    public function getSelectedSyndic(): Syndic
    {
        return $this->syndicRepository->findOneBy(['nom' => Syndic::GH_16]);
    }

}