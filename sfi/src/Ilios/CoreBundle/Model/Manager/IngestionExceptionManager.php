<?php

namespace Ilios\CoreBundle\Model\Manager;

use Ilios\CoreBundle\Model\Manager\IngestionExceptionManagerInterface;
use Ilios\CoreBundle\Model\IngestionExceptionInterface;

/**
 * IngestionExceptionManager
 */
abstract class IngestionExceptionManager implements IngestionExceptionManagerInterface
{
    /**
    * @return IngestionExceptionInterface
    */
    public function createIngestionException()
    {
        $class = $this->getClass();

        return new $class();
    }
}
