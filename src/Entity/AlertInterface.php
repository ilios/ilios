<?php

declare(strict_types=1);

namespace App\Entity;

use App\Traits\IdentifiableEntityInterface;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use App\Attribute as IA;

/**
 * Interface AlertInterface
 */
interface AlertInterface extends IdentifiableEntityInterface, LoggableEntityInterface
{
    /**
     * @param int $tableRowId
     */
    public function setTableRowId($tableRowId);

    /**
     * @return int
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
     * @param bool $dispatched
     */
    public function setDispatched($dispatched);

    /**
     * @return bool
     */
    public function isDispatched();

    public function setChangeTypes(Collection $changeTypes);

    public function addChangeType(AlertChangeTypeInterface $changeType);

    public function removeChangeType(AlertChangeTypeInterface $changeType);

    /**
     * @return ArrayCollection|AlertChangeTypeInterface[]
     */
    public function getChangeTypes();

    public function setInstigators(Collection $instigators);

    public function addInstigator(UserInterface $instigator);

    public function removeInstigator(UserInterface $instigator);

    /**
     * @return ArrayCollection|UserInterface[]
     */
    public function getInstigators();

    public function setRecipients(Collection $recipients);

    public function addRecipient(SchoolInterface $recipient);

    public function removeRecipient(SchoolInterface $recipient);

    /**
     * @return ArrayCollection|SchoolInterface[]
     */
    public function getRecipients();
}
