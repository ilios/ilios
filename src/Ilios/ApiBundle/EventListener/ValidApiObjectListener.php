<?php

namespace Ilios\ApiBundle\EventListener;

use Ilios\ApiBundle\Controller\ApiControllerInterface;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

use Symfony\Component\HttpKernel\Event\FilterControllerEvent;

/**
 * Class ValidApiObjectListener
 * Prevent some forms of URL injection by validating API
 * endpoints against a white list
 *
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
        $arr = explode(',', $validApiObjects);
        //YAML leaves whitespace sometimes.
        // So we strip it out
        $arr = array_map('trim', $arr);

        $this->validApiObjects = $arr;
    }

    /**
     * Search and validate requests
     *
     * @param FilterControllerEvent $event
     */
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
