<?php

declare(strict_types=1);

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use App\Traits\DirectorsEntity;
use App\Traits\PublishableEntity;
use Symfony\Component\Validator\Constraints as Assert;
use App\Attribute as IA;
use App\Traits\IdentifiableEntity;
use App\Traits\TitledEntity;
use App\Traits\StringableIdEntity;
use App\Traits\ProgramYearsEntity;
use App\Traits\SchoolEntity;
use App\Repository\ProgramRepository;

/**
 * Class Program
 */
#[ORM\Table(name: 'program')]
#[ORM\Entity(repositoryClass: ProgramRepository::class)]
#[IA\Entity]
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
     */
    #[ORM\Column(name: 'program_id', type: 'integer')]
    #[ORM\Id]
    #[ORM\GeneratedValue(strategy: 'IDENTITY')]
    #[IA\Expose]
    #[IA\Type('integer')]
    #[IA\OnlyReadable]
    #[Assert\Type(type: 'integer')]
    protected $id;

    /**
     * @var string
     */
    #[ORM\Column(type: 'string', length: 200, nullable: false)]
    #[IA\Expose]
    #[IA\Type('string')]
    #[Assert\NotBlank]
    #[Assert\Type(type: 'string')]
    #[Assert\Length(min: 1, max: 200)]
    protected $title;

    /**
     * @var string
     */
    #[ORM\Column(name: 'short_title', type: 'string', length: 10, nullable: true)]
    #[IA\Expose]
    #[IA\Type('string')]
    #[Assert\Type(type: 'string')]
    #[Assert\Length(min: 1, max: 10)]
    protected $shortTitle;

    /**
     * @var int
     */
    #[ORM\Column(name: 'duration', type: 'smallint')]
    #[IA\Expose]
    #[IA\Type('integer')]
    #[Assert\NotBlank]
    #[Assert\Type(type: 'integer')]
    protected $duration;

    /**
     * @var SchoolInterface
     */
    #[ORM\ManyToOne(targetEntity: 'School', inversedBy: 'programs')]
    #[ORM\JoinColumn(name: 'school_id', referencedColumnName: 'school_id', nullable: false)]
    #[IA\Expose]
    #[IA\Type('entity')]
    #[Assert\NotNull]
    protected $school;

    /**
     * @var ArrayCollection|ProgramYearInterface[]
     */
    #[ORM\OneToMany(targetEntity: 'ProgramYear', mappedBy: 'program')]
    #[ORM\OrderBy(['id' => 'ASC'])]
    #[IA\Expose]
    #[IA\Type('entityCollection')]
    protected $programYears;

    /**
     * @var ArrayCollection|CurriculumInventoryReportInterface[]
     */
    #[ORM\OneToMany(targetEntity: 'CurriculumInventoryReport', mappedBy: 'program')]
    #[ORM\OrderBy(['id' => 'ASC'])]
    #[IA\Expose]
    #[IA\Type('entityCollection')]
    protected $curriculumInventoryReports;

    /**
     * @var ArrayCollection|UserInterface[]
     */
    #[ORM\ManyToMany(targetEntity: 'User', inversedBy: 'directedPrograms')]
    #[ORM\JoinTable(name: 'program_director')]
    #[ORM\JoinColumn(name: 'program_id', referencedColumnName: 'program_id', onDelete: 'CASCADE')]
    #[ORM\InverseJoinColumn(name: 'user_id', referencedColumnName: 'user_id', onDelete: 'CASCADE')]
    #[ORM\OrderBy(['id' => 'ASC'])]
    #[IA\Expose]
    #[IA\Type('entityCollection')]
    protected $directors;

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

    public function getShortTitle(): string
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

    public function getDuration(): int
    {
        return $this->duration;
    }

    public function setCurriculumInventoryReports(Collection $reports)
    {
        $this->curriculumInventoryReports = new ArrayCollection();

        foreach ($reports as $report) {
            $this->addCurriculumInventoryReport($report);
        }
    }

    public function addCurriculumInventoryReport(CurriculumInventoryReportInterface $report)
    {
        if (!$this->curriculumInventoryReports->contains($report)) {
            $this->curriculumInventoryReports->add($report);
        }
    }

    public function removeCurriculumInventoryReport(CurriculumInventoryReportInterface $report)
    {
        if ($this->curriculumInventoryReports->contains($report)) {
            $this->curriculumInventoryReports->removeElement($report);
        }
    }

    public function getCurriculumInventoryReports(): Collection
    {
        return $this->curriculumInventoryReports;
    }

    public function addDirector(UserInterface $director)
    {
        if (!$this->directors->contains($director)) {
            $this->directors->add($director);
            $director->addDirectedProgram($this);
        }
    }

    public function removeDirector(UserInterface $director)
    {
        if ($this->directors->contains($director)) {
            $this->directors->removeElement($director);
            $director->removeDirectedProgram($this);
        }
    }
}
