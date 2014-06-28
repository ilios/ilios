<?php

namespace Ilios\CoreBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * ApiKey
 */
class ApiKey
{
    /**
     * @var integer
     */
    private $userId;

    /**
     * @var string
     */
    private $apiKey;

    /**
     * @var \Ilios\CoreBundle\Entity\User
     */
    private $user;


    /**
     * Set userId
     *
     * @param integer $userId
     * @return ApiKey
     */
    public function setUserId($userId)
    {
        $this->userId = $userId;

        return $this;
    }

    /**
     * Get userId
     *
     * @return integer 
     */
    public function getUserId()
    {
        return $this->userId;
    }

    /**
     * Set apiKey
     *
     * @param string $apiKey
     * @return ApiKey
     */
    public function setApiKey($apiKey)
    {
        $this->apiKey = $apiKey;

        return $this;
    }

    /**
     * Get apiKey
     *
     * @return string 
     */
    public function getApiKey()
    {
        return $this->apiKey;
    }

    /**
     * Set user
     *
     * @param \Ilios\CoreBundle\Entity\User $user
     * @return ApiKey
     */
    public function setUser(\Ilios\CoreBundle\Entity\User $user = null)
    {
        $this->user = $user;

        return $this;
    }

    /**
     * Get user
     *
     * @return \Ilios\CoreBundle\Entity\User 
     */
    public function getUser()
    {
        return $this->user;
    }
}
