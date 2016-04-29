<?php

namespace Ilios\CoreBundle\Entity\Manager;

/**
 * Interface ManagerInterface
 * @package Ilios\CoreBundle\Entity\Manager
 */
interface ManagerInterface
{
    /**
     * @return string
     */
    public function getClass();

    /**
     * Flush and clear the entity manager when doing bulk updates
     */
    public function flushAndClear();

    /**
     * Flush the entity manager when doing bulk updates
     */
    public function flush();
}
