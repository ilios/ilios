<?php

declare(strict_types=1);

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use App\Traits\AdministratorsEntity;
use App\Traits\SequenceBlocksEntity;
use App\Annotation as IS;
use Symfony\Component\Validator\Constraints as Assert;
use App\Traits\DescribableEntity;
use App\Traits\IdentifiableEntity;
use App\Traits\NameableEntity;
use App\Traits\StringableIdEntity;
use App\Repository\CurriculumInventoryReportRepository;

/**
 * Class CurriculumInventoryReport
 * @IS\Entity
 */
#[ORM\Table(name: 'curriculum_inventory_report')]
#[ORM\Index(columns: ['program_id'], name: 'IDX_6E31899E3EB8070A')]
#[ORM\UniqueConstraint(name: 'idx_ci_report_token_unique', columns: ['token'])]
#[ORM\Entity(repositoryClass: CurriculumInventoryReportRepository::class)]
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
     * @Assert\Type(type="integer")
     * @IS\Expose
     * @IS\Type("integer")
     * @IS\ReadOnly
     */
    #[ORM\Column(name: 'report_id', type: 'integer')]
    #[ORM\Id]
    #[ORM\GeneratedValue(strategy: 'IDENTITY')]
    protected $id;

    /**
     * @var string
     * @Assert\Type(type="string")
     * @Assert\AtLeastOneOf({
     *     @Assert\Blank,
     *     @Assert\Length(min=1,max=200)
     * })
     * @IS\Expose
     * @IS\Type("string")
     */
    #[ORM\Column(type: 'string', length: 200, nullable: true)]
    protected $name;

    /**
     * @var string
     * @Assert\Type(type="string")
     * @Assert\AtLeastOneOf({
     *     @Assert\Blank,
     *     @Assert\Length(min=1,max=65000)
     * })
     * @IS\Expose
     * @IS\Type("string")
     */
    #[ORM\Column(name: 'description', type: 'text', nullable: true)]
    protected $description;

    /**
     * @var int
     * @Assert\NotBlank()
     * @Assert\Type(type="integer")
     * @IS\Expose
     * @IS\Type("integer")
     */
    #[ORM\Column(name: 'year', type: 'smallint')]
    protected $year;

    /**
     * @var \DateTime
     * @Assert\NotBlank()
     * @IS\Expose
     * @IS\Type("dateTime")
     */
    #[ORM\Column(name: 'start_date', type: 'date')]
    protected $startDate;

    /**
     * @var \DateTime
     * @Assert\NotBlank()
     * @IS\Expose
     * @IS\Type("dateTime")
     */
    #[ORM\Column(name: 'end_date', type: 'date')]
    protected $endDate;

    /**
     * @var CurriculumInventoryExportInterface
     * @IS\Expose
     * @IS\Type("entity")
     */
    #[ORM\OneToOne(mappedBy: 'report', targetEntity: 'CurriculumInventoryExport')]
    protected $export;

    /**
     * @var CurriculumInventorySequenceInterface
     * @IS\Expose
     * @IS\Type("entity")
     */
    #[ORM\OneToOne(mappedBy: 'report', targetEntity: 'CurriculumInventorySequence')]
    protected $sequence;

    /**
     * @var ArrayCollection|CurriculumInventorySequenceBlockInterface[]
     * @IS\Expose
     * @IS\Type("entityCollection")
     */
    #[ORM\OneToMany(mappedBy: 'report', targetEntity: 'CurriculumInventorySequenceBlock')]
    #[ORM\OrderBy(['id' => 'ASC'])]
    protected $sequenceBlocks;

    /**
     * @var ProgramInterface
     * @IS\Expose
     * @IS\Type("entity")
     */
    #[ORM\ManyToOne(targetEntity: 'Program', inversedBy: 'curriculumInventoryReports')]
    #[ORM\JoinColumn(name: 'program_id', referencedColumnName: 'program_id')]
    protected $program;

    /**
     * @var CurriculumInventoryAcademicLevelInterface
     * @IS\Expose
     * @IS\Type("entityCollection")
     */
    #[ORM\OneToMany(mappedBy: 'report', targetEntity: 'CurriculumInventoryAcademicLevel')]
    #[ORM\OrderBy(['id' => 'ASC'])]
    protected $academicLevels;

    /**
     * @var string
     * @Assert\Type(type="string")
     * @Assert\AtLeastOneOf({
     *     @Assert\Blank,
     *     @Assert\Length(min=1,max=64)
     * })
     */
    #[ORM\Column(name: 'token', type: 'string', length: 64, nullable: true)]
    protected $token;

    /**
     * @var ArrayCollection|UserInterface[]
     * @IS\Expose
     * @IS\Type("entityCollection")
     */
    #[ORM\ManyToMany(targetEntity: 'User', inversedBy: 'administeredCurriculumInventoryReports')]
    #[ORM\JoinTable(name: 'curriculum_inventory_report_administrator')]
    #[ORM\JoinColumn(name: 'report_id', referencedColumnName: 'report_id', onDelete: 'CASCADE')]
    #[ORM\InverseJoinColumn(name: 'user_id', referencedColumnName: 'user_id', onDelete: 'CASCADE')]
    #[ORM\OrderBy(['id' => 'ASC'])]
    protected $administrators;

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

    public function addAcademicLevel(CurriculumInventoryAcademicLevelInterface $academicLevel)
    {
        if (!$this->academicLevels->contains($academicLevel)) {
            $this->academicLevels->add($academicLevel);
        }
    }

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
