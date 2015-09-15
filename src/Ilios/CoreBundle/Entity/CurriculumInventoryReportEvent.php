<?php

namespace Ilios\CoreBundle\Entity;

/**
 * Class CurriculumInventoryReportEvent
 * @package Ilios\CoreBundle\Entity
 */
class CurriculumInventoryReportEvent implements CurriculumInventoryReportEventInterface
{
    /**
     * @var int
     */
    protected $id;

    /**
     * @var string
     */
    protected $title;

    /**
     * @var string
     */
    protected $description;

    /**
     * @var int
     */
    protected $methodId;

    /**
     * @var boolean
     */
    protected $assessmentMethod;

    /**
     * @var string
     */
    protected $assessmentOption;

    /**
     * @var int
     */
    protected $duration;

    /**
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @param int $id
     */
    public function setId($id)
    {
        $this->id = $id;
    }

    /**
     * @return string
     */
    public function getTitle()
    {
        return $this->title;
    }

    /**
     * @param string $title
     */
    public function setTitle($title)
    {
        $this->title = $title;
    }

    /**
     * @return string
     */
    public function getDescription()
    {
        return $this->description;
    }

    /**
     * @param string $description
     */
    public function setDescription($description)
    {
        $this->description = $description;
    }

    /**
     * @return int
     */
    public function getMethodId()
    {
        return $this->methodId;
    }

    /**
     * @param int $methodId
     */
    public function setMethodId($methodId)
    {
        $this->methodId = $methodId;
    }

    /**
     * @return boolean
     */
    public function isAssessmentMethod()
    {
        return $this->assessmentMethod;
    }

    /**
     * @param int|boolean $assessmentMethod
     */
    public function setAssessmentMethod($assessmentMethod)
    {
        $this->assessmentMethod = (boolean) $assessmentMethod;
    }

    /**
     * @return string
     */
    public function getAssessmentOption()
    {
        return $this->assessmentOption;
    }

    /**
     * @param string $assessmentOption
     */
    public function setAssessmentOption($assessmentOption)
    {
        $this->assessmentOption = $assessmentOption;
    }

    /**
     * @return int
     */
    public function getDuration()
    {
        return $this->duration;
    }

    /**
     * @param int $duration
     */
    public function setDuration($duration)
    {
        $this->duration = $duration;
    }
}
