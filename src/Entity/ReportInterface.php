<?php

declare(strict_types=1);

namespace App\Entity;

use App\Traits\IdentifiableEntityInterface;
use App\Traits\TitledNullableEntityInterface;
use DateTime;

interface ReportInterface extends
    IdentifiableEntityInterface,
    TitledNullableEntityInterface,
    LoggableEntityInterface
{
    public function getCreatedAt(): DateTime;

    public function setSubject(string $subject): void;
    public function getSubject(): string;

    public function setPrepositionalObject(?string $prepositionalObject): void;
    public function getPrepositionalObject(): ?string;

    public function setPrepositionalObjectTableRowId(?string $prepositionalObjectTableRowId): void;
    public function getPrepositionalObjectTableRowId(): ?string;

    public function setUser(UserInterface $user): void;
    public function getUser(): UserInterface;

    public function getSchool(): ?SchoolInterface;
    public function setSchool(?SchoolInterface $school = null): void;
}
