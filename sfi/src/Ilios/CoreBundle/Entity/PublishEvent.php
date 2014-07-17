<?php

namespace Ilios\CoreBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * PublishEvent
 */
class PublishEvent
{
    /**
     * @var integer
     */
    private $publishEventId;

    /**
     * @var string
     */
    private $machineIp;

    /**
     * @var \DateTime
     */
    private $timeStamp;

    /**
     * @var string
     */
    private $tableName;

    /**
     * @var integer
     */
    private $tableRowId;
    
    /**
     * @var \Ilios\CoreBundle\Entity\User
     */
    private $administrator;


    /**
     * Get publishEventId
     *
     * @return integer 
     */
    public function getPublishEventId()
    {
        return $this->publishEventId;
    }

    /**
     * Set machineIp
     *
     * @param string $machineIp
     * @return PublishEvent
     */
    public function setMachineIp($machineIp)
    {
        $this->machineIp = $machineIp;

        return $this;
    }

    /**
     * Get machineIp
     *
     * @return string 
     */
    public function getMachineIp()
    {
        return $this->machineIp;
    }

    /**
     * Set timeStamp
     *
     * @param \DateTime $timeStamp
     * @return PublishEvent
     */
    public function setTimeStamp($timeStamp)
    {
        $this->timeStamp = $timeStamp;

        return $this;
    }

    /**
     * Get timeStamp
     *
     * @return \DateTime 
     */
    public function getTimeStamp()
    {
        return $this->timeStamp;
    }

    /**
     * Set tableName
     *
     * @param string $tableName
     * @return PublishEvent
     */
    public function setTableName($tableName)
    {
        $this->tableName = $tableName;

        return $this;
    }

    /**
     * Get tableName
     *
     * @return string 
     */
    public function getTableName()
    {
        return $this->tableName;
    }

    /**
     * Set tableRowId
     *
     * @param integer $tableRowId
     * @return PublishEvent
     */
    public function setTableRowId($tableRowId)
    {
        $this->tableRowId = $tableRowId;

        return $this;
    }

    /**
     * Get tableRowId
     *
     * @return integer 
     */
    public function getTableRowId()
    {
        return $this->tableRowId;
    }

    /**
     * Set administrator
     *
     * @param \Ilios\CoreBundle\Entity\User $user
     * @return PublishEvent
     */
    public function setAdministrator(\Ilios\CoreBundle\Entity\User $user = null)
    {
        $this->administrator = $user;

        return $this;
    }

    /**
     * Get administrator
     *
     * @return \Ilios\CoreBundle\Entity\User 
     */
    public function getAdministrator()
    {
        return $this->administrator;
    }
}
