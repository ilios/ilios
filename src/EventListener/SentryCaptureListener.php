<?php
namespace App\EventListener;

use App\Service\Config;
use Symfony\Component\HttpKernel\Event\GetResponseForExceptionEvent;

use function Sentry\init as sentryInit;
use function Sentry\captureException as sentryCaptureException;

/**
 * Sends errors to symfony
 */
class SentryCaptureListener
{
    /**
     * @var bool
     */
    protected $errorCaptureEnabled;

    public function __construct(
        Config $config,
        string $sentryDSN
    ) {
        $this->errorCaptureEnabled = $config->get('errorCaptureEnabled');
        if ($this->errorCaptureEnabled) {
            sentryInit(['dsn' => $sentryDSN]);
        }
    }

    public function onKernelException(GetResponseForExceptionEvent $event)
    {
        if ($this->errorCaptureEnabled) {
            sentryCaptureException($event->getException());
        }
    }
}
