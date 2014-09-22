<?php

namespace Ilios\CoreBundle\Model;



/**
 * SessionType
 */
class SessionType
{
    /**
     * @var integer
     */
    private $sessionTypeId;

    /**
     * @var string
     */
    private $title;

    /**
     * @var string
     */
    private $sessionTypeCssClass;

    /**
     * @var boolean
     */
    private $assessment;

    /**
     * @var \Ilios\CoreBundle\Model\AssessmentOption
     */
    private $assessmentOption;

    /**
     * @var \Ilios\CoreBundle\Model\School
     */
    private $owningSchool;

    /**
     * @var \Doctrine\Common\Collections\Collection
     */
    private $aamcMethods;

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->aamcMethods = new \Doctrine\Common\Collections\ArrayCollection();
    }

    /**
     * Get sessionTypeId
     *
     * @return integer 
     */
    public function getSessionTypeId()
    {
        return $this->sessionTypeId;
    }

    /**
     * Set title
     *
     * @param string $title
     * @return SessionType
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
     * Set sessionTypeCssClass
     *
     * @param string $sessionTypeCssClass
     * @return SessionType
     */
    public function setSessionTypeCssClass($sessionTypeCssClass)
    {
        $this->sessionTypeCssClass = $sessionTypeCssClass;

        return $this;
    }

    /**
     * Get sessionTypeCssClass
     *
     * @return string 
     */
    public function getSessionTypeCssClass()
    {
        return $this->sessionTypeCssClass;
    }

    /**
     * Set assessment
     *
     * @param boolean $assessment
     * @return SessionType
     */
    public function setAssessment($assessment)
    {
        $this->assessment = $assessment;

        return $this;
    }

    /**
     * Get assessment
     *
     * @return boolean 
     */
    public function getAssessment()
    {
        return $this->assessment;
    }

    /**
     * Set assessmentOption
     *
     * @param \Ilios\CoreBundle\Model\AssessmentOption $assessmentOption
     * @return SessionType
     */
    public function setAssessmentOption(\Ilios\CoreBundle\Model\AssessmentOption $assessmentOption = null)
    {
        $this->assessmentOption = $assessmentOption;

        return $this;
    }

    /**
     * Get assessmentOption
     *
     * @return \Ilios\CoreBundle\Model\AssessmentOption 
     */
    public function getAssessmentOption()
    {
        return $this->assessmentOption;
    }

    /**
     * Set owningSchool
     *
     * @param \Ilios\CoreBundle\Model\School $owningSchool
     * @return SessionType
     */
    public function setOwningSchool(\Ilios\CoreBundle\Model\School $owningSchool = null)
    {
        $this->owningSchool = $owningSchool;

        return $this;
    }

    /**
     * Get owningSchool
     *
     * @return \Ilios\CoreBundle\Model\School 
     */
    public function getOwningSchool()
    {
        return $this->owningSchool;
    }

    /**
     * Add aamcMethods
     *
     * @param \Ilios\CoreBundle\Model\AamcMethod $aamcMethods
     * @return SessionType
     */
    public function addAamcMethod(\Ilios\CoreBundle\Model\AamcMethod $aamcMethods)
    {
        $this->aamcMethods[] = $aamcMethods;

        return $this;
    }

    /**
     * Remove aamcMethods
     *
     * @param \Ilios\CoreBundle\Model\AamcMethod $aamcMethods
     */
    public function removeAamcMethod(\Ilios\CoreBundle\Model\AamcMethod $aamcMethods)
    {
        $this->aamcMethods->removeElement($aamcMethods);
    }

    /**
     * Get aamcMethods
     *
     * @return \Ilios\CoreBundle\Model\AamcMethod[]
     */
    public function getAamcMethods()
    {
        return $this->aamcMethods->toArray();
    }
}
