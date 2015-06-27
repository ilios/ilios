<?php

namespace Ilios\CoreBundle\Entity\Manager;

use Ilios\CoreBundle\Entity\AuthenticationInterface;

/**
 * Class AuthenticationManagerInterface
 * @package Ilios\CoreBundle\Entity\Manager
 */
interface AuthenticationManagerInterface
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
     * @param array $criteria
     * @param array $orderBy
     * @param integer $limit
     * @param integer $offset
     *
     * @return ArrayCollection|AuthenticationInterface[]
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
     * @return string
     */
    public function getClass();

    /**
     * @return AuthenticationInterface
     */
    public function createAuthentication();
}
