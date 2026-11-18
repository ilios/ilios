<?php

declare(strict_types=1);

namespace App\Entity;

use App\Entity\CourseObjectivesEntityInterface;
use App\Entity\IndexableCoursesEntityInterface;
use App\Entity\TitledNullableEntityInterface;
use DateTime;
use Doctrine\Common\Collections\Collection;
use App\Entity\AdministratorsEntityInterface;
use App\Entity\ArchivableEntityInterface;
use App\Entity\CategorizableEntityInterface;
use App\Entity\CohortsEntityInterface;
use App\Entity\DirectorsEntityInterface;
use App\Entity\IdentifiableEntityInterface;
use App\Entity\LockableEntityInterface;
use App\Entity\MeshDescriptorsEntityInterface;
use App\Entity\PublishableEntityInterface;
use App\Entity\SchoolEntityInterface;
use App\Entity\StudentAdvisorsEntityInterface;
use App\Entity\SessionsEntityInterface;

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
