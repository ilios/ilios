<?php

declare(strict_types=1);

namespace App\Entity;

use App\Traits\DescribableNullableEntityInterface;
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
use App\Traits\TimestampableEntityInterface;
use App\Traits\OfferingsEntityInterface;

interface SessionInterface extends
    IdentifiableEntityInterface,
    TitledNullableEntityInterface,
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
    DescribableNullableEntityInterface
{
    public function setAttireRequired(?bool $attireRequired): void;
    public function isAttireRequired(): ?bool;

    public function setEquipmentRequired(?bool $equipmentRequired): void;
    public function isEquipmentRequired(): ?bool;

    public function setSupplemental(?bool $supplemental): void;
    public function isSupplemental(): ?bool;

    public function setAttendanceRequired(?bool $attendanceRequired): void;
    public function isAttendanceRequired(): ?bool;

    public function getInstructionalNotes(): ?string;
    public function setInstructionalNotes(?string $instructionalNotes = null): void;

    public function setSessionType(SessionTypeInterface $sessionType): void;
    public function getSessionType(): SessionTypeInterface;

    public function setCourse(CourseInterface $course): void;
    public function getCourse(): CourseInterface;

    public function setIlmSession(?IlmSessionInterface $ilmSession = null): void;
    public function getIlmSession(): ?IlmSessionInterface;

    public function setLearningMaterials(?Collection $learningMaterials = null): void;
    public function addLearningMaterial(SessionLearningMaterialInterface $learningMaterial): void;
    public function removeLearningMaterial(SessionLearningMaterialInterface $learningMaterial): void;
    public function getLearningMaterials(): Collection;

    public function getSchool(): SchoolInterface;

    public function setExcludedSequenceBlocks(Collection $sequenceBlocks): void;
    public function addExcludedSequenceBlock(CurriculumInventorySequenceBlockInterface $sequenceBlock): void;
    public function removeExcludedSequenceBlock(CurriculumInventorySequenceBlockInterface $sequenceBlock): void;
    public function getExcludedSequenceBlocks(): Collection;

    public function setPostrequisite(SessionInterface $postrequisite): void;
    public function getPostrequisite(): ?SessionInterface;

    public function setPrerequisites(Collection $prerequisites): void;
    public function addPrerequisite(SessionInterface $prerequisite): void;
    public function removePrerequisite(SessionInterface $prerequisite): void;
    public function getPrerequisites(): Collection;
}
