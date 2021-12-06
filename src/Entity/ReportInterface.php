<?php

declare(strict_types=1);

namespace App\Entity;

use App\Traits\IdentifiableEntityInterface;
use App\Traits\SchoolEntityInterface;
use App\Traits\TitledEntityInterface;

/**
 * Interface ReportInterface
 */
interface ReportInterface extends
    IdentifiableEntityInterface,
    TitledEntityInterface,
    LoggableEntityInterface
{

    /**
     * @return \DateTime
     */
    public function getCreatedAt(): \DateTime;

    /**
     * @param string $subject
     */
    public function setSubject($subject);

    /**
     * @return string
     */
    public function getSubject(): string;

    /**
     * @param string $prepositionalObject
     */
    public function setPrepositionalObject($prepositionalObject);

    /**
     * @return string
     */
    public function getPrepositionalObject(): string;

    /**
     * @param string $prepositionalObjectTableRowId
     */
    public function setPrepositionalObjectTableRowId($prepositionalObjectTableRowId);

    /**
     * @return string
     */
    public function getPrepositionalObjectTableRowId(): string;

    public function setUser(UserInterface $user);

    /**
     * @return UserInterface
     */
    public function getUser(): UserInterface;

    public function getSchool(): ?SchoolInterface;

    /**
     * @param SchoolInterface|null $school
     */
    public function setSchool(SchoolInterface $school = null): void;
}
