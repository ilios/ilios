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
     * @deprecated 
     */
    public function findAuthenticationBy(
        array $criteria,
        array $orderBy = null
    ) {
        return $this->findOneBy($criteria, $orderBy);
    }

    /**
     * @deprecated 
     */
    public function findAuthenticationsBy(
        array $criteria,
        array $orderBy = null,
        $limit = null,
        $offset = null
    ) {
        return $this->findBy($criteria, $orderBy, $limit, $offset);
    }

    /**
     * @param string $username
     * @return AuthenticationInterface
     */
    public function findAuthenticationByUsername($username)
    {
        $username = strtolower($username);
        return $this->getRepository()->findOneByUsername($username);
    }

    /**
     * @deprecated
     */
    public function updateAuthentication(
        AuthenticationInterface $authentication,
        $andFlush = true,
        $forceId = false
    ) {
        $this->update($authentication, $andFlush, $forceId);
    }

    /**
     * @deprecated
     */
    public function deleteAuthentication(
        AuthenticationInterface $authentication
    ) {
        $this->delete($authentication);
    }

    /**
     * {@inheritdoc}
     */
    public function createAuthentication()
    {
        return $this->create();
    }
}
