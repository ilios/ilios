<?php

declare(strict_types=1);

namespace App\Entity;

use App\Traits\IdentifiableEntityInterface;
use App\Traits\TitledNullableEntityInterface;
use DateTime;

/**
 * Interface ReportInterface
 */
interface ReportInterface extends
    IdentifiableEntityInterface,
    TitledNullableEntityInterface,
    LoggableEntityInterface
{
    public function getCreatedAt(): DateTime;

    /**
     * @param string $subject
     */
    public function setSubject($subject);

    public function getSubject(): string;

    /**
     * @param string $prepositionalObject
     */
    public function setPrepositionalObject($prepositionalObject);

    public function getPrepositionalObject(): ?string;

    /**
     * @param string $prepositionalObjectTableRowId
     */
    public function setPrepositionalObjectTableRowId($prepositionalObjectTableRowId);

    public function getPrepositionalObjectTableRowId(): ?string;

    public function setUser(UserInterface $user);

    public function getUser(): UserInterface;

    public function getSchool(): ?SchoolInterface;

    /**
     * @param SchoolInterface|null $school
     */
    public function setSchool(SchoolInterface $school = null): void;
}
