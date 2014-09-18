<?php

namespace Ilios\CoreBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * AuditAtom
 */
class AuditAtom
{
    /**
     * @var integer
     */
    private $auditAtomId;

    /**
     * @var integer
     */
    private $tableRowId;

    /**
     * @var string
     */
    private $tableColumn;

    /**
     * @var string
     */
    private $tableName;

    /**
     * @var boolean
     */
    private $eventType;

    /**
     * @var \DateTime
     */
    private $createdAt;

    /**
     * @var \Ilios\CoreBundle\Entity\User
     */
    private $createdBy;


    /**
     * Get auditAtomId
     *
     * @return integer 
     */
    public function getAuditAtomId()
    {
        return $this->auditAtomId;
    }

    /**
     * Set tableRowId
     *
     * @param integer $tableRowId
     * @return AuditAtom
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
     * Set tableColumn
     *
     * @param string $tableColumn
     * @return AuditAtom
     */
    public function setTableColumn($tableColumn)
    {
        $this->tableColumn = $tableColumn;

        return $this;
    }

    /**
     * Get tableColumn
     *
     * @return string 
     */
    public function getTableColumn()
    {
        return $this->tableColumn;
    }

    /**
     * Set tableName
     *
     * @param string $tableName
     * @return AuditAtom
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
     * Set eventType
     *
     * @param boolean $eventType
     * @return AuditAtom
     */
    public function setEventType($eventType)
    {
        $this->eventType = $eventType;

        return $this;
    }

    /**
     * Get eventType
     *
     * @return boolean 
     */
    public function getEventType()
    {
        return $this->eventType;
    }

    /**
     * Set createdAt
     *
     * @param \DateTime $createdAt
     * @return AuditAtom
     */
    public function setCreatedAt($createdAt)
    {
        $this->createdAt = $createdAt;

        return $this;
    }

    /**
     * Get createdAt
     *
     * @return \DateTime 
     */
    public function getCreatedAt()
    {
        return $this->createdAt;
    }

    /**
     * Set createdBy
     *
     * @param \Ilios\CoreBundle\Entity\User $createdBy
     * @return AuditAtom
     */
    public function setCreatedBy(\Ilios\CoreBundle\Entity\User $createdBy = null)
    {
        $this->createdBy = $createdBy;

        return $this;
    }

    /**
     * Get createdBy
     *
     * @return \Ilios\CoreBundle\Entity\User 
     */
    public function getCreatedBy()
    {
        return $this->createdBy;
    }
}
