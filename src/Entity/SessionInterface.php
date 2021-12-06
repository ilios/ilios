<?php

declare(strict_types=1);

namespace App\Entity;

use App\Traits\DescribableEntityInterface;
use App\Traits\IndexableCoursesEntityInterface;
use App\Traits\SessionObjectivesEntityInterface;
use App\Traits\StudentAdvisorsEntityInterface;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use App\Traits\AdministratorsEntityInterface;
use App\Traits\CategorizableEntityInterface;
use App\Traits\IdentifiableEntityInterface;
use App\Traits\MeshDescriptorsEntityInterface;
use App\Traits\PublishableEntityInterface;
use App\Traits\SequenceBlocksEntityInterface;
use App\Traits\TitledEntityInterface;
use App\Traits\StringableEntityInterface;
use App\Traits\TimestampableEntityInterface;
use App\Traits\OfferingsEntityInterface;

/**
 * Interface SessionInterface
 */
interface SessionInterface extends
    IdentifiableEntityInterface,
    TitledEntityInterface,
    StringableEntityInterface,
    TimestampableEntityInterface,
    OfferingsEntityInterface,
    LoggableEntityInterface,
    PublishableEntityInterface,
    CategorizableEntityInterface,
    MeshDescriptorsEntityInterface,
    SequenceBlocksEntityInterface,
    AdministratorsEntityInterface,
    StudentAdvisorsEntityInterface,
    IndexableCoursesEntityInterface,
    SessionObjectivesEntityInterface,
    DescribableEntityInterface
{
    /**
     * @param bool $attireRequired
     */
    public function setAttireRequired($attireRequired);

    /**
     * @return bool
     */
    public function isAttireRequired(): bool;

    /**
     * @param bool $equipmentRequired
     */
    public function setEquipmentRequired($equipmentRequired);

    /**
     * @return bool
     */
    public function isEquipmentRequired(): bool;

    /**
     * @param bool $supplemental
     */
    public function setSupplemental($supplemental);

    /**
     * @return bool
     */
    public function isSupplemental(): bool;

    /**
     * @param bool $attendanceRequired
     */
    public function setAttendanceRequired($attendanceRequired);

    /**
     * @return bool
     */
    public function isAttendanceRequired(): bool;

    public function getInstructionalNotes(): ?string;

    public function setInstructionalNotes(string $instructionalNotes = null): void;

    public function setSessionType(SessionTypeInterface $sessionType);

    /**
     * @return SessionTypeInterface
     */
    public function getSessionType(): SessionTypeInterface;

    public function setCourse(CourseInterface $course);

    /**
     * @return CourseInterface|null
     */
    public function getCourse(): ?CourseInterface;

    public function setIlmSession(IlmSessionInterface $ilmSession = null);

    /**
     * @return IlmSessionInterface
     */
    public function getIlmSession(): IlmSessionInterface;

    public function setLearningMaterials(Collection $learningMaterials = null);

    public function addLearningMaterial(SessionLearningMaterialInterface $learningMaterial);

    public function removeLearningMaterial(SessionLearningMaterialInterface $learningMaterial);

    /**
     * @return ArrayCollection|SessionLearningMaterialInterface[]
     */
    public function getLearningMaterials(): Collection;

    /**
     * @return SchoolInterface|null
     */
    public function getSchool(): ?SchoolInterface;

    public function setExcludedSequenceBlocks(Collection $sequenceBlocks);

    public function addExcludedSequenceBlock(CurriculumInventorySequenceBlockInterface $sequenceBlock);

    public function removeExcludedSequenceBlock(CurriculumInventorySequenceBlockInterface $sequenceBlock);

    /**
     * @return CurriculumInventorySequenceBlockInterface[]|ArrayCollection
     */
    public function getExcludedSequenceBlocks(): Collection;

    public function setPostrequisite(SessionInterface $ancestor);

    /**
     * @return SessionInterface
     */
    public function getPostrequisite(): SessionInterface;

    public function setPrerequisites(Collection $children);

    public function addPrerequisite(SessionInterface $child);

    public function removePrerequisite(SessionInterface $child);

    /**
     * @return ArrayCollection|SessionInterface[]
     */
    public function getPrerequisites(): Collection;
}
