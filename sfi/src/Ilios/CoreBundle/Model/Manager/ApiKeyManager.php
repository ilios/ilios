<?php

namespace Ilios\CoreBundle\Entity\Manager;

use Ilios\CoreBundle\Entity\Manager\ApiKeyManagerInterface;
use Ilios\CoreBundle\Entity\ApiKeyInterface;

/**
 * ApiKeyManager
 */
abstract class ApiKeyManager implements ApiKeyManagerInterface
{
    /**
     * @return ApiKeyInterface
     */
     public function createApiKey()
     {
         $class = $this->getClass();

         return new $class();
     }
}
