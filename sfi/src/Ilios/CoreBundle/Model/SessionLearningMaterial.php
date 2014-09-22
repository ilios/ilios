<?php

namespace Ilios\CoreBundle\Model;



/**
 * SessionLearningMaterial
 */
class SessionLearningMaterial
{
    /**
     * @var integer
     */
    private $sessionLearningMaterialId;

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
     * @var \Ilios\CoreBundle\Model\Session
     */
    private $session;

    /**
     * @var \Ilios\CoreBundle\Model\LearningMaterial
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
     * Get sessionLearningMaterialId
     *
     * @return integer 
     */
    public function getSessionLearningMaterialId()
    {
        return $this->sessionLearningMaterialId;
    }

    /**
     * Set notes
     *
     * @param string $notes
     * @return SessionLearningMaterial
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
     * @return SessionLearningMaterial
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
     * @return SessionLearningMaterial
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
     * Set session
     *
     * @param \Ilios\CoreBundle\Model\Session $session
     * @return SessionLearningMaterial
     */
    public function setSession(\Ilios\CoreBundle\Model\Session $session = null)
    {
        $this->session = $session;

        return $this;
    }

    /**
     * Get session
     *
     * @return \Ilios\CoreBundle\Model\Session 
     */
    public function getSession()
    {
        return $this->session;
    }

    /**
     * Set learningMaterial
     *
     * @param \Ilios\CoreBundle\Model\LearningMaterial $learningMaterial
     * @return SessionLearningMaterial
     */
    public function setLearningMaterial(\Ilios\CoreBundle\Model\LearningMaterial $learningMaterial = null)
    {
        $this->learningMaterial = $learningMaterial;

        return $this;
    }

    /**
     * Get learningMaterial
     *
     * @return \Ilios\CoreBundle\Model\LearningMaterial 
     */
    public function getLearningMaterial()
    {
        return $this->learningMaterial;
    }

    /**
     * Add meshDescriptors
     *
     * @param \Ilios\CoreBundle\Model\MeshDescriptor $meshDescriptors
     * @return SessionLearningMaterial
     */
    public function addMeshDescriptor(\Ilios\CoreBundle\Model\MeshDescriptor $meshDescriptors)
    {
        $this->meshDescriptors[] = $meshDescriptors;

        return $this;
    }

    /**
     * Remove meshDescriptors
     *
     * @param \Ilios\CoreBundle\Model\MeshDescriptor $meshDescriptors
     */
    public function removeMeshDescriptor(\Ilios\CoreBundle\Model\MeshDescriptor $meshDescriptors)
    {
        $this->meshDescriptors->removeElement($meshDescriptors);
    }

    /**
     * Get meshDescriptors
     *
     * @return \Ilios\CoreBundle\Model\MeshDescriptor[]
     */
    public function getMeshDescriptors()
    {
        return $this->meshDescriptors->toArray();
    }
}
