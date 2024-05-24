<?php

declare(strict_types=1);

namespace App\Entity;

use DateTime;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use App\Traits\AdministratorsEntity;
use App\Traits\SequenceBlocksEntity;
use App\Attributes as IA;
use Symfony\Component\Validator\Constraints as Assert;
use App\Traits\DescribableNullableEntity;
use App\Traits\IdentifiableEntity;
use App\Traits\StringableIdEntity;
use App\Repository\CurriculumInventoryReportRepository;

#[ORM\Table(name: 'curriculum_inventory_report')]
#[ORM\Index(columns: ['program_id'], name: 'IDX_6E31899E3EB8070A')]
#[ORM\UniqueConstraint(name: 'idx_ci_report_token_unique', columns: ['token'])]
#[ORM\Entity(repositoryClass: CurriculumInventoryReportRepository::class)]
#[IA\Entity]
class CurriculumInventoryReport implements CurriculumInventoryReportInterface
{
    use IdentifiableEntity;
    use DescribableNullableEntity;
    use StringableIdEntity;
    use SequenceBlocksEntity;
    use AdministratorsEntity;

    #[ORM\Column(name: 'report_id', type: 'integer')]
    #[ORM\Id]
    #[ORM\GeneratedValue(strategy: 'IDENTITY')]
    #[IA\Expose]
    #[IA\Type('integer')]
    #[IA\OnlyReadable]
    #[Assert\Type(type: 'integer')]
    protected int $id;

    #[ORM\Column(type: 'string', length: 200, nullable: true)]
    #[IA\Expose]
    #[IA\Type('string')]
    #[Assert\Type(type: 'string')]
    #[Assert\Length(max: 200)]
    protected ?string $name = null;

    #[ORM\Column(name: 'description', type: 'text', nullable: true)]
    #[IA\Expose]
    #[IA\Type('string')]
    #[Assert\Type(type: 'string')]
    #[Assert\Length(max: 65000)]
    protected ?string $description = null;

    #[ORM\Column(name: 'year', type: 'smallint')]
    #[IA\Expose]
    #[IA\Type('integer')]
    #[Assert\NotBlank]
    #[Assert\Type(type: 'integer')]
    protected int $year;

    #[ORM\Column(name: 'start_date', type: 'date')]
    #[IA\Expose]
    #[IA\Type('dateTime')]
    #[Assert\NotBlank]
    protected DateTime $startDate;

    #[ORM\Column(name: 'end_date', type: 'date')]
    #[IA\Expose]
    #[IA\Type('dateTime')]
    #[Assert\NotBlank]
    protected DateTime $endDate;

    #[ORM\OneToOne(mappedBy: 'report', targetEntity: 'CurriculumInventoryExport')]
    #[IA\Expose]
    #[IA\Type('entity')]
    protected ?CurriculumInventoryExportInterface $export = null;

    #[ORM\OneToOne(mappedBy: 'report', targetEntity: 'CurriculumInventorySequence')]
    #[IA\Expose]
    #[IA\Type('entity')]
    protected ?CurriculumInventorySequenceInterface $sequence = null;

    #[ORM\OneToMany(mappedBy: 'report', targetEntity: 'CurriculumInventorySequenceBlock')]
    #[ORM\OrderBy(['id' => 'ASC'])]
    #[IA\Expose]
    #[IA\Type(IA\Type::ENTITY_COLLECTION)]
    protected Collection $sequenceBlocks;

    #[ORM\ManyToOne(targetEntity: 'Program', inversedBy: 'curriculumInventoryReports')]
    #[ORM\JoinColumn(name: 'program_id', referencedColumnName: 'program_id')]
    #[IA\Expose]
    #[IA\Type('entity')]
    protected ?ProgramInterface $program = null;

    #[ORM\OneToMany(mappedBy: 'report', targetEntity: 'CurriculumInventoryAcademicLevel')]
    #[ORM\OrderBy(['id' => 'ASC'])]
    #[IA\Expose]
    #[IA\Type(IA\Type::ENTITY_COLLECTION)]
    protected Collection $academicLevels;

    #[ORM\Column(name: 'token', type: 'string', length: 64, nullable: true)]
    #[Assert\Type(type: 'string')]
    #[Assert\Length(max: 64)]
    protected string $token;

    #[ORM\ManyToMany(targetEntity: 'User', inversedBy: 'administeredCurriculumInventoryReports')]
    #[ORM\JoinTable(name: 'curriculum_inventory_report_administrator')]
    #[ORM\JoinColumn(name: 'report_id', referencedColumnName: 'report_id', onDelete: 'CASCADE')]
    #[ORM\InverseJoinColumn(name: 'user_id', referencedColumnName: 'user_id', onDelete: 'CASCADE')]
    #[ORM\OrderBy(['id' => 'ASC'])]
    #[IA\Expose]
    #[IA\Type(IA\Type::ENTITY_COLLECTION)]
    protected Collection $administrators;

    public function __construct()
    {
        $this->academicLevels = new ArrayCollection();
        $this->sequenceBlocks = new ArrayCollection();
        $this->administrators = new ArrayCollection();
    }

    public function setName(?string $name): void
    {
        $this->name = $name;
    }
    public function getName(): ?string
    {
        return $this->name;
    }

    public function setYear(int $year): void
    {
        $this->year = $year;
    }

    public function getYear(): int
    {
        return $this->year;
    }

    public function setStartDate(DateTime $startDate): void
    {
        $this->startDate = $startDate;
    }

    public function getStartDate(): DateTime
    {
        return $this->startDate;
    }

    public function setEndDate(DateTime $endDate): void
    {
        $this->endDate = $endDate;
    }

    public function getEndDate(): DateTime
    {
        return $this->endDate;
    }

    public function setExport(?CurriculumInventoryExportInterface $export = null): void
    {
        $this->export = $export;
    }

    public function getExport(): ?CurriculumInventoryExportInterface
    {
        return $this->export;
    }

    public function setSequence(?CurriculumInventorySequenceInterface $sequence = null): void
    {
        $this->sequence = $sequence;
    }

    public function getSequence(): ?CurriculumInventorySequenceInterface
    {
        return $this->sequence;
    }

    public function setProgram(?ProgramInterface $program = null): void
    {
        $this->program = $program;
    }

    public function getProgram(): ?ProgramInterface
    {
        return $this->program;
    }

    public function setAcademicLevels(?Collection $academicLevels = null): void
    {
        $this->academicLevels = new ArrayCollection();
        if (is_null($academicLevels)) {
            return;
        }
        foreach ($academicLevels as $academicLevel) {
            $this->addAcademicLevel($academicLevel);
        }
    }

    public function addAcademicLevel(CurriculumInventoryAcademicLevelInterface $academicLevel): void
    {
        if (!$this->academicLevels->contains($academicLevel)) {
            $this->academicLevels->add($academicLevel);
        }
    }

    public function removeAcademicLevel(CurriculumInventoryAcademicLevelInterface $academicLevel): void
    {
        $this->academicLevels->removeElement($academicLevel);
    }

    public function getAcademicLevels(): Collection
    {
        return $this->academicLevels;
    }

    public function getSchool(): ?SchoolInterface
    {
        if ($program = $this->getProgram()) {
            return $program->getSchool();
        }
        return null;
    }

    public function getToken(): string
    {
        return $this->token;
    }

    public function generateToken(): void
    {
        $random = random_bytes(128);

        // prepend id to avoid a conflict
        // and current time to prevent a conflict with regeneration
        $key = $this->getId() . microtime() . $random;

        // hash the string to give consistent length and URL safe characters
        $this->token = hash('sha256', $key);
    }

    public function addAdministrator(UserInterface $administrator): void
    {
        if (!$this->administrators->contains($administrator)) {
            $this->administrators->add($administrator);
            $administrator->addAdministeredCurriculumInventoryReport($this);
        }
    }

    public function removeAdministrator(UserInterface $administrator): void
    {
        if ($this->administrators->contains($administrator)) {
            $this->administrators->removeElement($administrator);
            $administrator->removeAdministeredCurriculumInventoryReport($this);
        }
    }
}
