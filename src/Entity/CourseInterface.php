<?php

declare(strict_types=1);

namespace App\Entity;

use App\Traits\CourseObjectivesEntityInterface;
use App\Traits\IndexableCoursesEntityInterface;
use DateTime;
use Doctrine\Common\Collections\ArrayCollection;
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
use App\Traits\TitledEntityInterface;
use App\Traits\StringableEntityInterface;
use App\Traits\SessionsEntityInterface;

/**
 * Interface CourseInterface
 */
interface CourseInterface extends
    IdentifiableEntityInterface,
    TitledEntityInterface,
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

    /**
     * @return int
     */
    public function getLevel();

    /**
     * @param int $year
     */
    public function setYear($year);

    /**
     * @return int
     */
    public function getYear();

    public function setStartDate(DateTime $startDate);

    /**
     * @return DateTime
     */
    public function getStartDate();

    public function setEndDate(DateTime $endDate);

    /**
     * @return DateTime
     */
    public function getEndDate();

    /**
     * @todo: Possible rename.
     * @param string $externalId
     */
    public function setExternalId($externalId);

    /**
     * @todo: Possible rename.
     * @return string
     */
    public function getExternalId();

    public function setClerkshipType(CourseClerkshipTypeInterface $clerkshipType);

    /**
     * @return \App\Entity\CourseClerkshipType
     */
    public function getClerkshipType();

    public function setLearningMaterials(Collection $learningMaterials = null);

    public function addLearningMaterial(CourseLearningMaterialInterface $learningMaterial);

    public function removeLearningMaterial(CourseLearningMaterialInterface $learningMaterial);

    /**
     * @return ArrayCollection|CourseLearningMaterialInterface[]
     */
    public function getLearningMaterials();

    public function setAncestor(CourseInterface $ancestor);

    /**
     * @return CourseInterface
     */
    public function getAncestor();

    /**
     * @return CourseInterface
     */
    public function getAncestorOrSelf();

    public function setDescendants(Collection $children);

    public function addDescendant(CourseInterface $child);

    public function removeDescendant(CourseInterface $child);

    /**
     * @return ArrayCollection|CourseInterface[]
     */
    public function getDescendants();

    public function setSequenceBlocks(Collection $sequenceBlocks);

    public function addSequenceBlock(CurriculumInventorySequenceBlockInterface $sequenceBlock);

    public function removeSequenceBlock(CurriculumInventorySequenceBlockInterface $sequenceBlock);

    /**
     * @return CurriculumInventorySequenceBlockInterface[]|ArrayCollection
     */
    public function getSequenceBlocks();
}
