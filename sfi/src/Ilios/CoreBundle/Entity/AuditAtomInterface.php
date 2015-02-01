<?php

namespace Ilios\CoreBundle\Entity;

/**
 * Interface AuditAtomInterface
 */
interface AuditAtomInterface 
{
    public function getAuditAtomId();

    public function setTableRowId($tableRowId);

    public function getTableRowId();

    public function setTableColumn($tableColumn);

    public function getTableColumn();

    public function setTableName($tableName);

    public function getTableName();

    public function setEventType($eventType);

    public function getEventType();

    public function setCreatedAt($createdAt);

    public function getCreatedAt();

    public function setCreatedBy(\Ilios\CoreBundle\Entity\User $createdBy = null);

    public function getCreatedBy();
}

