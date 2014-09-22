<?php

namespace Ilios\CoreBundle\Model;



/**
 * Interface PermissionInterface
 */
interface PermissionInterface 
{
    public function getPermissionId();

    public function setUserId($userId);

    public function getUserId();

    public function setCanRead($canRead);

    public function getCanRead();

    public function setCanWrite($canWrite);

    public function getCanWrite();

    public function setTableRowId($tableRowId);

    public function getTableRowId();

    public function setTableName($tableName);

    public function getTableName();
}
