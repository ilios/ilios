<?php
namespace Ilios\CoreBundle\EventListener;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\DependencyInjection\ContainerAwareTrait;
use Symfony\Component\HttpKernel\Event\FilterControllerEvent;

/**
 * Pre-controller event listener that will send tracking data.
 *
 * Class TrackApiUsageListener
 * @package Ilios\CoreBundle\EventListener
 */
class TrackApiUsageListener
{
    use ContainerAwareTrait;

    public function onKernelController(FilterControllerEvent $event)
    {
        if (true !== $this->container->getParameter('ilios_core.enable_tracking')) {
            return;
        }

        $controller = $event->getController();

        /*
         * $controller passed can be either a class or a Closure.
         * This is not usual in Symfony but it may happen.
         * If it is a class, it comes in array format
         * @link http://symfony.com/doc/current/event_dispatcher/before_after_filters.html#creating-an-event-listener
         */
        if (!is_array($controller)) {
            return;
        }

        $controller = $controller[0];

        if ($controller instanceof Controller) {
            $request = $event->getRequest();
            $tracker = $this->container->get('happyr.google_analytics.tracker');
            $path = $request->getRequestUri();
            $host = $request->getHost();
            $title = get_class($controller);
            $data = [
                'dh' => $host,
                'dp' => $path,
                'dt' => $title
            ];
            try {
                $tracker->send($data, 'pageview');
            } catch (\Exception $e) {
                $logger = $this->container->get('logger');
                $logger->error('Failed to send tracking data.', ['exception' => $e]);
            }
        }
    }
}
