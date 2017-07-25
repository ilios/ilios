<?php

namespace Ilios\CoreBundle\Entity;

use Ilios\CoreBundle\Traits\IdentifiableEntityInterface;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Ilios\ApiBundle\Annotation as IS;

/**
 * Interface AlertInterface
 */
interface AlertInterface extends IdentifiableEntityInterface, LoggableEntityInterface
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
    public function isDispatched();

    /**
     * @param Collection $changeTypes
     */
    public function setChangeTypes(Collection $changeTypes);

    /**
     * @param AlertChangeTypeInterface $changeType
     */
    public function addChangeType(AlertChangeTypeInterface $changeType);

    /**
     * @param AlertChangeTypeInterface $changeType
     */
    public function removeChangeType(AlertChangeTypeInterface $changeType);

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
     * @param UserInterface $instigator
     */
    public function removeInstigator(UserInterface $instigator);

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
     * @param SchoolInterface $recipient
     */
    public function removeRecipient(SchoolInterface $recipient);

    /**
     * @return ArrayCollection|SchoolInterface[]
     */
    public function getRecipients();
}
