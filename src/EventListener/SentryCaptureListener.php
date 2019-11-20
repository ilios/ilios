<?php
namespace App\EventListener;

use App\Service\Config;
use Symfony\Component\HttpKernel\Event\GetResponseForExceptionEvent;

use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
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
            $exception = $event->getException();
            //don't report 404s to Sentry
            if ($exception instanceof NotFoundHttpException) {
                return;
            }
            sentryCaptureException($exception);
        }
    }
}
