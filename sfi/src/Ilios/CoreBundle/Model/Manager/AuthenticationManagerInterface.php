<?php

namespace Ilios\CoreBundle\Model\Manager;

use Doctrine\Common\Collections\Collection;
use Ilios\CoreBundle\Model\AuthenticationInterface;

/**
 * Interface AuthenticationManagerInterface
 */
interface AuthenticationManagerInterface
{
    /** 
     *@return AuthenticationInterface
     */
    public function createAuthentication();

    /**
     * @param array $criteria
     * @param array $orderBy
     *
     * @return AuthenticationInterface
     */
    public function findAuthenticationBy(array $criteria, array $orderBy = null);

    /**
     * @param array $criteria
     * @param array $orderBy
     * @param integer $limit
     * @param integer $offset
     *
     * @return AuthenticationInterface[]|Collection
     */
    public function findAuthenticationsBy(array $criteria, array $orderBy = null, $limit = null, $offset = null);

    /**
     * @param AuthenticationInterface $authentication
     * @param bool $andFlush
     *
     * @return void
     */
    public function updateAuthentication(AuthenticationInterface $authentication, $andFlush = true);

    /**
     * @param AuthenticationInterface $authentication
     *
     * @return void
     */
    public function deleteAuthentication(AuthenticationInterface $authentication);

    /**
     * @return string
     */
    public function getClass();
}
