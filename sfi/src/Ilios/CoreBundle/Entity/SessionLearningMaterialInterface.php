<?php

namespace Ilios\CoreBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Ilios\CoreBundle\Traits\IdentifiableEntityInterface;

/**
 * Interface SessionLearningMaterialInterface
 * @package Ilios\CoreBundle\Entity
 */
interface SessionLearningMaterialInterface extends IdentifiableEntityInterface
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
     * @param boolean $notesArePublic
     */
    public function setNotesArePublic($notesArePublic);

    /**
     * @todo: rename the property to something a bit more standardize, like publicNotes -> hasPublicNotes()
     * @return boolean
     */
    public function hasNotesArePublic();

    /**
     * @param SessionInterface $session
     */
    public function setSession(SessionInterface $session);

    /**
     * @return SessionInterface
     */
    public function getSession();

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
     * @param MeshDescriptorInterface $meshDescriptor
     */
    public function addMeshDescriptor(MeshDescriptorInterface $meshDescriptor);

    /**
     * @return ArrayCollection|MeshDescriptorInterface[]
     */
    public function getMeshDescriptors();
}