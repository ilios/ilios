<?php

namespace Ilios\CoreBundle\Model;

use Doctrine\ORM\Mapping as ORM;

/**
 * Authentication
 */
class Authentication
{
    /**
     * @var integer
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
     * @var \Ilios\CoreBundle\Model\User
     */
    private $user;


    /**
     * Set personId
     *
     * @param integer $personId
     * @return Authentication
     */
    public function setPersonId($personId)
    {
        $this->personId = $personId;

        return $this;
    }

    /**
     * Get personId
     *
     * @return integer 
     */
    public function getPersonId()
    {
        return $this->personId;
    }

    /**
     * Set username
     *
     * @param string $username
     * @return Authentication
     */
    public function setUsername($username)
    {
        $this->username = $username;

        return $this;
    }

    /**
     * Get username
     *
     * @return string 
     */
    public function getUsername()
    {
        return $this->username;
    }

    /**
     * Set passwordSha256
     *
     * @param string $passwordSha256
     * @return Authentication
     */
    public function setPasswordSha256($passwordSha256)
    {
        $this->passwordSha256 = $passwordSha256;

        return $this;
    }

    /**
     * Get passwordSha256
     *
     * @return string 
     */
    public function getPasswordSha256()
    {
        return $this->passwordSha256;
    }

    /**
     * Set user
     *
     * @param \Ilios\CoreBundle\Model\User $user
     * @return Authentication
     */
    public function setUser(\Ilios\CoreBundle\Model\User $user = null)
    {
        $this->user = $user;

        return $this;
    }

    /**
     * Get user
     *
     * @return \Ilios\CoreBundle\Model\User 
     */
    public function getUser()
    {
        return $this->user;
    }
}
