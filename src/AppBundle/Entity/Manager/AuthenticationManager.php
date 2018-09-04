<?php

namespace AppBundle\Entity\Manager;

use AppBundle\Entity\AuthenticationInterface;
use AppBundle\Entity\Repository\AuthenticationRepository;

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
