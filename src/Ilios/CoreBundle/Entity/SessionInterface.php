<?php

namespace Ilios\CoreBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Ilios\CoreBundle\Traits\IdentifiableEntityInterface;
use Ilios\CoreBundle\Traits\TitledEntityInterface;
use Ilios\CoreBundle\Traits\StringableEntityInterface;
use Ilios\CoreBundle\Traits\TimestampableEntityInterface;
use Ilios\CoreBundle\Traits\OfferingsEntityInterface;

/**
 * Interface SessionInterface
 * @package Ilios\CoreBundle\Entity
 */
interface SessionInterface extends
    IdentifiableEntityInterface,
    TitledEntityInterface,
    StringableEntityInterface,
    TimestampableEntityInterface,
    OfferingsEntityInterface,
    LoggableEntityInterface
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
     * @param boolean $deleted
     */
    public function setDeleted($deleted);

    /**
     * @return boolean
     */
    public function isDeleted();

    /**
     * @param boolean $publishedAsTbd
     */
    public function setPublishedAsTbd($publishedAsTbd);

    /**
     * @return boolean
     */
    public function isPublishedAsTbd();

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
     * @return CourseInterface
     */
    public function getCourse();

    /**
     * @param IlmSessionInterface $ilmSession
     */
    public function setIlmSession(IlmSessionInterface $ilmSession);

    /**
     * @return IlmSessionInterface
     */
    public function getIlmSession();

    /**
     * @param Collection $disciplines
     */
    public function setDisciplines(Collection $disciplines);

    /**
     * @param DisciplineInterface $discipline
     */
    public function addDiscipline(DisciplineInterface $discipline);

    /**
     * @return ArrayCollection|DisciplineInterface[]
     */
    public function getDisciplines();

    /**
     * @param Collection $objectives
     */
    public function setObjectives(Collection $objectives);

    /**
     * @param ObjectiveInterface $objective
     */
    public function addObjective(ObjectiveInterface $objective);

    /**
     * @return ArrayCollection|ObjectiveInterface[]
     */
    public function getObjectives();

    /**
     * @param Collection $meshDescriptors
     */
    public function setMeshDescriptors(Collection $meshDescriptors);

    /**
     * @param MeshDescriptorInterface $meshDescriptor
     */
    public function addMeshDescriptor(MeshDescriptorInterface $meshDescriptor);

    /**
     * @return ArrayCollection|MeshDescriptorInterface[]
     */
    public function getMeshDescriptors();

    /**
     * @param PublishEventInterface $publishEvent
     */
    public function setPublishEvent(PublishEventInterface $publishEvent);

    /**
     * @return PublishEventInterface
     */
    public function getPublishEvent();

    /**
     * @param SessionDescriptionInterface $sessionDescripiton
     */
    public function setSessionDescription(SessionDescriptionInterface $sessionDescripiton);

    /**
     * @return SessionDescription
     */
    public function getSessionDescription();
}
