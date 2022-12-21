<?php

declare(strict_types=1);

namespace App\Entity;

use Doctrine\Common\Collections\Collection;
use App\Traits\IdentifiableEntityInterface;
use App\Traits\IlmSessionsEntityInterface;
use App\Traits\InstructorGroupsEntityInterface;
use App\Traits\InstructorsEntityInterface;
use App\Traits\TitledEntityInterface;
use App\Traits\StringableEntityInterface;
use App\Traits\OfferingsEntityInterface;
use App\Traits\UsersEntityInterface;

interface LearnerGroupInterface extends
    IdentifiableEntityInterface,
    TitledEntityInterface,
    StringableEntityInterface,
    OfferingsEntityInterface,
    LoggableEntityInterface,
    UsersEntityInterface,
    InstructorGroupsEntityInterface,
    InstructorsEntityInterface,
    IlmSessionsEntityInterface
{
    public function setLocation(?string $location);
    public function getLocation(): ?string;

    public function setCohort(CohortInterface $cohort);
    public function getCohort(): CohortInterface;

    public function setParent(LearnerGroupInterface $parent = null);
    public function getParent(): ?LearnerGroupInterface;

    public function setChildren(Collection $children);
    public function addChild(LearnerGroupInterface $child);
    public function removeChild(LearnerGroupInterface $child);
    public function getChildren(): Collection;

    /**
     * Get the school we belong to
     */
    public function getSchool(): ?SchoolInterface;

    /**
     * Gets the program that this learner group belongs to.
     */
    public function getProgram(): ?ProgramInterface;

    /**
     * Gets the program year that this learner group belongs to.
     */
    public function getProgramYear(): ?ProgramYearInterface;

    public function setAncestor(LearnerGroupInterface $ancestor);
    public function getAncestor(): ?LearnerGroupInterface;

    public function getAncestorOrSelf(): LearnerGroupInterface;

    public function setDescendants(Collection $children);
    public function addDescendant(LearnerGroupInterface $child);
    public function removeDescendant(LearnerGroupInterface $child);
    public function getDescendants(): Collection;

    public function setNeedsAccommodation(bool $needsAccommodation): void;
    public function getNeedsAccommodation(): bool;

    public function setUrl(?string $url): void;
    public function getUrl(): ?string;
}
