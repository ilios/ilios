<?php

namespace Ilios\CoreBundle\Model;

use Doctrine\ORM\Mapping as ORM;

use Ilios\CoreBundle\Traits\DescribableEntity;
use Ilios\CoreBundle\Traits\IdentifiableEntity;
use Ilios\CoreBundle\Traits\NameableEntity;

use Ilios\CoreBundle\Model\CurriculumInventoryExportInterface;
use Ilios\CoreBundle\Model\CurriculumInventorySequenceInterface;
use Ilios\CoreBundle\Model\ProgramInterface;

/**
 * Class CurriculumInventoryReport
 * @package Ilios\CoreBundle\Model
 *
 * @ORM\Entity
 * @ORM\Table(name="curriculum_inventory_report")
 */
class CurriculumInventoryReport implements CurriculumInventoryReportInterface
{
//    use IdentifiableEntity;
    use NameableEntity;
    use DescribableEntity;

    /**
     * @deprecated To be removed in 3.1, replaced by ID by enabling trait.
     * @var int
     *
     * @ORM\Id
     * @ORM\Column(type="integer", length=10, name="report_id")
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $reportId;

    /**
     * @var int
     */
    protected $id;

    /**
     * @var int
     *
     * @ORM\Column(type="smallint", length=4)
     */
    protected $year;

    /**
     * @var \DateTime
     *
     * @ORM\Column(type="date", name="start_date")
     */
    protected $startDate;

    /**
     * @var \DateTime
     *
     * @ORM\Column(type="date", name="end_date")
     */
    protected $endDate;

    /**
     * @var CurriculumInventoryExportInterface
     *
     * @ORM\OneToOne(targetEntity="CurriculumInventoryExport", inversedBy="report")
     * @ORM\JoinColumn(name="report_id", referencedColumnName="report_id", nullable=true)
     */
    protected $export;

    /**
     * @var CurriculumInventorySequenceInterface
     *
     * @ORM\OneToOne(targetEntity="CurriculumInventorySequence", inversedBy="report")
     * @ORM\JoinColumn(name="report_id", referencedColumnName="report_id", nullable=true)
     */
    protected $sequence;

    /**
     * @var ProgramInterface
     *
     * @ORM\OneToMany(targetEntity="Program", mappedBy="curriculumInventoryReports")
     */
    protected $program;

    /**
     * @param int $id
     */
    public function setId($id)
    {
        $this->reportId = $id;
        $this->id = $id;
    }

    /**
     * @return int
     */
    public function getId()
    {
        return ($this->id === null) ? $this->reportId : $this->id;
    }

    /**
     * @param int $year
     */
    public function setYear($year)
    {
        $this->year = $year;
    }

    /**
     * @return int
     */
    public function getYear()
    {
        return $this->year;
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
