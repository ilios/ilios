<?php

namespace Ilios\CoreBundle\Model;

/**
 * Interface PublishEventInterface
 */
interface PublishEventInterface 
{
    public function getPublishEventId();

    public function setMachineIp($machineIp);

    public function getMachineIp();

    public function setTimeStamp($timeStamp);

    public function getTimeStamp();

    public function setTableName($tableName);

    public function getTableName();

    public function setTableRowId($tableRowId);

    public function getTableRowId();

    public function setAdministrator(\Ilios\CoreBundle\Model\User $user = null);

    public function getAdministrator();
}

