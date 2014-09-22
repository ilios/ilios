<?php

namespace Ilios\CoreBundle\Model;



/**
 * Program
 */
class Program
{
    /**
     * @var integer
     */
    private $programId;

    /**
     * @var string
     */
    private $title;

    /**
     * @var string
     */
    private $shortTitle;

    /**
     * @var integer
     */
    private $duration;

    /**
     * @var boolean
     */
    private $deleted;

    /**
     * @var boolean
     */
    private $publishedAsTbd;

    /**
     * @var \Ilios\CoreBundle\Model\PublishEvent
     */
    private $publishEvent;

    /**
     * @var \Ilios\CoreBundle\Model\School
     */
    private $owningSchool;


    /**
     * Get programId
     *
     * @return integer
     */
    public function getProgramId()
    {
        return $this->programId;
    }

    /**
     * Set title
     *
     * @param string $title
     * @return Program
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
     * Set shortTitle
     *
     * @param string $shortTitle
     * @return Program
     */
    public function setShortTitle($shortTitle)
    {
        $this->shortTitle = $shortTitle;

        return $this;
    }

    /**
     * Get shortTitle
     *
     * @return string
     */
    public function getShortTitle()
    {
        return $this->shortTitle;
    }

    /**
     * Set duration
     *
     * @param boolean $duration
     * @return Program
     */
    public function setDuration($duration)
    {
        $this->duration = $duration;

        return $this;
    }

    /**
     * Get duration
     *
     * @return boolean
     */
    public function getDuration()
    {
        return $this->duration;
    }

    /**
     * Set deleted
     *
     * @param boolean $deleted
     * @return Program
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
    public function isDeleted()
    {
        return $this->deleted;
    }

    /**
     * Set publishedAsTbd
     *
     * @param boolean $publishedAsTbd
     * @return Program
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
     * Set owningSchool
     *
     * @param \Ilios\CoreBundle\Model\School $school
     * @return Program
     */
    public function setOwningSchool(\Ilios\CoreBundle\Model\School $school = null)
    {
        $this->owningSchool = $school;

        return $this;
    }

    /**
     * @return \Ilios\CoreBundle\Model\School
     */
    public function getOwningSchool()
    {
        return $this->owningSchool;
    }

    /**
     * Set publishEvent
     *
     * @param \Ilios\CoreBundle\Model\PublishEvent $publishEvent
     * @return Program
     */
    public function setPublishEvent(\Ilios\CoreBundle\Model\PublishEvent $publishEvent = null)
    {
        $this->publishEvent = $publishEvent;

        return $this;
    }

    /**
     * @return \Ilios\CoreBundle\Model\PublishEvent
     */
    public function getPublishEvent()
    {
        return $this->publishEvent;
    }
}
