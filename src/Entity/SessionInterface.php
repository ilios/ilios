<?php

declare(strict_types=1);

namespace App\Entity;

use App\Traits\DescribableEntityInterface;
use App\Traits\IndexableCoursesEntityInterface;
use App\Traits\SessionObjectivesEntityInterface;
use App\Traits\StudentAdvisorsEntityInterface;
use App\Traits\TitledNullableEntityInterface;
use Doctrine\Common\Collections\Collection;
use App\Traits\AdministratorsEntityInterface;
use App\Traits\CategorizableEntityInterface;
use App\Traits\IdentifiableEntityInterface;
use App\Traits\MeshDescriptorsEntityInterface;
use App\Traits\PublishableEntityInterface;
use App\Traits\SequenceBlocksEntityInterface;
use App\Traits\StringableEntityToIdInterface;
use App\Traits\TimestampableEntityInterface;
use App\Traits\OfferingsEntityInterface;

/**
 * Interface SessionInterface
 */
interface SessionInterface extends
    IdentifiableEntityInterface,
    TitledNullableEntityInterface,
    StringableEntityToIdInterface,
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

    public function isAttireRequired(): ?bool;

    /**
     * @param bool $equipmentRequired
     */
    public function setEquipmentRequired($equipmentRequired);

    public function isEquipmentRequired(): ?bool;

    /**
     * @param bool $supplemental
     */
    public function setSupplemental($supplemental);

    public function isSupplemental(): ?bool;

    /**
     * @param bool $attendanceRequired
     */
    public function setAttendanceRequired($attendanceRequired);

    public function isAttendanceRequired(): ?bool;

    public function getInstructionalNotes(): ?string;

    public function setInstructionalNotes(string $instructionalNotes = null): void;

    public function setSessionType(SessionTypeInterface $sessionType);

    public function getSessionType(): SessionTypeInterface;

    public function setCourse(CourseInterface $course);

    public function getCourse(): ?CourseInterface;

    public function setIlmSession(IlmSessionInterface $ilmSession = null);

    public function getIlmSession(): ?IlmSessionInterface;

    public function setLearningMaterials(Collection $learningMaterials = null);

    public function addLearningMaterial(SessionLearningMaterialInterface $learningMaterial);

    public function removeLearningMaterial(SessionLearningMaterialInterface $learningMaterial);

    public function getLearningMaterials(): Collection;

    public function getSchool(): ?SchoolInterface;

    public function setExcludedSequenceBlocks(Collection $sequenceBlocks);

    public function addExcludedSequenceBlock(CurriculumInventorySequenceBlockInterface $sequenceBlock);

    public function removeExcludedSequenceBlock(CurriculumInventorySequenceBlockInterface $sequenceBlock);

    public function getExcludedSequenceBlocks(): Collection;

    public function setPostrequisite(SessionInterface $ancestor);

    public function getPostrequisite(): ?SessionInterface;

    public function setPrerequisites(Collection $children);

    public function addPrerequisite(SessionInterface $child);

    public function removePrerequisite(SessionInterface $child);

    public function getPrerequisites(): Collection;
}
