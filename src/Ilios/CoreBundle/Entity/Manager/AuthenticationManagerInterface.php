<?php

namespace Ilios\CoreBundle\Entity\Manager;

use Ilios\CoreBundle\Entity\AuthenticationInterface;

/**
 * Class AuthenticationManagerInterface
 * @package Ilios\CoreBundle\Entity\Manager
 */
interface AuthenticationManagerInterface extends ManagerInterface
{
    /**
     * @param array $criteria
     * @param array $orderBy
     *
     * @return AuthenticationInterface
     */
    public function findAuthenticationBy(
        array $criteria,
        array $orderBy = null
    );

    /**
     * @param string $username
     *
     * @return AuthenticationInterface
     */
    public function findAuthenticationByUsername($username);

    /**
     * @param array $criteria
     * @param array $orderBy
     * @param integer $limit
     * @param integer $offset
     *
     * @return AuthenticationInterface[]
     */
    public function findAuthenticationsBy(
        array $criteria,
        array $orderBy = null,
        $limit = null,
        $offset = null
    );

    /**
     * @param AuthenticationInterface $authentication
     * @param bool $andFlush
     * @param bool $forceId
     */
    public function updateAuthentication(
        AuthenticationInterface $authentication,
        $andFlush = true,
        $forceId = false
    );

    /**
     * @param AuthenticationInterface $authentication
     */
    public function deleteAuthentication(
        AuthenticationInterface $authentication
    );

    /**
     * @return AuthenticationInterface
     */
    public function createAuthentication();
}
