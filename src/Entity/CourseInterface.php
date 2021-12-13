<?php

declare(strict_types=1);

namespace App\Entity;

use App\Traits\CourseObjectivesEntityInterface;
use App\Traits\IndexableCoursesEntityInterface;
use App\Traits\TitledNullableEntityInterface;
use DateTime;
use Doctrine\Common\Collections\Collection;
use App\Traits\AdministratorsEntityInterface;
use App\Traits\ArchivableEntityInterface;
use App\Traits\CategorizableEntityInterface;
use App\Traits\CohortsEntityInterface;
use App\Traits\DirectorsEntityInterface;
use App\Traits\IdentifiableEntityInterface;
use App\Traits\LockableEntityInterface;
use App\Traits\MeshDescriptorsEntityInterface;
use App\Traits\PublishableEntityInterface;
use App\Traits\SchoolEntityInterface;
use App\Traits\StudentAdvisorsEntityInterface;
use App\Traits\StringableEntityInterface;
use App\Traits\SessionsEntityInterface;

/**
 * Interface CourseInterface
 */
interface CourseInterface extends
    IdentifiableEntityInterface,
    TitledNullableEntityInterface,
    StringableEntityInterface,
    LockableEntityInterface,
    ArchivableEntityInterface,
    SessionsEntityInterface,
    SchoolEntityInterface,
    StudentAdvisorsEntityInterface,
    LoggableEntityInterface,
    PublishableEntityInterface,
    CategorizableEntityInterface,
    CohortsEntityInterface,
    MeshDescriptorsEntityInterface,
    DirectorsEntityInterface,
    AdministratorsEntityInterface,
    IndexableCoursesEntityInterface,
    CourseObjectivesEntityInterface
{
    /**
     * @param int $level
     */
    public function setLevel($level);

    public function getLevel(): int;

    /**
     * @param int $year
     */
    public function setYear($year);

    public function getYear(): int;

    public function setStartDate(DateTime $startDate);

    public function getStartDate(): DateTime;

    public function setEndDate(DateTime $endDate);

    public function getEndDate(): DateTime;

    /**
     * @todo: Possible rename.
     * @param string $externalId
     */
    public function setExternalId($externalId);

    /**
     * @todo: Possible rename.
     */
    public function getExternalId(): ?string;

    public function setClerkshipType(?CourseClerkshipTypeInterface $clerkshipType);

    public function getClerkshipType(): ?CourseClerkshipTypeInterface;

    public function setLearningMaterials(Collection $learningMaterials = null);

    public function addLearningMaterial(CourseLearningMaterialInterface $learningMaterial);

    public function removeLearningMaterial(CourseLearningMaterialInterface $learningMaterial);

    public function getLearningMaterials(): Collection;

    public function setAncestor(CourseInterface $ancestor);

    public function getAncestor(): ?CourseInterface;

    public function getAncestorOrSelf(): CourseInterface;

    public function setDescendants(Collection $children);

    public function addDescendant(CourseInterface $child);

    public function removeDescendant(CourseInterface $child);

    public function getDescendants(): Collection;

    public function setSequenceBlocks(Collection $sequenceBlocks);

    public function addSequenceBlock(CurriculumInventorySequenceBlockInterface $sequenceBlock);

    public function removeSequenceBlock(CurriculumInventorySequenceBlockInterface $sequenceBlock);

    public function getSequenceBlocks(): Collection;
}
