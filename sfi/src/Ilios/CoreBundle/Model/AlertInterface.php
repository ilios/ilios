<?php

namespace Ilios\CoreBundle\Model;



/**
 * Interface AlertInterface
 */
interface AlertInterface 
{
    public function getAlertId();

    public function setTableRowId($tableRowId);

    public function getTableRowId();

    public function setTableName($tableName);

    public function getTableName();

    public function setAdditionalText($additionalText);

    public function getAdditionalText();

    public function setDispatched($dispatched);

    public function getDispatched();

    public function addChangeType(\Ilios\CoreBundle\Model\AlertChangeType $changeTypes);

    public function removeChangeType(\Ilios\CoreBundle\Model\AlertChangeType $changeTypes);

    public function getChangeTypes();

    public function addInstigator(\Ilios\CoreBundle\Model\User $instigators);

    public function removeInstigator(\Ilios\CoreBundle\Model\User $instigators);

    public function getInstigators();

    public function addRecipient(\Ilios\CoreBundle\Model\School $recipients);

    public function removeRecipient(\Ilios\CoreBundle\Model\School $recipients);

    public function getRecipients();
}
