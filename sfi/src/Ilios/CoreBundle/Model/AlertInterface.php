<?php

namespace Ilios\CoreBundle\Model;

use Ilios\CoreBundle\Traits\IdentifiableTraitIntertface;

use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Component\Validator\Constraints\Collection;

use Ilios\CoreBundle\Model\AlertChangeTypeInterface;
use Ilios\CoreBundle\Model\UserInterface;
use Ilios\CoreBundle\Model\SchoolInterface;

/**
 * Interface AlertInterface
 * @package Ilios\CoreBundle\Model
 */
interface AlertInterface extends IdentifiableTraitIntertface
{
    /**
     * @param integer $tableRowId
     */
    public function setTableRowId($tableRowId);

    /**
     * @return integer
     */
    public function getTableRowId();

    /**
     * @param string $tableName
     */
    public function setTableName($tableName);

    /**
     * @return string
     */
    public function getTableName();

    /**
     * @param string $additionalText
     */
    public function setAdditionalText($additionalText);

    /**
     * @return string
     */
    public function getAdditionalText();

    /**
     * @param boolean $dispatched
     */
    public function setDispatched($dispatched);

    /**
     * @return boolean
     */
    public function getDispatched();

    /**
     * @param Collection $changeTypes
     */
    public function setChangeTypes(Collection $changeTypes);

    /**
     * @param AlertChangeType $changeType
     */
    public function addChangeType(AlertChangeType $changeType);

    /**
     * @return ArrayCollection|AlertChangeTypeInterface[]
     */
    public function getChangeTypes();

    /**
     * @param Collection $instigators
     */
    public function setInstigators(Collection $instigators);

    /**
     * @param UserInterface $instigator
     */
    public function addInstigator(UserInterface $instigator);

    /**
     * @return ArrayCollection|UserInterface[]
     */
    public function getInstigators();

    /**
     * @param Collection $recipients
     */
    public function setRecipients(Collection $recipients);

    /**
     * @param SchoolInterface $recipient
     */
    public function addRecipient(SchoolInterface $recipient);

    /**
     * @return ArrayCollection|SchoolInterface[]
     */
    public function getRecipients();
}

