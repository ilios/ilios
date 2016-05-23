<?php

namespace Ilios\CoreBundle\Entity\Manager;

use Ilios\CoreBundle\Entity\AuthenticationInterface;

/**
 * Class AuthenticationManager
 * @package Ilios\CoreBundle\Entity\Manager
 */
class AuthenticationManager extends BaseManager
{
    /**
     * @param string $username
     * @return AuthenticationInterface
     */
    public function findAuthenticationByUsername($username)
    {
        $username = strtolower($username);
        return $this->getRepository()->findOneByUsername($username);
    }
}
