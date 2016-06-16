<?php

namespace Ilios\CoreBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use JMS\Serializer\Annotation as JMS;
use Symfony\Component\Validator\Constraints as Assert;

use Ilios\CoreBundle\Traits\DescribableEntity;
use Ilios\CoreBundle\Traits\IdentifiableEntity;
use Ilios\CoreBundle\Traits\NameableEntity;
use Ilios\CoreBundle\Traits\StringableIdEntity;

/**
 * Class CurriculumInventoryReport
 * @package Ilios\CoreBundle\Entity
 *
 * @ORM\Table(
 *   name="curriculum_inventory_report",
 *   indexes={
 *     @ORM\Index(name="IDX_6E31899E3EB8070A", columns={"program_id"})
 *   },
 *   uniqueConstraints={
 *     @ORM\UniqueConstraint(name="idx_ci_report_token_unique", columns={"token"})
 *   }
 * )
 * @ORM\Entity(repositoryClass="Ilios\CoreBundle\Entity\Repository\CurriculumInventoryReportRepository")
 *
 * @JMS\ExclusionPolicy("all")
 * @JMS\AccessType("public_method")
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
     *
     * @JMS\Expose
     * @JMS\Type("string")
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
     *
     * @JMS\Expose
     * @JMS\Type("string")
    */
    protected $description;

    /**
     * @var int
     *
     * @ORM\Column(name="year", type="smallint")
     *
     * @Assert\NotBlank()
     * @Assert\Type(type="integer")
     *
     * @JMS\Expose
     * @JMS\Type("integer")
     */
    protected $year;

    /**
     * @var \DateTime
     *
     * @ORM\Column(type="date", name="start_date")
     *
     * @Assert\NotBlank()
     *
     * @JMS\Expose
     * @JMS\Type("DateTime<'c'>")
     * @JMS\SerializedName("startDate")
     */
    protected $startDate;

    /**
     * @var \DateTime
     *
     * @ORM\Column(type="date", name="end_date")
     *
     * @Assert\NotBlank()
     *
     * @JMS\Expose
     * @JMS\Type("DateTime<'c'>")
     * @JMS\SerializedName("endDate")
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
    protected $academicLevels;

    /**
     * @var string
     *
     * @ORM\Column(name="token", type="string", length=64, nullable=true)
     *
     * @Assert\Type(type="string")
     * @Assert\Length(
     *      min = 1,
     *      max = 64
     * )
     */
    protected $token;

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->academicLevels = new ArrayCollection();
        $this->sequenceBlocks = new ArrayCollection();

        $this->generateToken();
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
    public function setStartDate($startDate = null)
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
    public function setEndDate($endDate = null)
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

    /**
     * @param Collection $sequenceBlocks
     */
    public function setSequenceBlocks(Collection $sequenceBlocks = null)
    {
        $this->sequenceBlocks = new ArrayCollection();
        if (is_null($sequenceBlocks)) {
            return;
        }
        foreach ($sequenceBlocks as $sequenceBlock) {
            $this->addSequenceBlock($sequenceBlock);
        }
    }

    /**
     * @param CurriculumInventorySequenceBlockInterface $sequenceBlock
     */
    public function addSequenceBlock(CurriculumInventorySequenceBlockInterface $sequenceBlock)
    {
        $this->sequenceBlocks->add($sequenceBlock);
    }

    /**
     * @return ArrayCollection|CurriculumInventorySequenceBlockInterface[]
     */
    public function getSequenceBlocks()
    {
        return $this->sequenceBlocks;
    }

    /**
     * @param Collection $academicLevels
     */
    public function setAcademicLevels(Collection $academicLevels = null)
    {
        $this->academicLevels = new ArrayCollection();
        if (is_null($academicLevels)) {
            return;
        }
        foreach ($academicLevels as $academicLevel) {
            $this->addAcademicLevel($academicLevel);
        }
    }

    /**
     * @param CurriculumInventoryAcademicLevelInterface $academicLevel
     */
    public function addAcademicLevel(CurriculumInventoryAcademicLevelInterface $academicLevel)
    {
        $this->academicLevels->add($academicLevel);
    }

    /**
     * @return ArrayCollection|CurriculumInventoryAcademicLevelInterface[]
     */
    public function getAcademicLevels()
    {
        return $this->academicLevels;
    }

    /**
     * @inheritdoc
     */
    public function getSchool()
    {
        if ($program = $this->getProgram()) {
            return $program->getSchool();
        }
        return null;
    }

    /**
     * @return string
     */
    public function getToken()
    {
        return $this->token;
    }

    /**
     * @inheritdoc
     */
    public function generateToken()
    {
        $random = random_bytes(128);

        // prepend id to avoid a conflict
        // and current time to prevent a conflict with regeneration
        $key = $this->getId() . microtime() . $random;

        // hash the string to give consistent length and URL safe characters
        $this->token = hash('sha256', $key);
    }
}
