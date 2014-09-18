<?php

namespace Ilios\CoreBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * CourseLearningMaterial
 */
class CourseLearningMaterial
{
    /**
     * @var integer
     */
    private $courseLearningMaterialId;

    /**
     * @var string
     */
    private $notes;

    /**
     * @var boolean
     */
    private $required;

    /**
     * @var boolean
     */
    private $notesArePublic;

    /**
     * @var \Ilios\CoreBundle\Entity\Course
     */
    private $course;

    /**
     * @var \Ilios\CoreBundle\Entity\LearningMaterial
     */
    private $learningMaterial;

    /**
     * @var \Doctrine\Common\Collections\Collection
     */
    private $meshDescriptors;

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->meshDescriptors = new \Doctrine\Common\Collections\ArrayCollection();
    }

    /**
     * Get courseLearningMaterialId
     *
     * @return integer 
     */
    public function getCourseLearningMaterialId()
    {
        return $this->courseLearningMaterialId;
    }

    /**
     * Set notes
     *
     * @param string $notes
     * @return CourseLearningMaterial
     */
    public function setNotes($notes)
    {
        $this->notes = $notes;

        return $this;
    }

    /**
     * Get notes
     *
     * @return string 
     */
    public function getNotes()
    {
        return $this->notes;
    }

    /**
     * Set required
     *
     * @param boolean $required
     * @return CourseLearningMaterial
     */
    public function setRequired($required)
    {
        $this->required = $required;

        return $this;
    }

    /**
     * Get required
     *
     * @return boolean 
     */
    public function getRequired()
    {
        return $this->required;
    }

    /**
     * Set notesArePublic
     *
     * @param boolean $notesArePublic
     * @return CourseLearningMaterial
     */
    public function setNotesArePublic($notesArePublic)
    {
        $this->notesArePublic = $notesArePublic;

        return $this;
    }

    /**
     * Get notesArePublic
     *
     * @return boolean 
     */
    public function getNotesArePublic()
    {
        return $this->notesArePublic;
    }

    /**
     * Set course
     *
     * @param \Ilios\CoreBundle\Entity\Course $course
     * @return CourseLearningMaterial
     */
    public function setCourse(\Ilios\CoreBundle\Entity\Course $course = null)
    {
        $this->course = $course;

        return $this;
    }

    /**
     * Get course
     *
     * @return \Ilios\CoreBundle\Entity\Course 
     */
    public function getCourse()
    {
        return $this->course;
    }

    /**
     * Set learningMaterial
     *
     * @param \Ilios\CoreBundle\Entity\LearningMaterial $learningMaterial
     * @return CourseLearningMaterial
     */
    public function setLearningMaterial(\Ilios\CoreBundle\Entity\LearningMaterial $learningMaterial = null)
    {
        $this->learningMaterial = $learningMaterial;

        return $this;
    }

    /**
     * Get learningMaterial
     *
     * @return \Ilios\CoreBundle\Entity\LearningMaterial 
     */
    public function getLearningMaterial()
    {
        return $this->learningMaterial;
    }

    /**
     * Add meshDescriptors
     *
     * @param \Ilios\CoreBundle\Entity\MeshDescriptor $meshDescriptors
     * @return CourseLearningMaterial
     */
    public function addMeshDescriptor(\Ilios\CoreBundle\Entity\MeshDescriptor $meshDescriptors)
    {
        $this->meshDescriptors[] = $meshDescriptors;

        return $this;
    }

    /**
     * Remove meshDescriptors
     *
     * @param \Ilios\CoreBundle\Entity\MeshDescriptor $meshDescriptors
     */
    public function removeMeshDescriptor(\Ilios\CoreBundle\Entity\MeshDescriptor $meshDescriptors)
    {
        $this->meshDescriptors->removeElement($meshDescriptors);
    }

    /**
     * Get meshDescriptors
     *
     * @return \Ilios\CoreBundle\Entity\MeshDescriptor[]
     */
    public function getMeshDescriptors()
    {
        return $this->meshDescriptors->toArray();
    }
}
