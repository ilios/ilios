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
    public function getTableRowId(): int;

    /**
     * @param string $tableName
     */
    public function setTableName($tableName);

    /**
     * @return string
     */
    public function getTableName(): string;

    /**
     * @param string $additionalText
     */
    public function setAdditionalText($additionalText);

    /**
     * @return string
     */
    public function getAdditionalText(): string;

    /**
     * @param bool $dispatched
     */
    public function setDispatched($dispatched);

    /**
     * @return bool
     */
    public function isDispatched(): bool;

    public function setChangeTypes(Collection $changeTypes);

    public function addChangeType(AlertChangeTypeInterface $changeType);

    public function removeChangeType(AlertChangeTypeInterface $changeType);

    /**
     * @return ArrayCollection|AlertChangeTypeInterface[]
     */
    public function getChangeTypes(): Collection;

    public function setInstigators(Collection $instigators);

    public function addInstigator(UserInterface $instigator);

    public function removeInstigator(UserInterface $instigator);

    /**
     * @return ArrayCollection|UserInterface[]
     */
    public function getInstigators(): Collection;

    public function setRecipients(Collection $recipients);

    public function addRecipient(SchoolInterface $recipient);

    public function removeRecipient(SchoolInterface $recipient);

    /**
     * @return ArrayCollection|SchoolInterface[]
     */
    public function getRecipients(): Collection;
}
