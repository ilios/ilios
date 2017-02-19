<?php

namespace Ilios\ApiBundle\EventListener;


use Ilios\ApiBundle\Controller\ApiControllerInterface;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Component\HttpKernel\Event\FilterControllerEvent;

/**
 * Class ValidApiObjectListener
 * Prevent some forms of URL injection by validating API
 * endpoints against a whitelist
 *
 * @package Ilios\ApiBundle\EventListener
 */
class ValidApiObjectListener
{
    /** @var array list of valid object names */
    private $validApiObjects;

    /**
     * ValidApiObjectListener constructor.
     * @param string $validApiObjects
     */
    public function __construct($validApiObjects)
    {
        $this->validApiObjects = explode(',', $validApiObjects);
    }

    public function onKernelController(FilterControllerEvent $event)
    {
        $controllers = $event->getController();

        /*
         * $controller passed can be either a class or a Closure.
         * This is not usual in Symfony but it may happen.
         * If it is a class, it comes in array format
         */
        if (!is_array($controllers)) {
            return;
        }
        $controller = $controllers[0];

        if ($controller instanceof ApiControllerInterface) {
            $request = $event->getRequest();
            $object = $request->get('object');

            if (!in_array($object, $this->validApiObjects)) {
                throw new NotFoundHttpException(
                    "{$object} is not a valid API endpoint at " .
                    $request->getRequestUri()
                );
            }
        }
    }
}