<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;

use App\Traits\AdministratorsEntityInterface;
use App\Traits\CategorizableEntityInterface;
use App\Traits\IdentifiableEntityInterface;
use App\Traits\MeshDescriptorsEntityInterface;
use App\Traits\ObjectivesEntityInterface;
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
    ObjectivesEntityInterface,
    PublishableEntityInterface,
    CategorizableEntityInterface,
    MeshDescriptorsEntityInterface,
    SequenceBlocksEntityInterface,
    AdministratorsEntityInterface
{
    /**
     * @param boolean $attireRequired
     */
    public function setAttireRequired($attireRequired);

    /**
     * @return boolean
     */
    public function isAttireRequired();

    /**
     * @param boolean $equipmentRequired
     */
    public function setEquipmentRequired($equipmentRequired);

    /**
     * @return boolean
     */
    public function isEquipmentRequired();

    /**
     * @param boolean $supplemental
     */
    public function setSupplemental($supplemental);

    /**
     * @return boolean
     */
    public function isSupplemental();

    /**
     * @param boolean $attendanceRequired
     */
    public function setAttendanceRequired($attendanceRequired);

    /**
     * @return boolean
     */
    public function isAttendanceRequired();

    /**
     * @return string
     */
    public function getInstructionalNotes(): ?string;

    /**
     * @param string $instructionalNotes
     */
    public function setInstructionalNotes(string $instructionalNotes = null): void;

    /**
     * @param SessionTypeInterface $sessionType
     */
    public function setSessionType(SessionTypeInterface $sessionType);

    /**
     * @return SessionTypeInterface
     */
    public function getSessionType();

    /**
     * @param CourseInterface $course
     */
    public function setCourse(CourseInterface $course);

    /**
     * @return CourseInterface|null
     */
    public function getCourse();

    /**
     * @param IlmSessionInterface $ilmSession
     */
    public function setIlmSession(IlmSessionInterface $ilmSession = null);

    /**
     * @return IlmSessionInterface
     */
    public function getIlmSession();

    /**
     * @param SessionDescriptionInterface $sessionDescripiton
     */
    public function setSessionDescription(SessionDescriptionInterface $sessionDescripiton);

    /**
     * @return SessionDescription
     */
    public function getSessionDescription();

    /**
     * @param Collection $learningMaterials
     */
    public function setLearningMaterials(Collection $learningMaterials = null);

    /**
     * @param SessionLearningMaterialInterface $learningMaterial
     */
    public function addLearningMaterial(SessionLearningMaterialInterface $learningMaterial);

    /**
     * @param SessionLearningMaterialInterface $learningMaterial
     */
    public function removeLearningMaterial(SessionLearningMaterialInterface $learningMaterial);

    /**
     * @return ArrayCollection|SessionLearningMaterialInterface[]
     */
    public function getLearningMaterials();

    /**
     * @return SchoolInterface|null
     */
    public function getSchool();

    /**
     * @param Collection $sequenceBlocks
     */
    public function setExcludedSequenceBlocks(Collection $sequenceBlocks);

    /**
     * @param CurriculumInventorySequenceBlockInterface $sequenceBlock
     */
    public function addExcludedSequenceBlock(CurriculumInventorySequenceBlockInterface $sequenceBlock);

    /**
     * @param CurriculumInventorySequenceBlockInterface $sequenceBlock
     */
    public function removeExcludedSequenceBlock(CurriculumInventorySequenceBlockInterface $sequenceBlock);

    /**
     * @return CurriculumInventorySequenceBlockInterface[]|ArrayCollection
     */
    public function getExcludedSequenceBlocks();

    /**
     * @param SessionInterface $ancestor
     */
    public function setPostrequisite(SessionInterface $ancestor);

    /**
     * @return SessionInterface
     */
    public function getPostrequisite();

    /**
     * @param Collection $children
     */
    public function setPrerequisites(Collection $children);

    /**
     * @param SessionInterface $child
     */
    public function addPrerequisite(SessionInterface $child);

    /**
     * @param SessionInterface $child
     */
    public function removePrerequisite(SessionInterface $child);

    /**
     * @return ArrayCollection|SessionInterface[]
     */
    public function getPrerequisites();
}
