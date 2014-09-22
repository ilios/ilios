<?php

namespace Ilios\CoreBundle\Model\Manager;

use Ilios\CoreBundle\Model\Manager\IlmSessionFacetManagerInterface;
use Ilios\CoreBundle\Model\IlmSessionFacetInterface;

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
