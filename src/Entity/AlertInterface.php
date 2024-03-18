<?php

declare(strict_types=1);

namespace App\Entity;

use App\Traits\IdentifiableEntityInterface;
use Doctrine\Common\Collections\Collection;

interface AlertInterface extends IdentifiableEntityInterface, LoggableEntityInterface
{
    public function setTableRowId(int $tableRowId): void;
    public function getTableRowId(): int;

    public function setTableName(string $tableName): void;
    public function getTableName(): string;

    public function setAdditionalText(?string $additionalText): void;
    public function getAdditionalText(): ?string;

    public function setDispatched(bool $dispatched): void;
    public function isDispatched(): bool;

    public function setChangeTypes(Collection $changeTypes): void;
    public function addChangeType(AlertChangeTypeInterface $changeType): void;
    public function removeChangeType(AlertChangeTypeInterface $changeType): void;
    public function getChangeTypes(): Collection;

    public function setInstigators(Collection $instigators): void;
    public function addInstigator(UserInterface $instigator): void;
    public function removeInstigator(UserInterface $instigator): void;
    public function getInstigators(): Collection;

    public function setRecipients(Collection $recipients): void;
    public function addRecipient(SchoolInterface $recipient): void;
    public function removeRecipient(SchoolInterface $recipient): void;
    public function getRecipients(): Collection;

    public function setServiceTokenInstigators(Collection $instigators): void;
    public function addServiceTokenInstigator(ServiceTokenInterface $instigator): void;
    public function removeServiceTokenInstigator(ServiceTokenInterface $instigator): void;
    public function getServiceTokenInstigators(): Collection;
}
