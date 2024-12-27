<?php

namespace App\EventSubscriber;

use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpKernel\Event\RequestEvent;
use Symfony\Component\HttpKernel\KernelEvents;
use Symfony\Component\Translation\LocaleSwitcher;

class LocaleSubscriber implements EventSubscriberInterface
{

    public function __construct(private LocaleSwitcher $localeSwitcher)
    {
    }

    public function onKernelRequest(RequestEvent $event): void
    {
        $request = $event->getRequest();
        // Lire la langue depuis la session
        $locale = $request->getSession()
            ->get('_locale', 'fr');
        // voir si un cookie existe
        $locale = $request->cookies->get('_locale', $locale);

        $this->localeSwitcher->setLocale($locale);
        // uniquement pour l'extension intl
        locale_set_default('fr');
    }

    public static function getSubscribedEvents(): array
    {
        return [
            KernelEvents::REQUEST => 'onKernelRequest',
        ];
    }
}
