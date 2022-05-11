<?php

declare(strict_types=1);

namespace App\Entity;

use App\Traits\IdentifiableEntityInterface;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use App\Attribute as IA;

interface AlertInterface extends IdentifiableEntityInterface, LoggableEntityInterface
{
    public function setTableRowId(int $tableRowId);
    public function getTableRowId(): int;

    public function setTableName(string $tableName);
    public function getTableName(): string;

    public function setAdditionalText(string $additionalText);
    public function getAdditionalText(): string;

    public function setDispatched(bool $dispatched);
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
