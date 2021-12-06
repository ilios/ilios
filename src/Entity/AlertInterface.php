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

    public function getTableRowId(): int;

    /**
     * @param string $tableName
     */
    public function setTableName($tableName);

    public function getTableName(): string;

    /**
     * @param string $additionalText
     */
    public function setAdditionalText($additionalText);

    public function getAdditionalText(): string;

    /**
     * @param bool $dispatched
     */
    public function setDispatched($dispatched);

    public function isDispatched(): bool;

    public function setChangeTypes(Collection $changeTypes);

    public function addChangeType(AlertChangeTypeInterface $changeType);

    public function removeChangeType(AlertChangeTypeInterface $changeType);

    public function getChangeTypes(): Collection;

    public function setInstigators(Collection $instigators);

    public function addInstigator(UserInterface $instigator);

    public function removeInstigator(UserInterface $instigator);

    public function getInstigators(): Collection;

    public function setRecipients(Collection $recipients);

    public function addRecipient(SchoolInterface $recipient);

    public function removeRecipient(SchoolInterface $recipient);

    public function getRecipients(): Collection;
}
