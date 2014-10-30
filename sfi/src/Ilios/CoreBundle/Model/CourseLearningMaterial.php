<?php

namespace Ilios\CoreBundle\Model;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;

use Ilios\CoreBundle\Traits\IdentifiableEntity;


/**
 * CourseLearningMaterial
 */
class CourseLearningMaterial implements CourseLearningMaterialInterface
{
    use IdentifiableEntity;

    /**
     * @var string
     */
    protected $notes;

    /**
     * @var boolean
     */
    protected $required;

    /**
     * @var boolean
     */
    protected $publicNote;

    /**
     * @var CourseInterface
     */
    protected $course;

    /**
     * @var LearningMaterialInterface
     */
    protected $learningMaterial;

    /**
     * @var ArrayCollection|MeshDescriptor[]
     */
    protected $meshDescriptors;

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->meshDescriptors = new ArrayCollection();
    }

    /**
     * @param string $notes
     */
    public function setNotes($notes)
    {
        $this->notes = $notes;
    }

    /**
     * @return string
     */
    public function getNotes()
    {
        return $this->notes;
    }

    /**
     * @param boolean $required
     */
    public function setRequired($required)
    {
        $this->required = $required;
    }

    /**
     * @return boolean
     */
    public function isRequired()
    {
        return $this->required;
    }

    /**
     * @param boolean $publicNote
     */
    public function setPublicNote($publicNote)
    {
        $this->publicNote = $publicNote;
    }

    /**
     * @return boolean
     */
    public function hasPublicNote()
    {
        return $this->publicNote;
    }

    /**
     * @param CourseInterface $course
     */
    public function setCourse(CourseInterface $course)
    {
        $this->course = $course;
    }

    /**
     * @return CourseInterface
     */
    public function getCourse()
    {
        return $this->course;
    }

    /**
     * @param LearningMaterialInterface $learningMaterial
     */
    public function setLearningMaterial(LearningMaterialInterface $learningMaterial)
    {
        $this->learningMaterial = $learningMaterial;
    }

    /**
     * @return LearningMaterialInterface
     */
    public function getLearningMaterial()
    {
        return $this->learningMaterial;
    }

    /**
     * @param Collection $meshDescriptors
     */
    public function setMeshDescriptors(Collection $meshDescriptors)
    {
        $this->meshDescriptors = new ArrayCollection();

        foreach ($meshDescriptors as $meshDescriptor) {
            $this->addMeshDescriptor($meshDescriptor);
        }
    }

    /**
     * @param MeshDescriptorInterface $meshDescriptors
     */
    public function addMeshDescriptor(MeshDescriptorInterface $meshDescriptors)
    {
        $this->meshDescriptors->add($meshDescriptors);
    }

    /**
     * @return ArrayCollection|MeshDescriptorInterface[]
     */
    public function getMeshDescriptors()
    {
        return $this->meshDescriptors;
    }
}
