<?php

declare(strict_types=1);

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use App\Traits\IdentifiableEntityInterface;
use App\Traits\IlmSessionsEntityInterface;
use App\Traits\InstructorGroupsEntityInterface;
use App\Traits\InstructorsEntityInterface;
use App\Traits\TitledEntityInterface;
use App\Traits\StringableEntityInterface;
use App\Traits\OfferingsEntityInterface;
use App\Traits\UsersEntityInterface;

/**
 * Interface GroupInterface
 */
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
    /**
     * @param string $location
     */
    public function setLocation($location);

    /**
     * @return string
     */
    public function getLocation();

    public function setCohort(CohortInterface $cohort);

    /**
     * @return CohortInterface
     */
    public function getCohort();

    public function setParent(LearnerGroupInterface $parent = null);

    /**
     * @return LearnerGroupInterface
     */
    public function getParent();

    public function setChildren(Collection $children);

    public function addChild(LearnerGroupInterface $child);

    public function removeChild(LearnerGroupInterface $child);

    /**
     * @return ArrayCollection|LearnerGroupInterface[]
     */
    public function getChildren();

    /**
     * Get the school we belong to
     * @return SchoolInterface|null
     */
    public function getSchool();

    /**
     * Gets the program that this learner group belongs to.
     * @return ProgramInterface|null
     */
    public function getProgram();

    /**
     * Gets the program year that this learner group belongs to.
     * @return ProgramYearInterface|null
     */
    public function getProgramYear();

    public function setAncestor(LearnerGroupInterface $ancestor);

    /**
     * @return LearnerGroupInterface
     */
    public function getAncestor();

    /**
     * @return LearnerGroupInterface
     */
    public function getAncestorOrSelf();

    public function setDescendants(Collection $children);

    public function addDescendant(LearnerGroupInterface $child);

    public function removeDescendant(LearnerGroupInterface $child);

    /**
     * @return ArrayCollection|LearnerGroupInterface[]
     */
    public function getDescendants();

    public function setNeedsAccommodation(bool $needsAccommodation): void;

    public function getNeedsAccommodation(): bool;

    /**
     * @var string|null $url
     */
    public function setUrl(?string $url): void;

    public function getUrl(): ?string;
}
