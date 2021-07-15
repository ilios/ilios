<?php

declare(strict_types=1);

namespace App\EventListener;

use App\Classes\SessionUserInterface;
use App\Service\Config;
use Sentry\Tracing\Transaction;
use Sentry\Tracing\TransactionContext;
use Symfony\Component\HttpKernel\Event\ExceptionEvent;
use Symfony\Component\HttpKernel\Event\RequestEvent;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Sentry\State\Scope;

use function Sentry\init as sentryInit;
use function Sentry\captureException as sentryCaptureException;
use function Sentry\configureScope as sentryConfigureScope;
use function Sentry\startTransaction;

/**
 * Sends errors and performance data to symfony
 */
class SentryCaptureListener
{
    protected bool $errorCaptureEnabled;
    protected TokenStorageInterface $tokenStorage;
    protected Transaction $transaction;

    public function __construct(
        Config $config,
        TokenStorageInterface $tokenStorage,
        string $sentryDSN
    ) {
        $this->errorCaptureEnabled = (bool) $config->get('errorCaptureEnabled');
        if ($this->errorCaptureEnabled) {
            sentryInit([
                'dsn' => $sentryDSN,
                'traces_sample_rate' => 0.1,
            ]);
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

    public function onKernelRequest(RequestEvent $event): void
    {
        $request = $event->getRequest();
        $requestStartTime = $request->server->get('REQUEST_TIME_FLOAT', microtime(true));

        $context = TransactionContext::fromSentryTrace($request->headers->get('sentry-trace', ''));
        $context->setOp('http.server');
        $context->setName(sprintf(
            '%s %s%s%s',
            $request->getMethod(),
            $request->getSchemeAndHttpHost(),
            $request->getBaseUrl(),
            $request->getPathInfo()
        ));
        $context->setStartTimestamp($requestStartTime);
        $this->transaction = startTransaction($context);
    }

    public function onKernelTerminate(): void
    {
        if (isset($this->transaction)) {
            $this->transaction->finish();
        }
    }
}
