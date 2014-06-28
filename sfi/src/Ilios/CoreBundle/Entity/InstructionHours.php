<?php

namespace Ilios\CoreBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * InstructionHours
 */
class InstructionHours
{
    /**
     * @var integer
     */
    private $instructionHoursId;

    /**
     * @var \DateTime
     */
    private $generationTimeStamp;

    /**
     * @var integer
     */
    private $hoursAccrued;

    /**
     * @var boolean
     */
    private $modified;

    /**
     * @var \DateTime
     */
    private $modificationTimeStamp;

    /**
     * @var integer
     */
    private $userId;

    /**
     * @var integer
     */
    private $sessionId;


    /**
     * Get instructionHoursId
     *
     * @return integer 
     */
    public function getInstructionHoursId()
    {
        return $this->instructionHoursId;
    }

    /**
     * Set generationTimeStamp
     *
     * @param \DateTime $generationTimeStamp
     * @return InstructionHours
     */
    public function setGenerationTimeStamp($generationTimeStamp)
    {
        $this->generationTimeStamp = $generationTimeStamp;

        return $this;
    }

    /**
     * Get generationTimeStamp
     *
     * @return \DateTime 
     */
    public function getGenerationTimeStamp()
    {
        return $this->generationTimeStamp;
    }

    /**
     * Set hoursAccrued
     *
     * @param integer $hoursAccrued
     * @return InstructionHours
     */
    public function setHoursAccrued($hoursAccrued)
    {
        $this->hoursAccrued = $hoursAccrued;

        return $this;
    }

    /**
     * Get hoursAccrued
     *
     * @return integer 
     */
    public function getHoursAccrued()
    {
        return $this->hoursAccrued;
    }

    /**
     * Set modified
     *
     * @param boolean $modified
     * @return InstructionHours
     */
    public function setModified($modified)
    {
        $this->modified = $modified;

        return $this;
    }

    /**
     * Get modified
     *
     * @return boolean 
     */
    public function getModified()
    {
        return $this->modified;
    }

    /**
     * Set modificationTimeStamp
     *
     * @param \DateTime $modificationTimeStamp
     * @return InstructionHours
     */
    public function setModificationTimeStamp($modificationTimeStamp)
    {
        $this->modificationTimeStamp = $modificationTimeStamp;

        return $this;
    }

    /**
     * Get modificationTimeStamp
     *
     * @return \DateTime 
     */
    public function getModificationTimeStamp()
    {
        return $this->modificationTimeStamp;
    }

    /**
     * Set userId
     *
     * @param integer $userId
     * @return InstructionHours
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
     * Set sessionId
     *
     * @param integer $sessionId
     * @return InstructionHours
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
}
