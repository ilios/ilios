<?php

namespace Ilios\CoreBundle\Model\Manager;

use Ilios\CoreBundle\Model\Manager\OfferingManagerInterface;
use Ilios\CoreBundle\Model\OfferingInterface;

/**
 * OfferingManager
 */
abstract class OfferingManager implements OfferingManagerInterface
{
    /**
    * @return OfferingInterface
    */
    public function createOffering()
    {
        $class = $this->getClass();

        return new $class();
    }
}
