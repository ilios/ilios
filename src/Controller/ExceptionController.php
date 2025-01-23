<?php

declare(strict_types=1);

namespace App\Controller;

use App\Exception\InvalidInputWithSafeUserMessageException;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\HttpExceptionInterface;
use Throwable;

/**
 * Convert our exceptions into JSON
 * Activated only in production in config/packages/prod/framework.yaml
 */
class ExceptionController
{
    /**
     * Uses the message for InvalidInputWithSafeUserMessageException exceptions
     * otherwise it uses the default HTTP status message for the code
     */
    public function __invoke(Throwable $exception): Response
    {
        $response = new Response();
        $code = Response::HTTP_INTERNAL_SERVER_ERROR;
        if (is_subclass_of($exception, HttpExceptionInterface::class)) {
            $code = $exception->getStatusCode();
            $response->headers->replace($exception->getHeaders());
        }
        $response->setStatusCode($code);

        $safeMessage = Response::$statusTexts[$code] ?? '';
        if ($exception instanceof InvalidInputWithSafeUserMessageException) {
            $safeMessage = $exception->getMessage();
        }

        $json = json_encode([
            'code' => $code,
            'message' => $safeMessage,
        ]);
        $response->setContent($json);
        $response->headers->set('Content-Type', 'application/json');

        return $response;
    }
}
