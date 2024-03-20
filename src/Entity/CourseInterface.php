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
use App\Traits\SessionsEntityInterface;

interface CourseInterface extends
    IdentifiableEntityInterface,
    TitledNullableEntityInterface,
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
    public function setLevel(int $level): void;
    public function getLevel(): int;

    public function setYear(int $year): void;
    public function getYear(): int;

    public function setStartDate(?DateTime $startDate = null): void;
    public function getStartDate(): DateTime;

    public function setEndDate(?DateTime $endDate = null): void;
    public function getEndDate(): DateTime;

    public function setExternalId(?string $externalId): void;
    public function getExternalId(): ?string;

    public function setClerkshipType(?CourseClerkshipTypeInterface $clerkshipType): void;
    public function getClerkshipType(): ?CourseClerkshipTypeInterface;

    public function setLearningMaterials(?Collection $learningMaterials = null): void;
    public function addLearningMaterial(CourseLearningMaterialInterface $learningMaterial): void;
    public function removeLearningMaterial(CourseLearningMaterialInterface $learningMaterial): void;
    public function getLearningMaterials(): Collection;

    public function setAncestor(?CourseInterface $ancestor = null): void;
    public function getAncestor(): ?CourseInterface;
    public function getAncestorOrSelf(): CourseInterface;

    public function setDescendants(Collection $descendants): void;
    public function addDescendant(CourseInterface $descendant): void;
    public function removeDescendant(CourseInterface $descendant): void;
    public function getDescendants(): Collection;

    public function setSequenceBlocks(Collection $sequenceBlocks): void;
    public function addSequenceBlock(CurriculumInventorySequenceBlockInterface $sequenceBlock): void;
    public function removeSequenceBlock(CurriculumInventorySequenceBlockInterface $sequenceBlock): void;
    public function getSequenceBlocks(): Collection;
}
