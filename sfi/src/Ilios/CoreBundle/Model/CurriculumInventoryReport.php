<?php

namespace Ilios\CoreBundle\Model;

use Ilios\CoreBundle\Traits\DescribableTrait;
use Ilios\CoreBundle\Traits\IdentifiableTrait;
use Ilios\CoreBundle\Traits\NameableTrait;

use Ilios\CoreBundle\Model\CurriculumInventoryExportInterface;
use Ilios\CoreBundle\Model\CurriculumInventorySequenceInterface;
use Ilios\CoreBundle\Model\ProgramInterface;

/**
 * Class CurriculumInventoryReport
 * @package Ilios\CoreBundle\Model
 */
class CurriculumInventoryReport implements CurriculumInventoryReportInterface
{
    use IdentifiableTrait;
    use NameableTrait;
    use DescribableTrait;

    /**
     * @var integer
     */
    protected $year;

    /**
     * @var \DateTime
     */
    protected $startDate;

    /**
     * @var \DateTime
     */
    protected $endDate;

    /**
     * @var CurriculumInventoryExportInterface
     */
    protected $export;

    /**
     * @var CurriculumInventorySequenceInterface
     */
    protected $sequence;

    /**
     * @var ProgramInterface
     */
    protected $program;

    /**
     * @param integer $year
     */
    public function setYear($year)
    {
        $this->year = $year;
    }

    /**
     * @return integer
     */
    public function getYear()
    {
        return $this->year;
    }

    /**
     * @param string $name
     */
    public function setName($name)
    {
        $this->name = $name;
    }

    /**
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @param string $description
     */
    public function setDescription($description)
    {
        $this->description = $description;
    }

    /**
     * @return string
     */
    public function getDescription()
    {
        return $this->description;
    }

    /**
     * @param \DateTime $startDate
     */
    public function setStartDate($startDate)
    {
        $this->startDate = $startDate;
    }

    /**
     * @return \DateTime
     */
    public function getStartDate()
    {
        return $this->startDate;
    }

    /**
     * @param \DateTime $endDate
     */
    public function setEndDate($endDate)
    {
        $this->endDate = $endDate;
    }

    /**
     * @return \DateTime
     */
    public function getEndDate()
    {
        return $this->endDate;
    }

    /**
     * @param CurriculumInventoryExportInterface $export
     */
    public function setExport(CurriculumInventoryExportInterface $export)
    {
        $this->export = $export;
    }

    /**
     * @return CurriculumInventoryExportInterface
     */
    public function getExport()
    {
        return $this->export;
    }

    /**
     * @param CurriculumInventorySequenceInterface $sequence
     */
    public function setSequence(CurriculumInventorySequenceInterface $sequence)
    {
        $this->sequence = $sequence;
    }

    /**
     * @return CurriculumInventorySequenceInterface
     */
    public function getSequence()
    {
        return $this->sequence;
    }

    /**
     * @param ProgramInterface $program
     */
    public function setProgram(ProgramInterface $program)
    {
        $this->program = $program;
    }

    /**
     * @return ProgramInterface
     */
    public function getProgram()
    {
        return $this->program;
    }
}
