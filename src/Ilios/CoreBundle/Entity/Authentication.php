<?php

namespace Ilios\CoreBundle\Entity;

/**
 * Class Authentication
 * @package Ilios\CoreBundle\Entity
 */
class Authentication implements AuthenticationInterface
{
    /**
     * @var int
     */
    private $personId;

    /**
     * @var string
     */
    private $username;

    /**
     * @var string
     */
    private $passwordSha256;

    /**
     * @var UserInterface
     */
    private $user;

    /**
     * @param int $personId
     */
    public function setPersonId($personId)
    {
        $this->personId = $personId;
    }

    /**
     * @return int
     */
    public function getPersonId()
    {
        return $this->personId;
    }

    /**
     * @param string $username
     */
    public function setUsername($username)
    {
        $this->username = $username;
    }

    /**
     * @return string
     */
    public function getUsername()
    {
        return $this->username;
    }

    /**
     * @param string $passwordSha256
     */
    public function setPasswordSha256($passwordSha256)
    {
        $this->passwordSha256 = $passwordSha256;
    }

    /**
     * @return string
     */
    public function getPasswordSha256()
    {
        return $this->passwordSha256;
    }

    /**
     * @param UserInterface $user
     */
    public function setUser(UserInterface $user)
    {
        $this->user = $user;
    }

    /**
     * @return UserInterface
     */
    public function getUser()
    {
        return $this->user;
    }
}
