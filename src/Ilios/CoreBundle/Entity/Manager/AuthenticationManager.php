<?php

namespace Ilios\CoreBundle\Entity\Manager;

use Ilios\CoreBundle\Entity\AuthenticationInterface;
use Ilios\CoreBundle\Entity\Repository\AuthenticationRepository;

/**
 * Class AuthenticationManager
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
        /** @var AuthenticationRepository $repository */
        $repository = $this->getRepository();
        return $repository->findOneByUsername($username);
    }
}
