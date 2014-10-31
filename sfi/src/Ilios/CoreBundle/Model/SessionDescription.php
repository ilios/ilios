<?php

namespace Ilios\CoreBundle\Model;

use Doctrine\ORM\Mapping as ORM;

/**
 * SessionDescription
 */
class SessionDescription
{
    /**
     * @var integer
     */
    private $sessionId;

    /**
     * @var string
     */
    private $description;

    /**
     * @var \Ilios\CoreBundle\Model\Session
     */
    private $session;


    /**
     * Set sessionId
     *
     * @param integer $sessionId
     * @return SessionDescription
     */
    public function setSessionId($sessionId)
    {
        $this->sessionId = $sessionId;

        return $this;
    }

    /**
     * Get sessionId
     *
     * @return integer 
     */
    public function getSessionId()
    {
        return $this->sessionId;
    }

    /**
     * Set description
     *
     * @param string $description
     * @return SessionDescription
     */
    public function setDescription($description)
    {
        $this->description = $description;

        return $this;
    }

    /**
     * Get description
     *
     * @return string 
     */
    public function getDescription()
    {
        return $this->description;
    }

    /**
     * Set session
     *
     * @param \Ilios\CoreBundle\Model\Session $session
     * @return SessionDescription
     */
    public function setSession(\Ilios\CoreBundle\Model\Session $session = null)
    {
        $this->session = $session;

        return $this;
    }

    /**
     * Get session
     *
     * @return \Ilios\CoreBundle\Model\Session 
     */
    public function getSession()
    {
        return $this->session;
    }
}
