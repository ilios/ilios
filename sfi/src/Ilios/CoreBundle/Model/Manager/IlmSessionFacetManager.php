<?php

namespace Ilios\CoreBundle\Entity\Manager;

use Ilios\CoreBundle\Entity\Manager\IlmSessionFacetManagerInterface;
use Ilios\CoreBundle\Entity\IlmSessionFacetInterface;

/**
 * IlmSessionFacetManager
 */
abstract class IlmSessionFacetManager implements IlmSessionFacetManagerInterface
{
    /**
     * @return IlmSessionFacetInterface
     */
     public function createIlmSessionFacet()
     {
         $class = $this->getClass();

         return new $class();
     }
}
