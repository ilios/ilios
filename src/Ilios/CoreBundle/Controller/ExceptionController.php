<?php

namespace Ilios\CoreBundle\Controller;

use Ilios\CoreBundle\Exception\InvalidInputWithSafeUserMessageException;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Debug\Exception\FlattenException;
use Symfony\Component\HttpKernel\Exception\HttpExceptionInterface;
use Symfony\Component\HttpKernel\Log\DebugLoggerInterface;
use Twig_Environment;

/**
 * Convert our exceptions into JSON
 */
class ExceptionController
{
    /**
     * @var Twig_Environment
     */
    protected $twig;

    /**
     * @var bool
     */
    protected $showException;

    /**
     * @var bool
     */
    protected $showFullErrorMessage;

    /**
     * Only show exceptions in the dev environment
     *
     * @param Twig_Environment $twig
     * @param string $environment
     */
    public function __construct(Twig_Environment $twig, $environment)
    {
        $this->twig = $twig;
        $this->showException = $environment === 'dev';
        $this->showFullErrorMessage = in_array($environment, ['dev', 'test']);
    }


    /**
     * Converts an Exception to a Response.
     *
     * If we're in dev mode then we use symfony's excellent built in HTMl error pages
     * but production users will be presented with JSON and a safe error message
     *
     * @inheritdoc
     */
    public function showAction(Request $request, FlattenException $exception, DebugLoggerInterface $logger = null)
    {
        // I don't know what this does - its the way Symfony does it by default though so I'm leaving it
        $showException = $this->showException;

        //If we are in debug mode then show the nice default symfony HTML page with a stacktrace
        if ($showException) {
            $code = $exception->getStatusCode();
            return new Response(
                $this->twig->render(
                    '@Twig/Exception/exception_full.html.twig',
                    [
                        'status_code' => $code,
                        'status_text' => isset(Response::$statusTexts[$code]) ? Response::$statusTexts[$code] : '',
                        'exception' => $exception,
                        'logger' => $logger,
                        'currentContent' => null,
                    ]
                )
            );
        }
        $response = new Response();
        $code = Response::HTTP_INTERNAL_SERVER_ERROR;
        if (is_subclass_of($exception->getClass(), HttpExceptionInterface::class)) {
            $code = $exception->getStatusCode();
            $response->headers->replace($exception->getHeaders());
        }
        $response->setStatusCode($code);

        $safeMessage = isset(Response::$statusTexts[$code]) ? Response::$statusTexts[$code] : '';
        if ($exception instanceof InvalidInputWithSafeUserMessageException) {
            $safeMessage = $exception->getMessage();
        }

        $message = $this->showFullErrorMessage?$exception->getMessage():$safeMessage;

        $json = json_encode([
            'code' => $code,
            'message' => $message
        ]);

        $response->setContent($json);
        $response->headers->set('Content-Type', 'application/json');

        return $response;
    }
}
