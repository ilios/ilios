<?php

namespace Ilios\CoreBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Permission
 */
class Permission
{
    /**
     * @var integer
     */
    private $permissionId;

    /**
     * @var integer
     */
    private $userId;

    /**
     * @var boolean
     */
    private $canRead;

    /**
     * @var boolean
     */
    private $canWrite;

    /**
     * @var integer
     */
    private $tableRowId;

    /**
     * @var string
     */
    private $tableName;


    /**
     * Get permissionId
     *
     * @return integer 
     */
    public function getPermissionId()
    {
        return $this->permissionId;
    }

    /**
     * Set userId
     *
     * @param integer $userId
     * @return Permission
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
     * Set canRead
     *
     * @param boolean $canRead
     * @return Permission
     */
    public function setCanRead($canRead)
    {
        $this->canRead = $canRead;

        return $this;
    }

    /**
     * Get canRead
     *
     * @return boolean 
     */
    public function getCanRead()
    {
        return $this->canRead;
    }

    /**
     * Set canWrite
     *
     * @param boolean $canWrite
     * @return Permission
     */
    public function setCanWrite($canWrite)
    {
        $this->canWrite = $canWrite;

        return $this;
    }

    /**
     * Get canWrite
     *
     * @return boolean 
     */
    public function getCanWrite()
    {
        return $this->canWrite;
    }

    /**
     * Set tableRowId
     *
     * @param integer $tableRowId
     * @return Permission
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
     * Set tableName
     *
     * @param string $tableName
     * @return Permission
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
}
