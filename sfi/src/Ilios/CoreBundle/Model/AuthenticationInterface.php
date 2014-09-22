<?php

namespace Ilios\CoreBundle\Model;

/**
 * Interface AuthenticationInterface
 */
interface AuthenticationInterface 
{
    public function setPersonId($personId);

    public function getPersonId();

    public function setUsername($username);

    public function getUsername();

    public function setPasswordSha256($passwordSha256);

    public function getPasswordSha256();

    public function setUser(\Ilios\CoreBundle\Model\User $user = null);

    public function getUser();
}

