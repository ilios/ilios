<?php
namespace Ilios\CoreBundle\EventSubscriber;

use Ilios\CoreBundle\Service\Config;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpKernel\Event\GetResponseEvent;
use Symfony\Component\HttpKernel\KernelEvents;

class RedirectInsecureConnections implements EventSubscriberInterface
{
    /**
     * @var string
     */
    protected $requireSecureConnection;

    public function __construct(Config $config)
    {
        $this->requireSecureConnection = $config->get('requireSecureConnection');
    }

    public static function getSubscribedEvents()
    {
        // return the subscribed events, their methods and priorities
        return array(
            KernelEvents::REQUEST => array(
                array('checkAndRedirect', 10),
            )
        );
    }

    /**
     * If we are enforcing a secure connection redirect any users who land
     * here accidentally.
     *
     * @param GetResponseEvent $event
     */
    public function checkAndRedirect(GetResponseEvent $event)
    {
        if ($this->requireSecureConnection && $event->isMasterRequest()) {
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
