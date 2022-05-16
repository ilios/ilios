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

    public function setSubject(string $subject);
    public function getSubject(): string;

    public function setPrepositionalObject(?string $prepositionalObject);
    public function getPrepositionalObject(): ?string;

    public function setPrepositionalObjectTableRowId(?string $prepositionalObjectTableRowId);
    public function getPrepositionalObjectTableRowId(): ?string;

    public function setUser(UserInterface $user);
    public function getUser(): UserInterface;

    public function getSchool(): ?SchoolInterface;
    public function setSchool(SchoolInterface $school = null): void;
}
