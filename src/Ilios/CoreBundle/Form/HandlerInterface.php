<?php

namespace Ilios\CoreBundle\Form;

/**
 * Interface HandlerInterface
 * @package Ilios\CoreBundle\Form
 */
interface HandlerInterface
{
    /**
     * @param array $parameters
     * @return object
     */
    public function post(array $parameters);

    /**
     * @param object $entity
     * @param array $parameters
     *
     * @return object
     */
    public function put($entity, array $parameters);

    /**
     * @param object $entity
     * @param array $parameters
     *
     * @return object
     */
    public function patch($entity, array $parameters);
}
