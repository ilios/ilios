<?php

declare(strict_types=1);

namespace App\Entity;

use DateTime;
use Doctrine\ORM\Mapping as ORM;
use App\Traits\SessionsEntity;
use App\Attributes as IA;
use Doctrine\Common\Collections\Collection;
use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Component\Validator\Constraints as Assert;
use App\Traits\DescribableNullableEntity;
use App\Traits\IdentifiableEntity;
use App\Traits\TitledEntity;
use App\Traits\StringableIdEntity;
use App\Repository\CurriculumInventorySequenceBlockRepository;

#[ORM\Table(name: 'curriculum_inventory_sequence_block')]
#[ORM\Entity(repositoryClass: CurriculumInventorySequenceBlockRepository::class)]
#[IA\Entity]
class CurriculumInventorySequenceBlock implements CurriculumInventorySequenceBlockInterface
{
    use IdentifiableEntity;
    use DescribableNullableEntity;
    use TitledEntity;
    use StringableIdEntity;
    use SessionsEntity;

    #[ORM\Column(name: 'sequence_block_id', type: 'integer')]
    #[ORM\Id]
    #[ORM\GeneratedValue(strategy: 'IDENTITY')]
    #[IA\Expose]
    #[IA\Type('integer')]
    #[IA\OnlyReadable]
    #[Assert\Type(type: 'integer')]
    protected int $id;

    #[ORM\Column(type: 'string', length: 200)]
    #[IA\Expose]
    #[IA\Type('string')]
    #[Assert\NotBlank]
    #[Assert\Type(type: 'string')]
    #[Assert\Length(min: 1, max: 200)]
    protected string $title;

    #[ORM\Column(name: 'description', type: 'text', nullable: true)]
    #[IA\Expose]
    #[IA\Type('string')]
    #[Assert\Type(type: 'string')]
    #[Assert\Length(max: 65000)]
    protected ?string $description = null;

    #[ORM\Column(name: 'required', type: 'integer')]
    #[IA\Expose]
    #[IA\Type('integer')]
    #[Assert\NotNull]
    #[Assert\Type(type: 'integer')]
    protected int $required;

    #[ORM\Column(name: 'child_sequence_order', type: 'smallint')]
    #[IA\Expose]
    #[IA\Type('integer')]
    #[Assert\NotBlank]
    #[Assert\Type(type: 'integer')]
    #[Assert\Range(min: 1, max: 3)]
    protected int $childSequenceOrder;

    #[ORM\Column(name: 'order_in_sequence', type: 'integer')]
    #[IA\Expose]
    #[IA\Type('integer')]
    #[Assert\NotBlank]
    #[Assert\Type(type: 'integer')]
    protected int $orderInSequence;

    #[ORM\Column(name: 'minimum', type: 'integer')]
    #[IA\Expose]
    #[IA\Type('integer')]
    #[Assert\NotBlank]
    #[Assert\Type(type: 'integer')]
    protected int $minimum;

    #[ORM\Column(name: 'maximum', type: 'integer')]
    #[IA\Expose]
    #[IA\Type('integer')]
    #[Assert\NotBlank]
    #[Assert\Type(type: 'integer')]
    protected int $maximum;

    #[ORM\Column(name: 'track', type: 'boolean')]
    #[IA\Expose]
    #[IA\Type('boolean')]
    #[Assert\NotNull]
    #[Assert\Type(type: 'bool')]
    protected bool $track;

    #[ORM\Column(name: 'start_date', type: 'date', nullable: true)]
    #[IA\Expose]
    #[IA\Type('dateTime')]
    protected ?DateTime $startDate = null;

    #[ORM\Column(name: 'end_date', type: 'date', nullable: true)]
    #[IA\Expose]
    #[IA\Type('dateTime')]
    protected ?DateTime $endDate = null;

    #[ORM\Column(name: 'duration', type: 'integer')]
    #[IA\Expose]
    #[IA\Type('integer')]
    #[Assert\NotBlank]
    #[Assert\Type(type: 'integer')]
    protected int $duration;

    #[ORM\ManyToOne(targetEntity: 'CurriculumInventoryAcademicLevel', inversedBy: 'startingSequenceBlocks')]
    #[ORM\JoinColumn(
        name: 'starting_academic_level_id',
        referencedColumnName: 'academic_level_id',
        nullable: false,
        onDelete: 'cascade'
    )]
    #[IA\Expose]
    #[IA\Type('entity')]
    protected CurriculumInventoryAcademicLevelInterface $startingAcademicLevel;

    #[ORM\ManyToOne(targetEntity: 'CurriculumInventoryAcademicLevel', inversedBy: 'endingSequenceBlocks')]
    #[ORM\JoinColumn(
        name: 'ending_academic_level_id',
        referencedColumnName: 'academic_level_id',
        nullable: false,
        onDelete: 'cascade'
    )]
    #[IA\Expose]
    #[IA\Type('entity')]
    protected CurriculumInventoryAcademicLevelInterface $endingAcademicLevel;

    #[ORM\ManyToOne(targetEntity: 'Course', inversedBy: 'sequenceBlocks')]
    #[ORM\JoinColumn(name: 'course_id', referencedColumnName: 'course_id')]
    #[IA\Expose]
    #[IA\Type('entity')]
    protected ?CourseInterface $course = null;

    #[ORM\ManyToOne(targetEntity: 'CurriculumInventorySequenceBlock', inversedBy: 'children')]
    #[ORM\JoinColumn(
        name: 'parent_sequence_block_id',
        referencedColumnName: 'sequence_block_id',
        onDelete: 'cascade'
    )]
    #[IA\Expose]
    #[IA\Type('entity')]
    protected ?CurriculumInventorySequenceBlockInterface $parent = null;

    #[ORM\OneToMany(mappedBy: 'parent', targetEntity: 'CurriculumInventorySequenceBlock')]
    #[ORM\OrderBy(['id' => 'ASC'])]
    #[IA\Expose]
    #[IA\Type(IA\Type::ENTITY_COLLECTION)]
    protected Collection $children;

    #[ORM\ManyToOne(targetEntity: 'CurriculumInventoryReport', inversedBy: 'sequenceBlocks')]
    #[ORM\JoinColumn(name: 'report_id', referencedColumnName: 'report_id', onDelete: 'cascade')]
    #[IA\Expose]
    #[IA\Type('entity')]
    #[Assert\NotNull]
    protected CurriculumInventoryReportInterface $report;

    #[ORM\ManyToMany(targetEntity: 'Session', inversedBy: 'sequenceBlocks')]
    #[ORM\JoinTable('curriculum_inventory_sequence_block_x_session')]
    #[ORM\JoinColumn(name: 'sequence_block_id', referencedColumnName: 'sequence_block_id', onDelete: 'CASCADE')]
    #[ORM\InverseJoinColumn(name: 'session_id', referencedColumnName: 'session_id', onDelete: 'CASCADE')]
    #[ORM\OrderBy(['id' => 'ASC'])]
    #[IA\Expose]
    #[IA\Type(IA\Type::ENTITY_COLLECTION)]
    protected Collection $sessions;

    #[ORM\ManyToMany(targetEntity: 'Session', inversedBy: 'excludedSequenceBlocks')]
    #[ORM\JoinTable('curriculum_inventory_sequence_block_x_excluded_session')]
    #[ORM\JoinColumn(name: 'sequence_block_id', referencedColumnName: 'sequence_block_id', onDelete: 'CASCADE')]
    #[ORM\InverseJoinColumn(name: 'session_id', referencedColumnName: 'session_id', onDelete: 'CASCADE')]
    #[ORM\OrderBy(['id' => 'ASC'])]
    #[IA\Expose]
    #[IA\Type(IA\Type::ENTITY_COLLECTION)]
    protected Collection $excludedSessions;


    public function __construct()
    {
        $this->children = new ArrayCollection();
        $this->sessions = new ArrayCollection();
        $this->excludedSessions = new ArrayCollection();
        $this->required = self::OPTIONAL;
        $this->childSequenceOrder = self::ORDERED;
        $this->orderInSequence = 0;
        $this->maximum = 0;
        $this->minimum = 0;
        $this->track = false;
        $this->duration = 0;
    }

    public function setRequired(int $required): void
    {
        $this->required = $required;
    }

    public function getRequired(): int
    {
        return $this->required;
    }

    public function setChildSequenceOrder(int $childSequenceOrder): void
    {
        $this->childSequenceOrder = $childSequenceOrder;
    }

    public function getChildSequenceOrder(): int
    {
        return $this->childSequenceOrder;
    }

    public function setOrderInSequence(int $orderInSequence): void
    {
        $this->orderInSequence = $orderInSequence;
    }

    public function getOrderInSequence(): int
    {
        return $this->orderInSequence;
    }

    public function setMinimum(int $minimum): void
    {
        $this->minimum = $minimum;
    }

    public function getMinimum(): int
    {
        return $this->minimum;
    }

    public function setMaximum(int $maximum): void
    {
        $this->maximum = $maximum;
    }

    public function getMaximum(): int
    {
        return $this->maximum;
    }

    public function setTrack(bool $track): void
    {
        $this->track = $track;
    }

    public function hasTrack(): bool
    {
        return $this->track;
    }

    public function setStartDate(?DateTime $startDate = null): void
    {
        $this->startDate = $startDate;
    }

    public function getStartDate(): ?DateTime
    {
        return $this->startDate;
    }

    public function setEndDate(?DateTime $endDate = null): void
    {
        $this->endDate = $endDate;
    }

    public function getEndDate(): ?DateTime
    {
        return $this->endDate;
    }

    public function setDuration(int $duration): void
    {
        $this->duration = $duration;
    }

    public function getDuration(): int
    {
        return $this->duration;
    }

    public function setCourse(?CourseInterface $course = null): void
    {
        $this->course = $course;
    }

    public function getCourse(): ?CourseInterface
    {
        return $this->course;
    }

    public function setParent(?CurriculumInventorySequenceBlockInterface $parent = null): void
    {
        $this->parent = $parent;
    }

    public function getParent(): ?CurriculumInventorySequenceBlockInterface
    {
        return $this->parent;
    }

    public function setChildren(Collection $children): void
    {
        $this->children = new ArrayCollection();

        foreach ($children as $child) {
            $this->addChild($child);
        }
    }

    public function addChild(CurriculumInventorySequenceBlockInterface $child): void
    {
        if (!$this->children->contains($child)) {
            $this->children->add($child);
        }
    }

    public function removeChild(CurriculumInventorySequenceBlockInterface $child): void
    {
        $this->children->removeElement($child);
    }

    public function getChildren(): Collection
    {
        return $this->children;
    }

    public function setReport(CurriculumInventoryReportInterface $report): void
    {
        $this->report = $report;
    }

    public function getReport(): CurriculumInventoryReportInterface
    {
        return $this->report;
    }

    public function getChildrenAsSortedList(): array
    {
        $children = $this->getChildren()->toArray();
        $sortStrategy = $this->getChildSequenceOrder();
        switch ($sortStrategy) {
            case self::ORDERED:
                usort($children, [__CLASS__, 'compareSequenceBlocksWithOrderedStrategy']);
                break;
            case self::UNORDERED:
            case self::PARALLEL:
            default:
                usort($children, [__CLASS__, 'compareSequenceBlocksWithDefaultStrategy']);
                break;
        }
        return $children;
    }

    /**
     * Callback function for comparing sequence blocks.
     * The applied criterion for comparison is the <pre>orderInSequence</pre> property.
     */
    public static function compareSequenceBlocksWithOrderedStrategy(
        CurriculumInventorySequenceBlockInterface $a,
        CurriculumInventorySequenceBlockInterface $b
    ): int {
        if ($a->getOrderInSequence() === $b->getOrderInSequence()) {
            return 0;
        }
        return ($a->getOrderInSequence() > $b->getOrderInSequence()) ? 1 : -1;
    }

    /**
     * Callback function for comparing sequence blocks.
     * The applied, ranked criteria for comparison are:
     * 1. "academic level"
     *      Numeric sort, ascending.
     * 2. "start date"
     *      Numeric sort on timestamps, ascending. NULL values will be treated as unix timestamp 0.
     * 3. "title"
     *    Alphabetical sort.
     * 4. "sequence block id"
     *    A last resort. Numeric sort, ascending.
     */
    public static function compareSequenceBlocksWithDefaultStrategy(
        CurriculumInventorySequenceBlockInterface $a,
        CurriculumInventorySequenceBlockInterface $b
    ): int {
        // 1. starting academic level id
        if ($a->getStartingAcademicLevel()->getLevel() > $b->getStartingAcademicLevel()->getLevel()) {
            return 1;
        } elseif ($a->getStartingAcademicLevel()->getLevel() < $b->getStartingAcademicLevel()->getLevel()) {
            return -1;
        }

        // 2. ending academic level id
        if ($a->getEndingAcademicLevel()->getLevel() > $b->getEndingAcademicLevel()->getLevel()) {
            return 1;
        } elseif ($a->getEndingAcademicLevel()->getLevel() < $b->getEndingAcademicLevel()->getLevel()) {
            return -1;
        }

        // 2. start date
        $startDateA = $a->getStartDate() ? $a->getStartDate()->getTimestamp() : 0;
        $startDateB = $b->getStartDate() ? $b->getStartDate()->getTimestamp() : 0;

        if ($startDateA > $startDateB) {
            return 1;
        } elseif ($startDateA < $startDateB) {
            return -1;
        }

        // 3. title comparison
        $n = strcasecmp($a->getTitle(), $b->getTitle());
        if ($n) {
            return $n > 0 ? 1 : -1;
        }

        // 4. sequence block id comparison
        if ($a->getId() > $b->getId()) {
            return 1;
        } elseif ($a->getId() < $b->getId()) {
            return -1;
        }
        return 0;
    }

    public function setExcludedSessions(Collection $sessions): void
    {
        $this->excludedSessions = new ArrayCollection();

        foreach ($sessions as $session) {
            $this->addExcludedSession($session);
        }
    }

    public function addExcludedSession(SessionInterface $session): void
    {
        if (!$this->excludedSessions->contains($session)) {
            $this->excludedSessions->add($session);
        }
    }

    public function removeExcludedSession(SessionInterface $session): void
    {
        $this->excludedSessions->removeElement($session);
    }

    public function getExcludedSessions(): Collection
    {
        return $this->excludedSessions;
    }

    public function setStartingAcademicLevel(?CurriculumInventoryAcademicLevelInterface $level = null): void
    {
        $this->startingAcademicLevel = $level;
    }

    public function setEndingAcademicLevel(?CurriculumInventoryAcademicLevelInterface $level = null): void
    {
        $this->endingAcademicLevel = $level;
    }

    public function getStartingAcademicLevel(): CurriculumInventoryAcademicLevelInterface
    {
        return $this->startingAcademicLevel;
    }

    public function getEndingAcademicLevel(): CurriculumInventoryAcademicLevelInterface
    {
        return $this->endingAcademicLevel;
    }
}
