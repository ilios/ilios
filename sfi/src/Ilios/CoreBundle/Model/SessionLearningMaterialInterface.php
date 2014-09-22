<?php

namespace Ilios\CoreBundle\Model;



/**
 * Interface SessionLearningMaterialInterface
 */
interface SessionLearningMaterialInterface 
{
    public function getSessionLearningMaterialId();

    public function setNotes($notes);

    public function getNotes();

    public function setRequired($required);

    public function getRequired();

    public function setNotesArePublic($notesArePublic);

    public function getNotesArePublic();

    public function setSession(\Ilios\CoreBundle\Model\Session $session = null);

    public function getSession();

    public function setLearningMaterial(\Ilios\CoreBundle\Model\LearningMaterial $learningMaterial = null);

    public function getLearningMaterial();

    public function addMeshDescriptor(\Ilios\CoreBundle\Model\MeshDescriptor $meshDescriptors);

    public function removeMeshDescriptor(\Ilios\CoreBundle\Model\MeshDescriptor $meshDescriptors);

    public function getMeshDescriptors();
}
