<?php

namespace Ilios\CoreBundle\Model;



/**
 * CurriculumInventoryAcademicLevel
 */
class CurriculumInventoryAcademicLevel
{
    /**
     * @var integer
     */
    private $academicLevelId;

    /**
     * @var integer
     */
    private $level;

    /**
     * @var string
     */
    private $name;

    /**
     * @var string
     */
    private $description;

    /**
     * @var \Ilios\CoreBundle\Model\CurriculumInventoryReport
     */
    private $report;


    /**
     * Get academicLevelId
     *
     * @return integer 
     */
    public function getAcademicLevelId()
    {
        return $this->academicLevelId;
    }

    /**
     * Set level
     *
     * @param integer $level
     * @return CurriculumInventoryAcademicLevel
     */
    public function setLevel($level)
    {
        $this->level = $level;

        return $this;
    }

    /**
     * Get level
     *
     * @return integer 
     */
    public function getLevel()
    {
        return $this->level;
    }

    /**
     * Set name
     *
     * @param string $name
     * @return CurriculumInventoryAcademicLevel
     */
    public function setName($name)
    {
        $this->name = $name;

        return $this;
    }

    /**
     * Get name
     *
     * @return string 
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * Set description
     *
     * @param string $description
     * @return CurriculumInventoryAcademicLevel
     */
    public function setDescription($description)
    {
        $this->description = $description;

        return $this;
    }

    /**
     * Get description
     *
     * @return string 
     */
    public function getDescription()
    {
        return $this->description;
    }

    /**
     * Set report
     *
     * @param \Ilios\CoreBundle\Model\CurriculumInventoryReport $report
     * @return CurriculumInventoryAcademicLevel
     */
    public function setReport(\Ilios\CoreBundle\Model\CurriculumInventoryReport $report = null)
    {
        $this->report = $report;

        return $this;
    }

    /**
     * Get report
     *
     * @return \Ilios\CoreBundle\Model\CurriculumInventoryReport 
     */
    public function getReport()
    {
        return $this->report;
    }
}
