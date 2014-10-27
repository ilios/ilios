<?php

namespace Ilios\CoreBundle\Model;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;

use Ilios\CoreBundle\Traits\IdentifiableTraitInterface;

/**
 * Interface CourseLearningMaterialInterface
 * @package Ilios\CoreBundle\Model
 */
interface CourseLearningMaterialInterface extends IdentifiableTraitInterface
{
    /**
     * @param string $notes
     */
    public function setNotes($notes);

    /**
     * @return string
     */
    public function getNotes();

    /**
     * @param boolean $required
     */
    public function setRequired($required);

    /**
     * @return boolean
     */
    public function isRequired();

    /**
     * @param boolean $publicNote
     */
    public function setPublicNote($publicNote);

    /**
     * @return boolean
     */
    public function hasPublicNote();

    /**
     * @param CourseInterface $course
     */
    public function setCourse(CourseInterface $course);

    /**
     * @return CourseInterface
     */
    public function getCourse();

    /**
     * @param LearningMaterialInterface $learningMaterial
     */
    public function setLearningMaterial(LearningMaterialInterface $learningMaterial);

    /**
     * @return LearningMaterialInterface
     */
    public function getLearningMaterial();

    /**
     * @param Collection $meshDescriptors
     */
    public function setMeshDescriptors(Collection $meshDescriptors);

    /**
     * @param MeshDescriptorInterface $meshDescriptors
     */
    public function addMeshDescriptor(MeshDescriptorInterface $meshDescriptors);

    /**
     * @return ArrayCollection|MeshDescriptorInterface[]
     */
    public function getMeshDescriptors();
}

