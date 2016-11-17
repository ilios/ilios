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

        if ($controller[0] instanceof Controller) {
            $request = $event->getRequest();
            // @todo implement. [ST 2016/11/17]
        }
    }
}
