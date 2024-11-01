<?php

declare(strict_types=1);

namespace App\EventSubscriber;

use App\Service\Config;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpKernel\Event\RequestEvent;
use Symfony\Component\HttpKernel\KernelEvents;

class RedirectInsecureConnections implements EventSubscriberInterface
{
    protected bool $requireSecureConnection;

    public function __construct(Config $config)
    {
        $this->requireSecureConnection = $config->get('requireSecureConnection');
    }

    public static function getSubscribedEvents(): array
    {
        // return the subscribed events, their methods and priorities
        return [
            KernelEvents::REQUEST => [
                ['checkAndRedirect', 10],
            ],
        ];
    }

    /**
     * If we are enforcing a secure connection redirect any users who land
     * here accidentally.
     */
    public function checkAndRedirect(RequestEvent $event): void
    {
        if ($this->requireSecureConnection && $event->isMainRequest()) {
            $request = $event->getRequest();
            if (!$request->isSecure()) {
                $path = $request->getPathInfo();
                $host = $request->getHttpHost();
                $url = 'https://' . $host . $path;

                $response = new RedirectResponse($url);
                $event->setResponse($response);
            }
        }
    }
}
