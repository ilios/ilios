<?php

declare(strict_types=1);

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use App\Attribute as IA;
use Symfony\Component\Validator\Constraints as Assert;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use App\Traits\DescribableEntity;
use App\Traits\IdentifiableEntity;
use App\Traits\NameableEntity;
use App\Traits\StringableIdEntity;
use App\Repository\CurriculumInventoryAcademicLevelRepository;

/**
 * Class CurriculumInventoryAcademicLevel
 */
#[ORM\Table(name: 'curriculum_inventory_academic_level')]
#[ORM\UniqueConstraint(name: 'report_id_level', columns: ['report_id', 'level'])]
#[ORM\Index(columns: ['report_id'], name: 'IDX_B4D3296D4BD2A4C0')]
#[ORM\Entity(repositoryClass: CurriculumInventoryAcademicLevelRepository::class)]
#[IA\Entity]
class CurriculumInventoryAcademicLevel implements CurriculumInventoryAcademicLevelInterface
{
    use IdentifiableEntity;
    use NameableEntity;
    use DescribableEntity;
    use StringableIdEntity;

    /**
     * @var int
     */
    #[ORM\Id]
    #[ORM\Column(name: 'academic_level_id', type: 'integer')]
    #[ORM\GeneratedValue(strategy: 'AUTO')]
    #[IA\Expose]
    #[IA\Type('integer')]
    #[IA\OnlyReadable]
    #[Assert\Type(type: 'integer')]
    protected $id;

    /**
     * @var string
     */
    #[ORM\Column(type: 'string', length: 50)]
    #[IA\Expose]
    #[IA\Type('string')]
    #[Assert\NotBlank]
    #[Assert\Type(type: 'string')]
    #[Assert\Length(min: 1, max: 50)]
    protected $name;

    /**
     * @var string
     */
    #[ORM\Column(name: 'description', type: 'text', nullable: true)]
    #[IA\Expose]
    #[IA\Type('string')]
    #[Assert\Type(type: 'string')]
    #[Assert\AtLeastOneOf([
        new Assert\Blank(),
        new Assert\Length(min: 1, max: 65000),
    ])]
    protected $description;
    /**
     * @var int
     */
    #[ORM\Column(name: 'level', type: 'integer')]
    #[IA\Expose]
    #[IA\Type('integer')]
    #[Assert\NotBlank]
    #[Assert\Type(type: 'integer')]
    protected $level;

    /**
     * @var CurriculumInventoryReportInterface
     */
    #[ORM\ManyToOne(targetEntity: 'CurriculumInventoryReport', inversedBy: 'academicLevels')]
    #[ORM\JoinColumn(name: 'report_id', referencedColumnName: 'report_id', onDelete: 'cascade')]
    #[IA\Expose]
    #[IA\Type('entity')]
    protected $report;

    /**
     * @var ArrayCollection|CurriculumInventorySequenceBlockInterface[]
     */
    #[ORM\OneToMany(mappedBy: 'startingAcademicLevel', targetEntity: 'CurriculumInventorySequenceBlock')]
    #[ORM\OrderBy(['id' => 'ASC'])]
    #[IA\Expose]
    #[IA\Type('entityCollection')]
    protected $startingSequenceBlocks;

    /**
     * @var ArrayCollection|CurriculumInventorySequenceBlockInterface[]
     */
    #[ORM\OneToMany(mappedBy: 'endingAcademicLevel', targetEntity: 'CurriculumInventorySequenceBlock')]
    #[ORM\OrderBy(['id' => 'ASC'])]
    #[IA\Expose]
    #[IA\Type('entityCollection')]
    protected $endingSequenceBlocks;

    public function __construct()
    {
        $this->startingSequenceBlocks = new ArrayCollection();
        $this->endingSequenceBlocks = new ArrayCollection();
    }

    /**
     * @param int $level
     */
    public function setLevel($level)
    {
        $this->level = $level;
    }

    public function getLevel(): int
    {
        return $this->level;
    }

    public function setReport(CurriculumInventoryReportInterface $report)
    {
        $this->report = $report;
    }

    public function getReport(): CurriculumInventoryReportInterface
    {
        return $this->report;
    }

    public function setStartingSequenceBlocks(Collection $sequenceBlocks): void
    {
        $this->startingSequenceBlocks = new ArrayCollection();

        foreach ($sequenceBlocks as $sequenceBlock) {
            $this->addStartingSequenceBlock($sequenceBlock);
        }
    }

    public function addStartingSequenceBlock(
        CurriculumInventorySequenceBlockInterface $sequenceBlock
    ): void {
        if (!$this->startingSequenceBlocks->contains($sequenceBlock)) {
            $this->startingSequenceBlocks->add($sequenceBlock);
        }
    }

    public function removeStartingSequenceBlock(
        CurriculumInventorySequenceBlockInterface $sequenceBlock
    ): void {
        $this->startingSequenceBlocks->removeElement($sequenceBlock);
    }

    public function getStartingSequenceBlocks(): Collection
    {
        return $this->startingSequenceBlocks;
    }

    public function setEndingSequenceBlocks(Collection $sequenceBlocks): void
    {
        $this->startingSequenceBlocks = new ArrayCollection();

        foreach ($sequenceBlocks as $sequenceBlock) {
            $this->addEndingSequenceBlock($sequenceBlock);
        }
    }

    public function addEndingSequenceBlock(
        CurriculumInventorySequenceBlockInterface $sequenceBlock
    ): void {
        if (!$this->endingSequenceBlocks->contains($sequenceBlock)) {
            $this->endingSequenceBlocks->add($sequenceBlock);
        }
    }

    public function removeEndingSequenceBlock(
        CurriculumInventorySequenceBlockInterface $sequenceBlock
    ): void {
        $this->endingSequenceBlocks->removeElement($sequenceBlock);
    }

    public function getEndingSequenceBlocks(): Collection
    {
        return $this->endingSequenceBlocks;
    }
}
