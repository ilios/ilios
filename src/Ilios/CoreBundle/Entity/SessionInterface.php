<?php

namespace Ilios\CoreBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;

use Ilios\CoreBundle\Traits\CategorizableEntityInterface;
use Ilios\CoreBundle\Traits\IdentifiableEntityInterface;
use Ilios\CoreBundle\Traits\ObjectivesEntityInterface;
use Ilios\CoreBundle\Traits\PublishableEntityInterface;
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
    LoggableEntityInterface,
    ObjectivesEntityInterface,
    PublishableEntityInterface,
    CategorizableEntityInterface
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
     * @deprecated
     * @param Collection $topics
     */
    public function setTopics(Collection $topics);

    /**
     * @deprecated
     * @param TopicInterface $topic
     */
    public function addTopic(TopicInterface $topic);

    /**
     * @deprecated
     * @return ArrayCollection|TopicInterface[]
     */
    public function getTopics();

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
     * @return ArrayCollection|SessionLearningMaterialInterface[]
     */
    public function getLearningMaterials();

    /**
     * @return SchoolInterface|null
     */
    public function getSchool();
}
