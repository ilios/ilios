<?php
namespace Ilios\CoreBundle\EventListener;

use Happyr\GoogleAnalyticsBundle\Service\Tracker;
use Ilios\AuthenticationBundle\Classes\SessionUserInterface;
use Ilios\CoreBundle\Service\Config;
use Psr\Log\LoggerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpKernel\Event\FilterControllerEvent;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;

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
     * @var string
     */
    protected $trackingCode;

    /**
     * @var Tracker
     */
    protected $tracker;

    /**
     * @var LoggerInterface
     */
    protected $logger;

    /**
     * @var integer
     */
    protected $userId;

    /**
     * @param Config $config
     * @param Tracker $tracker
     * @param LoggerInterface $logger
     * @param TokenStorageInterface $tokenStorage
     */
    public function __construct(
        Config $config,
        Tracker $tracker,
        LoggerInterface $logger,
        TokenStorageInterface $tokenStorage
    ) {
        $this->isTrackingEnabled = $config->get('enable_tracking');
        $this->trackingCode = $config->get('tracking_code');
        $this->tracker = $tracker;
        $this->logger = $logger;
        if (null !== $tokenStorage &&
            null !== $tokenStorage->getToken()
        ) {
            /** @var SessionUserInterface $sessionUser */
            $sessionUser = $tokenStorage->getToken()->getUser();
            if ($sessionUser instanceof SessionUserInterface) {
                $this->userId = $sessionUser->getId();
            }
        }
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
            $clientIp = $request->getClientIp();
            $title = get_class($controller);
            $data = [
                'tid' => $this->trackingCode,
                'dh' => $host,
                'dp' => $path,
                'dt' => $title,
            ];

            if ($clientIp) {
                $data['uip'] = $clientIp;
            }

            if ($this->userId) {
                $data['uid'] = $this->userId;
            }

            try {
                $this->tracker->send($data, 'pageview');
            } catch (\Exception $e) {
                $this->logger->error('Failed to send tracking data.', ['exception' => $e]);
            }
        }
    }
}
