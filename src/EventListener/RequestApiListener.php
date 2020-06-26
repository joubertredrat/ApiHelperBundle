<?php

declare(strict_types=1);

namespace RedRat\ApiHelperBundle\EventListener;

use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpKernel\Event\RequestEvent;
use Symfony\Component\HttpKernel\KernelEvents;

class RequestApiListener implements EventSubscriberInterface
{
    public static function getSubscribedEvents()
    {
        return [
            KernelEvents::REQUEST => [
                ['onKernelRequest', 10],
            ],
        ];
    }

    public function onKernelRequest(RequestEvent $event)
    {

    }
}
