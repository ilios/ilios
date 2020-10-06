<?php

declare(strict_types=1);

namespace App\EventListener;

use App\Classes\SessionUserInterface;
use App\Service\Config;
use Symfony\Component\HttpKernel\Event\ExceptionEvent;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Sentry\State\Scope;

use function Sentry\init as sentryInit;
use function Sentry\captureException as sentryCaptureException;
use function Sentry\configureScope as sentryConfigureScope;

/**
 * Sends errors to symfony
 */
class SentryCaptureListener
{
    /**
     * @var bool
     */
    protected $errorCaptureEnabled;

    /**
     * @var TokenStorageInterface
     */
    protected TokenStorageInterface $tokenStorage;

    public function __construct(
        Config $config,
        TokenStorageInterface $tokenStorage,
        string $sentryDSN
    ) {
        $this->errorCaptureEnabled = $config->get('errorCaptureEnabled');
        if ($this->errorCaptureEnabled) {
            sentryInit(['dsn' => $sentryDSN]);
        }
        $this->tokenStorage = $tokenStorage;
    }

    public function onKernelException(ExceptionEvent $event)
    {
        if ($this->errorCaptureEnabled) {
            $exception = $event->getThrowable();
            //don't report 404s to Sentry
            if ($exception instanceof NotFoundHttpException) {
                return;
            }
            $token = $this->tokenStorage->getToken();
            if ($token) {
                $sessionUser = $token->getUser();
                if ($sessionUser instanceof SessionUserInterface) {
                    sentryConfigureScope(function (Scope $scope) use ($sessionUser): void {
                        $scope->setUser([
                            'id' => $sessionUser->getId(),
                        ]);
                    });
                }
            }

            sentryCaptureException($exception);
        }
    }
}
