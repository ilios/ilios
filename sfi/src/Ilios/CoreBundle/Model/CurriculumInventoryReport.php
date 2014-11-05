<?php

namespace Ilios\CoreBundle\Model;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
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
     * @var ArrayCollection|CurriculumInventoryExportInterface[]
     */
    protected $exports;

    /**
     * @var ArrayCollection|CurriculumInventorySequenceInterface[]
     */
    protected $sequences;

    /**
     * @var ProgramInterface
     */
    protected $program;

    public function __construct()
    {
        $this->exports = new ArrayCollection();
        $this->sequences = new ArrayCollection();
    }

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
     * @param Collection $exports
     */
    public function setExports(Collection $exports)
    {
        $this->exports = new ArrayCollection();

        foreach ($exports as $export) {
            $this->addExport($export);
        }
    }

    /**
     * @param CurriculumInventoryExportInterface $export
     */
    public function addExport(CurriculumInventoryExportInterface $export)
    {
        $this->exports->add($export);
    }

    /**
     * @return ArrayCollection|CurriculumInventoryExportInterface[]
     */
    public function getExports()
    {
        return $this->exports;
    }

    /**
     * @param Collection $sequences
     */
    public function setSequences(Collection $sequences)
    {
        $this->sequences = new ArrayCollection();

        foreach ($sequences as $sequence) {
            $this->addSequence($sequence);
        }
    }

    /**
     * @param CurriculumInventorySequenceInterface $sequence
     */
    public function addSequence(CurriculumInventorySequenceInterface $sequence)
    {
        $this->sequences->add($sequence);
    }

    /**
     * @return ArrayCollection|CurriculumInventorySequenceInterface[]
     */
    public function getSequence()
    {
        return $this->sequences;
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
