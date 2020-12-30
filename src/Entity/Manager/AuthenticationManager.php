<?php

declare(strict_types=1);

namespace App\Entity\Manager;

use App\Entity\AuthenticationInterface;
use App\Repository\AuthenticationRepository;

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
    /**
     * @return string[]
     */
    public function getUsernames()
    {
        /** @var AuthenticationRepository $repository */
        $repository = $this->getRepository();
        return $repository->getUsernames();
    }
}
