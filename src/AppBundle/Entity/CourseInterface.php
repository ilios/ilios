<?php

namespace AppBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;

use AppBundle\Traits\AdministratorsEntityInterface;
use AppBundle\Traits\ArchivableEntityInterface;
use AppBundle\Traits\CategorizableEntityInterface;
use AppBundle\Traits\CohortsEntityInterface;
use AppBundle\Traits\DirectorsEntityInterface;
use AppBundle\Traits\IdentifiableEntityInterface;
use AppBundle\Traits\LockableEntityInterface;
use AppBundle\Traits\MeshDescriptorsEntityInterface;
use AppBundle\Traits\ObjectivesEntityInterface;
use AppBundle\Traits\PublishableEntityInterface;
use AppBundle\Traits\SchoolEntityInterface;
use AppBundle\Traits\TitledEntityInterface;
use AppBundle\Traits\StringableEntityInterface;
use AppBundle\Traits\SessionsEntityInterface;

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
    LoggableEntityInterface,
    ObjectivesEntityInterface,
    PublishableEntityInterface,
    CategorizableEntityInterface,
    CohortsEntityInterface,
    MeshDescriptorsEntityInterface,
    DirectorsEntityInterface,
    AdministratorsEntityInterface
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

    /**
     * @param \DateTime $startDate
     */
    public function setStartDate(\DateTime $startDate);

    /**
     * @return \DateTime
     */
    public function getStartDate();

    /**
     * @param \DateTime $endDate
     */
    public function setEndDate(\DateTime $endDate);

    /**
     * @return \DateTime
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

    /**
     * @param CourseClerkshipTypeInterface $clerkshipType
     */
    public function setClerkshipType(CourseClerkshipTypeInterface $clerkshipType);

    /**
     * @return \AppBundle\Entity\CourseClerkshipType
     */
    public function getClerkshipType();

    /**
     * @param Collection $learningMaterials
     */
    public function setLearningMaterials(Collection $learningMaterials = null);

    /**
     * @param CourseLearningMaterialInterface $learningMaterial
     */
    public function addLearningMaterial(CourseLearningMaterialInterface $learningMaterial);

    /**
     * @param CourseLearningMaterialInterface $learningMaterial
     */
    public function removeLearningMaterial(CourseLearningMaterialInterface $learningMaterial);

    /**
     * @return ArrayCollection|CourseLearningMaterialInterface[]
     */
    public function getLearningMaterials();

    /**
     * @param CourseInterface $ancestor
     */
    public function setAncestor(CourseInterface $ancestor);

    /**
     * @return CourseInterface
     */
    public function getAncestor();

    /**
     * @return CourseInterface
     */
    public function getAncestorOrSelf();

    /**
     * @param Collection $children
     */
    public function setDescendants(Collection $children);

    /**
     * @param CourseInterface $child
     */
    public function addDescendant(CourseInterface $child);

    /**
     * @param CourseInterface $child
     */
    public function removeDescendant(CourseInterface $child);

    /**
     * @return ArrayCollection|CourseInterface[]
     */
    public function getDescendants();

    /**
     * @param Collection $sequenceBlocks
     */
    public function setSequenceBlocks(Collection $sequenceBlocks);

    /**
     * @param CurriculumInventorySequenceBlockInterface $sequenceBlock
     */
    public function addSequenceBlock(CurriculumInventorySequenceBlockInterface $sequenceBlock);

    /**
     * @param CurriculumInventorySequenceBlockInterface $sequenceBlock
     */
    public function removeSequenceBlock(CurriculumInventorySequenceBlockInterface $sequenceBlock);

    /**
     * @return CurriculumInventorySequenceBlockInterface[]|ArrayCollection
     */
    public function getSequenceBlocks();
}
