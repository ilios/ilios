<?php

declare(strict_types=1);

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use App\Attributes as IA;
use Symfony\Component\Validator\Constraints as Assert;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use App\Traits\DescribableNullableEntity;
use App\Traits\IdentifiableEntity;
use App\Traits\NameableEntity;
use App\Traits\StringableIdEntity;
use App\Repository\CurriculumInventoryAcademicLevelRepository;

#[ORM\Table(name: 'curriculum_inventory_academic_level')]
#[ORM\UniqueConstraint(name: 'report_id_level', columns: ['report_id', 'level'])]
#[ORM\Index(columns: ['report_id'], name: 'IDX_B4D3296D4BD2A4C0')]
#[ORM\Entity(repositoryClass: CurriculumInventoryAcademicLevelRepository::class)]
#[IA\Entity]
class CurriculumInventoryAcademicLevel implements CurriculumInventoryAcademicLevelInterface
{
    use IdentifiableEntity;
    use NameableEntity;
    use DescribableNullableEntity;
    use StringableIdEntity;

    #[ORM\Id]
    #[ORM\Column(name: 'academic_level_id', type: 'integer')]
    #[ORM\GeneratedValue(strategy: 'AUTO')]
    #[IA\Expose]
    #[IA\Type('integer')]
    #[IA\OnlyReadable]
    #[Assert\Type(type: 'integer')]
    protected int $id;

    #[ORM\Column(type: 'string', length: 50)]
    #[IA\Expose]
    #[IA\Type('string')]
    #[Assert\NotBlank]
    #[Assert\Type(type: 'string')]
    #[Assert\Length(min: 1, max: 50)]
    protected string $name;

    #[ORM\Column(name: 'description', type: 'text', nullable: true)]
    #[IA\Expose]
    #[IA\Type('string')]
    #[Assert\Type(type: 'string')]
    #[Assert\Length(max: 65000)]
    protected ?string $description = null;

    #[ORM\Column(name: 'level', type: 'integer')]
    #[IA\Expose]
    #[IA\Type('integer')]
    #[Assert\NotBlank]
    #[Assert\Type(type: 'integer')]
    protected int $level;

    #[ORM\ManyToOne(targetEntity: 'CurriculumInventoryReport', inversedBy: 'academicLevels')]
    #[ORM\JoinColumn(name: 'report_id', referencedColumnName: 'report_id', onDelete: 'cascade')]
    #[IA\Expose]
    #[IA\Type('entity')]
    protected CurriculumInventoryReportInterface $report;

    #[ORM\OneToMany(mappedBy: 'startingAcademicLevel', targetEntity: 'CurriculumInventorySequenceBlock')]
    #[ORM\OrderBy(['id' => 'ASC'])]
    #[IA\Expose]
    #[IA\Type(IA\Type::ENTITY_COLLECTION)]
    protected Collection $startingSequenceBlocks;

    #[ORM\OneToMany(mappedBy: 'endingAcademicLevel', targetEntity: 'CurriculumInventorySequenceBlock')]
    #[ORM\OrderBy(['id' => 'ASC'])]
    #[IA\Expose]
    #[IA\Type(IA\Type::ENTITY_COLLECTION)]
    protected Collection $endingSequenceBlocks;

    public function __construct()
    {
        $this->startingSequenceBlocks = new ArrayCollection();
        $this->endingSequenceBlocks = new ArrayCollection();
    }

    public function setLevel(int $level): void
    {
        $this->level = $level;
    }

    public function getLevel(): int
    {
        return $this->level;
    }

    public function setReport(CurriculumInventoryReportInterface $report): void
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
