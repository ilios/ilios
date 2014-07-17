<?php

namespace Ilios\CoreBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Session
 */
class Session
{
    /**
     * @var integer
     */
    private $sessionId;

    /**
     * @var string
     */
    private $title;

    /**
     * @var boolean
     */
    private $attireRequired;

    /**
     * @var boolean
     */
    private $equipmentRequired;

    /**
     * @var boolean
     */
    private $supplemental;

    /**
     * @var boolean
     */
    private $deleted;

    /**
     * @var boolean
     */
    private $publishedAsTbd;

    /**
     * @var \DateTime
     */
    private $lastUpdatedOn;

    /**
     * @var \Ilios\CoreBundle\Entity\SessionType
     */
    private $sessionType;

    /**
     * @var \Ilios\CoreBundle\Entity\Course
     */
    private $course;

    /**
     * @var \Ilios\CoreBundle\Entity\IlmSessionFacet
     */
    private $ilmSessionFacet;

    /**
     * @var \Doctrine\Common\Collections\Collection
     */
    private $disciplines;

    /**
     * @var \Doctrine\Common\Collections\Collection
     */
    private $objectives;

    /**
     * @var \Doctrine\Common\Collections\Collection
     */
    private $meshDescriptors;
    
    /**
     * @var \Ilios\CoreBundle\Entity\PublishEvent
     */
    private $publishEvent;

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->disciplines = new \Doctrine\Common\Collections\ArrayCollection();
        $this->objectives = new \Doctrine\Common\Collections\ArrayCollection();
        $this->meshDescriptors = new \Doctrine\Common\Collections\ArrayCollection();
    }

    /**
     * Get sessionId
     *
     * @return integer 
     */
    public function getSessionId()
    {
        return $this->sessionId;
    }

    /**
     * Set title
     *
     * @param string $title
     * @return Session
     */
    public function setTitle($title)
    {
        $this->title = $title;

        return $this;
    }

    /**
     * Get title
     *
     * @return string 
     */
    public function getTitle()
    {
        return $this->title;
    }

    /**
     * Set attireRequired
     *
     * @param boolean $attireRequired
     * @return Session
     */
    public function setAttireRequired($attireRequired)
    {
        $this->attireRequired = $attireRequired;

        return $this;
    }

    /**
     * Get attireRequired
     *
     * @return boolean 
     */
    public function getAttireRequired()
    {
        return $this->attireRequired;
    }

    /**
     * Set equipmentRequired
     *
     * @param boolean $equipmentRequired
     * @return Session
     */
    public function setEquipmentRequired($equipmentRequired)
    {
        $this->equipmentRequired = $equipmentRequired;

        return $this;
    }

    /**
     * Get equipmentRequired
     *
     * @return boolean 
     */
    public function getEquipmentRequired()
    {
        return $this->equipmentRequired;
    }

    /**
     * Set supplemental
     *
     * @param boolean $supplemental
     * @return Session
     */
    public function setSupplemental($supplemental)
    {
        $this->supplemental = $supplemental;

        return $this;
    }

    /**
     * Get supplemental
     *
     * @return boolean 
     */
    public function getSupplemental()
    {
        return $this->supplemental;
    }

    /**
     * Set deleted
     *
     * @param boolean $deleted
     * @return Session
     */
    public function setDeleted($deleted)
    {
        $this->deleted = $deleted;

        return $this;
    }

    /**
     * Get deleted
     *
     * @return boolean 
     */
    public function getDeleted()
    {
        return $this->deleted;
    }

    /**
     * Set publishedAsTbd
     *
     * @param boolean $publishedAsTbd
     * @return Session
     */
    public function setPublishedAsTbd($publishedAsTbd)
    {
        $this->publishedAsTbd = $publishedAsTbd;

        return $this;
    }

    /**
     * Get publishedAsTbd
     *
     * @return boolean 
     */
    public function getPublishedAsTbd()
    {
        return $this->publishedAsTbd;
    }

    /**
     * Set lastUpdatedOn
     *
     * @param \DateTime $lastUpdatedOn
     * @return Session
     */
    public function setLastUpdatedOn($lastUpdatedOn)
    {
        $this->lastUpdatedOn = $lastUpdatedOn;

        return $this;
    }

    /**
     * Get lastUpdatedOn
     *
     * @return \DateTime 
     */
    public function getLastUpdatedOn()
    {
        return $this->lastUpdatedOn;
    }

    /**
     * Set sessionType
     *
     * @param \Ilios\CoreBundle\Entity\SessionType $sessionType
     * @return Session
     */
    public function setSessionType(\Ilios\CoreBundle\Entity\SessionType $sessionType = null)
    {
        $this->sessionType = $sessionType;

        return $this;
    }

    /**
     * Get sessionType
     *
     * @return \Ilios\CoreBundle\Entity\SessionType 
     */
    public function getSessionType()
    {
        return $this->sessionType;
    }

    /**
     * Set course
     *
     * @param \Ilios\CoreBundle\Entity\Course $course
     * @return Session
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
     * Set ilmSessionFacet
     *
     * @param \Ilios\CoreBundle\Entity\IlmSessionFacet $ilmSessionFacet
     * @return Session
     */
    public function setIlmSessionFacet(\Ilios\CoreBundle\Entity\IlmSessionFacet $ilmSessionFacet = null)
    {
        $this->ilmSessionFacet = $ilmSessionFacet;

        return $this;
    }

    /**
     * Get ilmSessionFacet
     *
     * @return \Ilios\CoreBundle\Entity\IlmSessionFacet 
     */
    public function getIlmSessionFacet()
    {
        return $this->ilmSessionFacet;
    }

    /**
     * Add disciplines
     *
     * @param \Ilios\CoreBundle\Entity\Discipline $disciplines
     * @return Session
     */
    public function addDiscipline(\Ilios\CoreBundle\Entity\Discipline $disciplines)
    {
        $this->disciplines[] = $disciplines;

        return $this;
    }

    /**
     * Remove disciplines
     *
     * @param \Ilios\CoreBundle\Entity\Discipline $disciplines
     */
    public function removeDiscipline(\Ilios\CoreBundle\Entity\Discipline $disciplines)
    {
        $this->disciplines->removeElement($disciplines);
    }

    /**
     * Get disciplines
     *
     * @return \Ilios\CoreBundle\Entity\Discipline[]
     */
    public function getDisciplines()
    {
        return $this->disciplines->toArray();
    }

    /**
     * Add objectives
     *
     * @param \Ilios\CoreBundle\Entity\Objective $objectives
     * @return Session
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
     * Add meshDescriptors
     *
     * @param \Ilios\CoreBundle\Entity\MeshDescriptor $meshDescriptors
     * @return Session
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

    /**
     * Set publishEvent
     *
     * @param \Ilios\CoreBundle\Entity\PublishEvent $publishEvent
     * @return Session
     */
    public function setPublishEvent(\Ilios\CoreBundle\Entity\PublishEvent $publishEvent = null)
    {
        $this->publishEvent = $publishEvent;

        return $this;
    }

    /**
     * Get publishEvent
     *
     * @return \Ilios\CoreBundle\Entity\PublishEvent 
     */
    public function getPublishEvent()
    {
        return $this->publishEvent;
    }
}
