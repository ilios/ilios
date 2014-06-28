<?php

namespace Ilios\CoreBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * MeshDescriptor
 */
class MeshDescriptor
{
    /**
     * @var string
     */
    private $meshDescriptorUid;

    /**
     * @var string
     */
    private $name;

    /**
     * @var string
     */
    private $annotation;

    /**
     * @var \DateTime
     */
    private $createdAt;

    /**
     * @var \DateTime
     */
    private $updatedAt;

    /**
     * @var \Doctrine\Common\Collections\Collection
     */
    private $courses;

    /**
     * @var \Doctrine\Common\Collections\Collection
     */
    private $objectives;

    /**
     * @var \Doctrine\Common\Collections\Collection
     */
    private $sessions;

    /**
     * @var \Doctrine\Common\Collections\Collection
     */
    private $sessionLearningMaterials;

    /**
     * @var \Doctrine\Common\Collections\Collection
     */
    private $courseLearningMaterials;

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->courses = new \Doctrine\Common\Collections\ArrayCollection();
        $this->objectives = new \Doctrine\Common\Collections\ArrayCollection();
        $this->sessions = new \Doctrine\Common\Collections\ArrayCollection();
        $this->sessionLearningMaterials = new \Doctrine\Common\Collections\ArrayCollection();
        $this->courseLearningMaterials = new \Doctrine\Common\Collections\ArrayCollection();
    }

    /**
     * Set meshDescriptorUid
     *
     * @param string $meshDescriptorUid
     * @return MeshDescriptor
     */
    public function setMeshDescriptorUid($meshDescriptorUid)
    {
        $this->meshDescriptorUid = $meshDescriptorUid;

        return $this;
    }

    /**
     * Get meshDescriptorUid
     *
     * @return string 
     */
    public function getMeshDescriptorUid()
    {
        return $this->meshDescriptorUid;
    }

    /**
     * Set name
     *
     * @param string $name
     * @return MeshDescriptor
     */
    public function setName($name)
    {
        $this->name = $name;

        return $this;
    }

    /**
     * Get name
     *
     * @return string 
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * Set annotation
     *
     * @param string $annotation
     * @return MeshDescriptor
     */
    public function setAnnotation($annotation)
    {
        $this->annotation = $annotation;

        return $this;
    }

    /**
     * Get annotation
     *
     * @return string 
     */
    public function getAnnotation()
    {
        return $this->annotation;
    }

    /**
     * Set createdAt
     *
     * @param \DateTime $createdAt
     * @return MeshDescriptor
     */
    public function setCreatedAt($createdAt)
    {
        $this->createdAt = $createdAt;

        return $this;
    }

    /**
     * Get createdAt
     *
     * @return \DateTime 
     */
    public function getCreatedAt()
    {
        return $this->createdAt;
    }

    /**
     * Set updatedAt
     *
     * @param \DateTime $updatedAt
     * @return MeshDescriptor
     */
    public function setUpdatedAt($updatedAt)
    {
        $this->updatedAt = $updatedAt;

        return $this;
    }

    /**
     * Get updatedAt
     *
     * @return \DateTime 
     */
    public function getUpdatedAt()
    {
        return $this->updatedAt;
    }

    /**
     * Add courses
     *
     * @param \Ilios\CoreBundle\Entity\Course $courses
     * @return MeshDescriptor
     */
    public function addCourse(\Ilios\CoreBundle\Entity\Course $courses)
    {
        $this->courses[] = $courses;

        return $this;
    }

    /**
     * Remove courses
     *
     * @param \Ilios\CoreBundle\Entity\Course $courses
     */
    public function removeCourse(\Ilios\CoreBundle\Entity\Course $courses)
    {
        $this->courses->removeElement($courses);
    }

    /**
     * Get courses
     *
     * @return \Ilios\CoreBundle\Entity\Course[]
     */
    public function getCourses()
    {
        return $this->courses->toArray();
    }

    /**
     * Add objectives
     *
     * @param \Ilios\CoreBundle\Entity\Objective $objectives
     * @return MeshDescriptor
     */
    public function addObjective(\Ilios\CoreBundle\Entity\Objective $objectives)
    {
        $this->objectives[] = $objectives;

        return $this;
    }

    /**
     * Remove objectives
     *
     * @param \Ilios\CoreBundle\Entity\Objective $objectives
     */
    public function removeObjective(\Ilios\CoreBundle\Entity\Objective $objectives)
    {
        $this->objectives->removeElement($objectives);
    }

    /**
     * Get objectives
     *
     * @return \Ilios\CoreBundle\Entity\Objective[]
     */
    public function getObjectives()
    {
        return $this->objectives->toArray();
    }

    /**
     * Add sessions
     *
     * @param \Ilios\CoreBundle\Entity\Session $sessions
     * @return MeshDescriptor
     */
    public function addSession(\Ilios\CoreBundle\Entity\Session $sessions)
    {
        $this->sessions[] = $sessions;

        return $this;
    }

    /**
     * Remove sessions
     *
     * @param \Ilios\CoreBundle\Entity\Session $sessions
     */
    public function removeSession(\Ilios\CoreBundle\Entity\Session $sessions)
    {
        $this->sessions->removeElement($sessions);
    }

    /**
     * Get sessions
     *
     * @return \Ilios\CoreBundle\Entity\Session[]
     */
    public function getSessions()
    {
        return $this->sessions->toArray();
    }

    /**
     * Add sessionLearningMaterials
     *
     * @param \Ilios\CoreBundle\Entity\SessionLearningMaterial $sessionLearningMaterials
     * @return MeshDescriptor
     */
    public function addSessionLearningMaterial(
        \Ilios\CoreBundle\Entity\SessionLearningMaterial $sessionLearningMaterials
    ) {
        $this->sessionLearningMaterials[] = $sessionLearningMaterials;

        return $this;
    }

    /**
     * Remove sessionLearningMaterials
     *
     * @param \Ilios\CoreBundle\Entity\SessionLearningMaterial $sessionLearningMaterials
     */
    public function removeSessionLearningMaterial(
        \Ilios\CoreBundle\Entity\SessionLearningMaterial $sessionLearningMaterials
    ) {
        $this->sessionLearningMaterials->removeElement($sessionLearningMaterials);
    }

    /**
     * Get sessionLearningMaterials
     *
     * @return \Ilios\CoreBundle\Entity\SessionLearningMaterial[]
     */
    public function getSessionLearningMaterials()
    {
        return $this->sessionLearningMaterials->toArray();
    }

    /**
     * Add courseLearningMaterials
     *
     * @param \Ilios\CoreBundle\Entity\CourseLearningMaterial $courseLearningMaterials
     * @return MeshDescriptor
     */
    public function addCourseLearningMaterial(\Ilios\CoreBundle\Entity\CourseLearningMaterial $courseLearningMaterials)
    {
        $this->courseLearningMaterials[] = $courseLearningMaterials;

        return $this;
    }

    /**
     * Remove courseLearningMaterials
     *
     * @param \Ilios\CoreBundle\Entity\CourseLearningMaterial $courseLearningMaterials
     */
    public function removeCourseLearningMaterial(
        \Ilios\CoreBundle\Entity\CourseLearningMaterial $courseLearningMaterials
    ) {
        $this->courseLearningMaterials->removeElement($courseLearningMaterials);
    }

    /**
     * Get courseLearningMaterials
     *
     * @return \Ilios\CoreBundle\Entity\CourseLearningMaterial[]
     */
    public function getCourseLearningMaterials()
    {
        return $this->courseLearningMaterials->toArray();
    }
}
