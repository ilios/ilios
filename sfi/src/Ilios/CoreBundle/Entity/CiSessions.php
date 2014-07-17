<?php

namespace Ilios\CoreBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * CiSessions
 */
class CiSessions
{
    /**
     * @var string
     */
    private $sessionId;

    /**
     * @var string
     */
    private $ipAddress;

    /**
     * @var string
     */
    private $userAgent;

    /**
     * @var integer
     */
    private $lastActivity;

    /**
     * @var string
     */
    private $userData;


    /**
     * Set sessionId
     *
     * @param string $sessionId
     * @return CiSessions
     */
    public function setSessionId($sessionId)
    {
        $this->sessionId = $sessionId;

        return $this;
    }

    /**
     * Get sessionId
     *
     * @return string 
     */
    public function getSessionId()
    {
        return $this->sessionId;
    }

    /**
     * Set ipAddress
     *
     * @param string $ipAddress
     * @return CiSessions
     */
    public function setIpAddress($ipAddress)
    {
        $this->ipAddress = $ipAddress;

        return $this;
    }

    /**
     * Get ipAddress
     *
     * @return string 
     */
    public function getIpAddress()
    {
        return $this->ipAddress;
    }

    /**
     * Set userAgent
     *
     * @param string $userAgent
     * @return CiSessions
     */
    public function setUserAgent($userAgent)
    {
        $this->userAgent = $userAgent;

        return $this;
    }

    /**
     * Get userAgent
     *
     * @return string 
     */
    public function getUserAgent()
    {
        return $this->userAgent;
    }

    /**
     * Set lastActivity
     *
     * @param integer $lastActivity
     * @return CiSessions
     */
    public function setLastActivity($lastActivity)
    {
        $this->lastActivity = $lastActivity;

        return $this;
    }

    /**
     * Get lastActivity
     *
     * @return integer 
     */
    public function getLastActivity()
    {
        return $this->lastActivity;
    }

    /**
     * Set userData
     *
     * @param string $userData
     * @return CiSessions
     */
    public function setUserData($userData)
    {
        $this->userData = $userData;

        return $this;
    }

    /**
     * Get userData
     *
     * @return string 
     */
    public function getUserData()
    {
        return $this->userData;
    }
}
