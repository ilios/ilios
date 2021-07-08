<?php

declare(strict_types=1);

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use App\Traits\DirectorsEntity;
use App\Traits\PublishableEntity;
use Symfony\Component\Validator\Constraints as Assert;
use App\Annotation as IS;
use App\Traits\IdentifiableEntity;
use App\Traits\TitledEntity;
use App\Traits\StringableIdEntity;
use App\Traits\ProgramYearsEntity;
use App\Traits\SchoolEntity;
use App\Repository\ProgramRepository;

/**
 * Class Program
 * @IS\Entity
 */
#[ORM\Table(name: 'program')]
#[ORM\Entity(repositoryClass: ProgramRepository::class)]
class Program implements ProgramInterface
{
    use TitledEntity;
    use IdentifiableEntity;
    use StringableIdEntity;
    use ProgramYearsEntity;
    use SchoolEntity;
    use DirectorsEntity;
    /**
     * @var int
     * @Assert\Type(type="integer")
     * @IS\Expose
     * @IS\Type("integer")
     * @IS\ReadOnly
     */
    #[ORM\Column(name: 'program_id', type: 'integer')]
    #[ORM\Id]
    #[ORM\GeneratedValue(strategy: 'IDENTITY')]
    protected $id;
    /**
     * @var string
     * @Assert\NotBlank()
     * @Assert\Type(type="string")
     * @Assert\Length(
     *      min = 1,
     *      max = 200
     * )
     * @IS\Expose
     * @IS\Type("string")
     */
    #[ORM\Column(type: 'string', length: 200, nullable: false)]
    protected $title;
    /**
     * @var string
     * @Assert\Type(type="string")
     * @Assert\AtLeastOneOf({
     *     @Assert\Blank,
     *     @Assert\Length(min=1,max=10)
     * })
     * @IS\Expose
     * @IS\Type("string")
     */
    #[ORM\Column(name: 'short_title', type: 'string', length: 10, nullable: true)]
    protected $shortTitle;
    /**
     * @var int
     * @Assert\NotBlank()
     * @Assert\Type(type="integer")
     * @IS\Expose
     * @IS\Type("integer")
     */
    #[ORM\Column(name: 'duration', type: 'smallint')]
    protected $duration;
    /**
     * @var SchoolInterface
     * @Assert\NotNull()
     * })
     * @IS\Expose
     * @IS\Type("entity")
     */
    #[ORM\ManyToOne(targetEntity: 'School', inversedBy: 'programs')]
    #[ORM\JoinColumn(name: 'school_id', referencedColumnName: 'school_id', nullable: false)]
    protected $school;
    /**
     * @var ArrayCollection|ProgramYearInterface[]
     * @IS\Expose
     * @IS\Type("entityCollection")
     */
    #[ORM\OneToMany(targetEntity: 'ProgramYear', mappedBy: 'program')]
    #[ORM\OrderBy(['id' => 'ASC'])]
    protected $programYears;
    /**
     * @var ArrayCollection|CurriculumInventoryReportInterface[]
     * @IS\Expose
     * @IS\Type("entityCollection")
     */
    #[ORM\OneToMany(targetEntity: 'CurriculumInventoryReport', mappedBy: 'program')]
    #[ORM\OrderBy(['id' => 'ASC'])]
    protected $curriculumInventoryReports;
    /**
     * @var ArrayCollection|UserInterface[]
     * @IS\Expose
     * @IS\Type("entityCollection")
     */
    #[ORM\ManyToMany(targetEntity: 'User', inversedBy: 'directedPrograms')]
    #[ORM\JoinTable(name: 'program_director')]
    #[ORM\JoinColumn(name: 'program_id', referencedColumnName: 'program_id', onDelete: 'CASCADE')]
    #[ORM\InverseJoinColumn(name: 'user_id', referencedColumnName: 'user_id', onDelete: 'CASCADE')]
    #[ORM\OrderBy(['id' => 'ASC'])]
    protected $directors;
    /**
     * Constructor
     */
    public function __construct()
    {
        $this->programYears = new ArrayCollection();
        $this->curriculumInventoryReports = new ArrayCollection();
        $this->directors = new ArrayCollection();
    }
    /**
     * @param string $shortTitle
     */
    public function setShortTitle($shortTitle)
    {
        $this->shortTitle = $shortTitle;
    }
    /**
     * @return string
     */
    public function getShortTitle()
    {
        return $this->shortTitle;
    }
    /**
     * @param int $duration
     */
    public function setDuration($duration)
    {
        $this->duration = $duration;
    }
    /**
     * @return int
     */
    public function getDuration()
    {
        return $this->duration;
    }
    /**
     * {@inheritdoc}
     */
    public function setCurriculumInventoryReports(Collection $reports)
    {
        $this->curriculumInventoryReports = new ArrayCollection();

        foreach ($reports as $report) {
            $this->addCurriculumInventoryReport($report);
        }
    }
    /**
     * {@inheritdoc}
     */
    public function addCurriculumInventoryReport(CurriculumInventoryReportInterface $report)
    {
        if (!$this->curriculumInventoryReports->contains($report)) {
            $this->curriculumInventoryReports->add($report);
        }
    }
    /**
     * {@inheritdoc}
     */
    public function removeCurriculumInventoryReport(CurriculumInventoryReportInterface $report)
    {
        if ($this->curriculumInventoryReports->contains($report)) {
            $this->curriculumInventoryReports->removeElement($report);
        }
    }
    /**
     * {@inheritdoc}
     */
    public function getCurriculumInventoryReports()
    {
        return $this->curriculumInventoryReports;
    }
    /**
     * @inheritdoc
     */
    public function addDirector(UserInterface $director)
    {
        if (!$this->directors->contains($director)) {
            $this->directors->add($director);
            $director->addDirectedProgram($this);
        }
    }
    /**
     * @inheritdoc
     */
    public function removeDirector(UserInterface $director)
    {
        if ($this->directors->contains($director)) {
            $this->directors->removeElement($director);
            $director->removeDirectedProgram($this);
        }
    }
}
