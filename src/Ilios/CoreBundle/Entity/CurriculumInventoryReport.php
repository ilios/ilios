<?php

namespace Ilios\CoreBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Ilios\CoreBundle\Traits\AdministratorsEntity;
use Ilios\CoreBundle\Traits\SequenceBlocksEntity;
use Ilios\ApiBundle\Annotation as IS;
use Symfony\Component\Validator\Constraints as Assert;

use Ilios\CoreBundle\Traits\DescribableEntity;
use Ilios\CoreBundle\Traits\IdentifiableEntity;
use Ilios\CoreBundle\Traits\NameableEntity;
use Ilios\CoreBundle\Traits\StringableIdEntity;

/**
 * Class CurriculumInventoryReport
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
 * @IS\Entity
 */
class CurriculumInventoryReport implements CurriculumInventoryReportInterface
{
    use IdentifiableEntity;
    use NameableEntity;
    use DescribableEntity;
    use StringableIdEntity;
    use SequenceBlocksEntity;
    use AdministratorsEntity;

    /**
     * @var int
     *
     * @ORM\Column(name="report_id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     *
     * @Assert\Type(type="integer")
     *
     * @IS\Expose
     * @IS\Type("integer")
     * @IS\ReadOnly
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
     * @IS\Expose
     * @IS\Type("string")
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
     * @IS\Expose
     * @IS\Type("string")
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
     * @IS\Expose
     * @IS\Type("integer")
     */
    protected $year;

    /**
     * @var \DateTime
     *
     * @ORM\Column(type="date", name="start_date")
     *
     * @Assert\NotBlank()
     *
     * @IS\Expose
     * @IS\Type("dateTime")
     */
    protected $startDate;

    /**
     * @var \DateTime
     *
     * @ORM\Column(type="date", name="end_date")
     *
     * @Assert\NotBlank()
     *
     * @IS\Expose
     * @IS\Type("dateTime")
     */
    protected $endDate;

    /**
     * @var CurriculumInventoryExportInterface
     *
     * @ORM\OneToOne(targetEntity="CurriculumInventoryExport", mappedBy="report")
     *
     * @IS\Expose
     * @IS\Type("entity")
     */
    protected $export;

    /**
    * @var CurriculumInventorySequenceInterface
    *
    * @ORM\OneToOne(targetEntity="CurriculumInventorySequence", mappedBy="report")
    *
    * @IS\Expose
    * @IS\Type("entity")
    */
    protected $sequence;

    /**
    * @var ArrayCollection|CurriculumInventorySequenceBlockInterface[]
    *
    * @ORM\OneToMany(targetEntity="CurriculumInventorySequenceBlock",mappedBy="report")
    *
    * @IS\Expose
    * @IS\Type("entityCollection")
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
    * @IS\Expose
    * @IS\Type("entity")
    */
    protected $program;

    /**
    * @var CurriculumInventoryAcademicLevelInterface
    *
    * @ORM\OneToMany(targetEntity="CurriculumInventoryAcademicLevel", mappedBy="report")
    *
    * @IS\Expose
    * @IS\Type("entityCollection")
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
     * @var ArrayCollection|UserInterface[]
     *
     * @ORM\ManyToMany(targetEntity="User", inversedBy="administeredCurriculumInventoryReports"))
     * @ORM\JoinTable(name="curriculum_inventory_report_administrator",
     *   joinColumns={
     *     @ORM\JoinColumn(name="report_id", referencedColumnName="report_id", onDelete="CASCADE")
     *   },
     *   inverseJoinColumns={
     *     @ORM\JoinColumn(name="user_id", referencedColumnName="user_id", onDelete="CASCADE")
     *   }
     * )
     *
     * @IS\Expose
     * @IS\Type("entityCollection")
     */
    protected $administrators;

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->academicLevels = new ArrayCollection();
        $this->sequenceBlocks = new ArrayCollection();
        $this->administrators = new ArrayCollection();
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
     * @param CurriculumInventoryExportInterface|null $export
     */
    public function setExport(CurriculumInventoryExportInterface $export = null)
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
     * @param CurriculumInventorySequenceInterface|null $sequence
     */
    public function setSequence(CurriculumInventorySequenceInterface $sequence = null)
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
    public function setProgram(ProgramInterface $program = null)
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
        if (!$this->academicLevels->contains($academicLevel)) {
            $this->academicLevels->add($academicLevel);
        }
    }

    /**
     * @param CurriculumInventoryAcademicLevelInterface $academicLevel
     */
    public function removeAcademicLevel(CurriculumInventoryAcademicLevelInterface $academicLevel)
    {
        $this->academicLevels->removeElement($academicLevel);
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

    /**
     * @inheritdoc
     */
    public function addAdministrator(UserInterface $administrator)
    {
        if (!$this->administrators->contains($administrator)) {
            $this->administrators->add($administrator);
            $administrator->addAdministeredCurriculumInventoryReport($this);
        }
    }

    /**
     * @inheritdoc
     */
    public function removeAdministrator(UserInterface $administrator)
    {
        if ($this->administrators->contains($administrator)) {
            $this->administrators->removeElement($administrator);
            $administrator->removeAdministeredCurriculumInventoryReport($this);
        }
    }
}
