<?php

namespace Ilios\CoreBundle\Model\Manager;

use Ilios\CoreBundle\Model\Manager\ApiKeyManagerInterface;
use Ilios\CoreBundle\Model\ApiKeyInterface;

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
