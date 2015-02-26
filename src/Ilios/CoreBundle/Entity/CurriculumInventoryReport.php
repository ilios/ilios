<?php

namespace Ilios\CoreBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use JMS\Serializer\Annotation as JMS;
use Symfony\Component\Validator\Constraints as Assert;

use Ilios\CoreBundle\Traits\DescribableEntity;
use Ilios\CoreBundle\Traits\IdentifiableEntity;
use Ilios\CoreBundle\Traits\NameableEntity;
use Ilios\CoreBundle\Traits\StringableIdEntity;
use Ilios\CoreBundle\Entity\CurriculumInventoryExportInterface;
use Ilios\CoreBundle\Entity\CurriculumInventorySequenceInterface;
use Ilios\CoreBundle\Entity\ProgramInterface;

/**
 * Class CurriculumInventoryReport
 * @package Ilios\CoreBundle\Entity
 *
 * @ORM\Table(name="curriculum_inventory_report",
 *   uniqueConstraints={
 *     @ORM\UniqueConstraint(name="program_id_year", columns={"program_id", "year"})
 *   },
 *   indexes={
 *     @ORM\Index(name="IDX_6E31899E3EB8070A", columns={"program_id"})
 *   }
 * )
 * @ORM\Entity
 *
 * @JMS\ExclusionPolicy("all")
 */
class CurriculumInventoryReport implements CurriculumInventoryReportInterface
{
    use IdentifiableEntity;
    use NameableEntity;
    use DescribableEntity;
    use StringableIdEntity;

    /**
     * @var int
     *
     * @ORM\Column(name="report_id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     *
     * @Assert\Type(type="integer")
     *
     * @JMS\Expose
     * @JMS\Type("integer")
     */
    protected $id;

    /**
    * @var string
    *
    * @ORM\Column(type="string", length=200, nullable=true)
    *
    * @Assert\Type(type="string")
     * @Assert\Length(
     *      min = 1,
     *      max = 200
     * )    
    */
    protected $name;

    /**
    * @var string
    *
    * @ORM\Column(name="description", type="text", nullable=true)
    *
    * @Assert\Type(type="string")
     * @Assert\Length(
     *      min = 1,
     *      max = 65000
     * )        
    */
    protected $description;

    /**
     * @var int
     *
     * @ORM\Column(name="year", type="smallint")
     *
     * @Assert\NotBlank()
     * @Assert\Type(type="integer")
     */
    protected $year;

    /**
     * @var \DateTime
     *
     * @ORM\Column(type="date", name="start_date")
     *
     * @Assert\NotBlank()
     */
    protected $startDate;

    /**
     * @var \DateTime
     *
     * @ORM\Column(type="date", name="end_date")
     *
     * @Assert\NotBlank()     
     */
    protected $endDate;

    /**
     * @var CurriculumInventoryExportInterface
     *
     * @ORM\OneToOne(targetEntity="CurriculumInventoryExport", mappedBy="report")
     *
     * @JMS\Expose
     * @JMS\Type("string")
     */
    protected $export;

    /**
    * @var CurriculumInventorySequenceInterface
    *
    * @ORM\OneToOne(targetEntity="CurriculumInventorySequence", mappedBy="report")
    *
    * @JMS\Expose
    * @JMS\Type("string")
    */
    protected $sequence;

    /**
    * @var ArrayCollection|CurriculumInventorySequenceBlockInterface[]
    *
    * @ORM\OneToMany(targetEntity="CurriculumInventorySequenceBlock",mappedBy="report")
    *
    * @JMS\Expose
    * @JMS\Type("array<string>")
    * @JMS\SerializedName("sequenceBlocks")
    */
    protected $sequenceBlocks;

    /**
    * @var ProgramInterface
    *
    * @ORM\ManyToOne(targetEntity="Program", inversedBy="curriculumInventoryReports")
    * @ORM\JoinColumns({
    *   @ORM\JoinColumn(name="program_id", referencedColumnName="program_id")
    * })
    *
    * @JMS\Expose
    * @JMS\Type("string")
    */
    protected $program;

    /**
    * @var CurriculumInventoryAcademicLevelInterface
    *
    * @ORM\OneToMany(targetEntity="CurriculumInventoryAcademicLevel", mappedBy="report")
    *
    * @JMS\Expose
    * @JMS\Type("array<string>")
    * @JMS\SerializedName("academicLevels")
    */
    protected $curriculumInventoryAcademicLevels;

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
