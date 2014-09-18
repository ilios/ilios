<?php

namespace Ilios\CoreBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * UserSyncException
 */
class UserSyncException
{
    /**
     * @var integer
     */
    private $exceptionId;

    /**
     * @var integer
     */
    private $processId;

    /**
     * @var string
     */
    private $processName;

    /**
     * @var integer
     */
    private $exceptionCode;

    /**
     * @var string
     */
    private $mismatchedPropertyName;

    /**
     * @var string
     */
    private $mismatchedPropertyValue;

    /**
     * @var \Ilios\CoreBundle\Entity\User
     */
    private $user;


    /**
     * Get exceptionId
     *
     * @return integer 
     */
    public function getExceptionId()
    {
        return $this->exceptionId;
    }

    /**
     * Set processId
     *
     * @param integer $processId
     * @return UserSyncException
     */
    public function setProcessId($processId)
    {
        $this->processId = $processId;

        return $this;
    }

    /**
     * Get processId
     *
     * @return integer 
     */
    public function getProcessId()
    {
        return $this->processId;
    }

    /**
     * Set processName
     *
     * @param string $processName
     * @return UserSyncException
     */
    public function setProcessName($processName)
    {
        $this->processName = $processName;

        return $this;
    }

    /**
     * Get processName
     *
     * @return string 
     */
    public function getProcessName()
    {
        return $this->processName;
    }

    /**
     * Set exceptionCode
     *
     * @param integer $exceptionCode
     * @return UserSyncException
     */
    public function setExceptionCode($exceptionCode)
    {
        $this->exceptionCode = $exceptionCode;

        return $this;
    }

    /**
     * Get exceptionCode
     *
     * @return integer 
     */
    public function getExceptionCode()
    {
        return $this->exceptionCode;
    }

    /**
     * Set mismatchedPropertyName
     *
     * @param string $mismatchedPropertyName
     * @return UserSyncException
     */
    public function setMismatchedPropertyName($mismatchedPropertyName)
    {
        $this->mismatchedPropertyName = $mismatchedPropertyName;

        return $this;
    }

    /**
     * Get mismatchedPropertyName
     *
     * @return string 
     */
    public function getMismatchedPropertyName()
    {
        return $this->mismatchedPropertyName;
    }

    /**
     * Set mismatchedPropertyValue
     *
     * @param string $mismatchedPropertyValue
     * @return UserSyncException
     */
    public function setMismatchedPropertyValue($mismatchedPropertyValue)
    {
        $this->mismatchedPropertyValue = $mismatchedPropertyValue;

        return $this;
    }

    /**
     * Get mismatchedPropertyValue
     *
     * @return string 
     */
    public function getMismatchedPropertyValue()
    {
        return $this->mismatchedPropertyValue;
    }

    /**
     * Set user
     *
     * @param \Ilios\CoreBundle\Entity\User $user
     * @return UserSyncException
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
