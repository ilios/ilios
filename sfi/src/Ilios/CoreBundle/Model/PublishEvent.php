<?php

namespace Ilios\CoreBundle\Model;

use Doctrine\ORM\Mapping as ORM;

/**
 * PublishEvent
 */
class PublishEvent
{
    /**
     * @var int
     */
    protected $publishEventId;

    /**
     * @var string
     */
    protected $machineIp;

    /**
     * @var \DateTime
     */
    protected $timeStamp;

    /**
     * @var string
     */
    protected $tableName;

    /**
     * @var int
     */
    protected $tableRowId;
    
    /**
     * @var \Ilios\CoreBundle\Model\User
     */
    protected $administrator;

    protected $courses;

    /**
     * Get publishEventId
     *
     * @return int 
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
     * @param int $tableRowId
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
     * @return int 
     */
    public function getTableRowId()
    {
        return $this->tableRowId;
    }

    /**
     * Set administrator
     *
     * @param \Ilios\CoreBundle\Model\User $user
     * @return PublishEvent
     */
    public function setAdministrator(\Ilios\CoreBundle\Model\User $user = null)
    {
        $this->administrator = $user;

        return $this;
    }

    /**
     * Get administrator
     *
     * @return \Ilios\CoreBundle\Model\User 
     */
    public function getAdministrator()
    {
        return $this->administrator;
    }
}
