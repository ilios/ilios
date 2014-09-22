<?php

namespace Ilios\CoreBundle\Entity\Manager;

use Ilios\CoreBundle\Entity\Manager\IngestionExceptionManagerInterface;
use Ilios\CoreBundle\Entity\IngestionExceptionInterface;

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
