<?php

namespace Ilios\CoreBundle\Model;

use Doctrine\ORM\Mapping as ORM;

/**
 * Session
 */
class Session
{
    /**
     * @var int
     */
    protected $sessionId;

    /**
     * @var string
     */
    protected $title;

    /**
     * @var boolean
     */
    protected $attireRequired;

    /**
     * @var boolean
     */
    protected $equipmentRequired;

    /**
     * @var boolean
     */
    protected $supplemental;

    /**
     * @var boolean
     */
    protected $deleted;

    /**
     * @var boolean
     */
    protected $publishedAsTbd;

    /**
     * @var \DateTime
     */
    protected $lastUpdatedOn;

    /**
     * @var \Ilios\CoreBundle\Model\SessionType
     */
    protected $sessionType;

    /**
     * @var \Ilios\CoreBundle\Model\Course
     */
    protected $course;

    /**
     * @var \Ilios\CoreBundle\Model\IlmSessionFacet
     */
    protected $ilmSessionFacet;

    /**
     * @var \Doctrine\Common\Collections\Collection
     */
    protected $disciplines;

    /**
     * @var \Doctrine\Common\Collections\Collection
     */
    protected $objectives;

    /**
     * @var \Doctrine\Common\Collections\Collection
     */
    protected $meshDescriptors;

    /**
     * @var \Ilios\CoreBundle\Model\PublishEvent
     */
    protected $publishEvent;

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->attireRequired = false;
        $this->equipmentRequired = false;
        $this->supplemental = false;
        $this->deleted = false;
        $this->publishedAsTbd = false;

        $this->disciplines = new \Doctrine\Common\Collections\ArrayCollection();
        $this->objectives = new \Doctrine\Common\Collections\ArrayCollection();
        $this->meshDescriptors = new \Doctrine\Common\Collections\ArrayCollection();
    }

    /**
     * Get sessionId
     *
     * @return int
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
     * @param \Ilios\CoreBundle\Model\SessionType $sessionType
     * @return Session
     */
    public function setSessionType(\Ilios\CoreBundle\Model\SessionType $sessionType = null)
    {
        $this->sessionType = $sessionType;

        return $this;
    }

    /**
     * Get sessionType
     *
     * @return \Ilios\CoreBundle\Model\SessionType
     */
    public function getSessionType()
    {
        return $this->sessionType;
    }

    /**
     * Set course
     *
     * @param \Ilios\CoreBundle\Model\Course $course
     * @return Session
     */
    public function setCourse(\Ilios\CoreBundle\Model\Course $course = null)
    {
        $this->course = $course;

        return $this;
    }

    /**
     * Get course
     *
     * @return \Ilios\CoreBundle\Model\Course
     */
    public function getCourse()
    {
        return $this->course;
    }

    /**
     * Set ilmSessionFacet
     *
     * @param \Ilios\CoreBundle\Model\IlmSessionFacet $ilmSessionFacet
     * @return Session
     */
    public function setIlmSessionFacet(\Ilios\CoreBundle\Model\IlmSessionFacet $ilmSessionFacet = null)
    {
        $this->ilmSessionFacet = $ilmSessionFacet;

        return $this;
    }

    /**
     * Get ilmSessionFacet
     *
     * @return \Ilios\CoreBundle\Model\IlmSessionFacet
     */
    public function getIlmSessionFacet()
    {
        return $this->ilmSessionFacet;
    }

    /**
     * Add disciplines
     *
     * @param \Ilios\CoreBundle\Model\Discipline $disciplines
     * @return Session
     */
    public function addDiscipline(\Ilios\CoreBundle\Model\Discipline $disciplines)
    {
        $this->disciplines[] = $disciplines;

        return $this;
    }

    /**
     * Remove disciplines
     *
     * @param \Ilios\CoreBundle\Model\Discipline $disciplines
     */
    public function removeDiscipline(\Ilios\CoreBundle\Model\Discipline $disciplines)
    {
        $this->disciplines->removeElement($disciplines);
    }

    /**
     * Get disciplines
     *
     * @return \Ilios\CoreBundle\Model\Discipline[]
     */
    public function getDisciplines()
    {
        return $this->disciplines->toArray();
    }

    /**
     * Add objectives
     *
     * @param \Ilios\CoreBundle\Model\Objective $objectives
     * @return Session
     */
    public function addObjective(\Ilios\CoreBundle\Model\Objective $objectives)
    {
        $this->objectives[] = $objectives;

        return $this;
    }

    /**
     * Remove objectives
     *
     * @param \Ilios\CoreBundle\Model\Objective $objectives
     */
    public function removeObjective(\Ilios\CoreBundle\Model\Objective $objectives)
    {
        $this->objectives->removeElement($objectives);
    }

    /**
     * Get objectives
     *
     * @return \Ilios\CoreBundle\Model\Objective[]
     */
    public function getObjectives()
    {
        return $this->objectives->toArray();
    }

    /**
     * Add meshDescriptors
     *
     * @param \Ilios\CoreBundle\Model\MeshDescriptor $meshDescriptors
     * @return Session
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

    /**
     * Set publishEvent
     *
     * @param \Ilios\CoreBundle\Model\PublishEvent $publishEvent
     * @return Session
     */
    public function setPublishEvent(\Ilios\CoreBundle\Model\PublishEvent $publishEvent = null)
    {
        $this->publishEvent = $publishEvent;

        return $this;
    }

    /**
     * Get publishEvent
     *
     * @return \Ilios\CoreBundle\Model\PublishEvent
     */
    public function getPublishEvent()
    {
        return $this->publishEvent;
    }
}
