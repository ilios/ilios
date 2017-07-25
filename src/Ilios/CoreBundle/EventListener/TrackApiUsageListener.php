<?php
namespace Ilios\CoreBundle\EventListener;

use Happyr\GoogleAnalyticsBundle\Service\Tracker;
use Psr\Log\LoggerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpKernel\Event\FilterControllerEvent;

/**
 * Pre-controller event listener that will send tracking data.
 *
 * Class TrackApiUsageListener
 */
class TrackApiUsageListener
{
    /**
     * @var bool
     */
    protected $isTrackingEnabled;

    /**
     * @var Tracker
     */
    protected $tracker;

    /**
     * @var LoggerInterface
     */
    protected $logger;

    /**
     * @param bool $isTrackingEnabled
     * @param Tracker $tracker
     * @param LoggerInterface $logger
     */
    public function __construct($isTrackingEnabled, Tracker $tracker, LoggerInterface $logger)
    {
        $this->isTrackingEnabled = $isTrackingEnabled;
        $this->tracker = $tracker;
        $this->logger = $logger;
    }

    public function onKernelController(FilterControllerEvent $event)
    {
        if (! $this->isTrackingEnabled) {
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
            $path = $request->getRequestUri();
            $host = $request->getHost();
            $title = get_class($controller);
            $data = [
                'dh' => $host,
                'dp' => $path,
                'dt' => $title
            ];
            try {
                $this->tracker->send($data, 'pageview');
            } catch (\Exception $e) {
                $this->logger->error('Failed to send tracking data.', ['exception' => $e]);
            }
        }
    }
}
