<?php

namespace Ilios\CoreBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\DependencyInjection\ContainerAware;

/**
 * CISession
 */
class CISession extends ContainerAware
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
    private $unserializedData;

    /**
     * Set sessionId
     *
     * @param string $sessionId
     * @return CISession
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
     * @return CISession
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
     * @return CISession
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
     * @return CISession
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
     * @return CISession
     */
    public function setUserData($userData)
    {
        $this->unserializedData = null;
        $utilities = $this->container->get('ilios_legacy.utilities');
        $this->userData = $utilities->serialize($userData);
        return $this;
    }

    /**
     * Get userData
     *
     * @return string 
     */
    public function getUserData()
    {
        return $this->getUnserializedUserData();
    }

    /**
     * Retrieves a user data item by its given key.
     * 
     * @param string $key
     * @return mixed The user data value, or FALSE if not found.
     */
    public function getUserDataItem($key)
    {
        $data = $this->getUnserializedUserData();
        if (!$data) {
            return false;
        }
        return array_key_exists($key, $data) ? $data[$key] : false;
    }

    /**
     * Get unserialized data
     *
     * @return mixed 
     */
    protected function getUnserializedUserData()
    {
        if (!isset($this->unserializedData)) {
            $utilities = $this->container->get('ilios_legacy.utilities');
            $this->unserializedData = $utilities->unserialize($this->userData);
        }

        return $this->unserializedData;
    }
}
