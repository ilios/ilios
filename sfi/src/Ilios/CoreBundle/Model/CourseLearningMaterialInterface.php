<?php

namespace Ilios\CoreBundle\Model;

/**
 * Interface CourseLearningMaterialInterface
 */
interface CourseLearningMaterialInterface 
{
    public function getCourseLearningMaterialId();

    public function setNotes($notes);

    public function getNotes();

    public function setRequired($required);

    public function getRequired();

    public function setNotesArePublic($notesArePublic);

    public function getNotesArePublic();

    public function setCourse(\Ilios\CoreBundle\Model\Course $course = null);

    public function getCourse();

    public function setLearningMaterial(\Ilios\CoreBundle\Model\LearningMaterial $learningMaterial = null);

    public function getLearningMaterial();

    public function addMeshDescriptor(\Ilios\CoreBundle\Model\MeshDescriptor $meshDescriptors);

    public function removeMeshDescriptor(\Ilios\CoreBundle\Model\MeshDescriptor $meshDescriptors);

    public function getMeshDescriptors();
}

