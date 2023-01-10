<?php

declare(strict_types=1);

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use App\Traits\StringableIdEntity;
use App\Attributes as IA;
use Symfony\Component\Validator\Constraints as Assert;
use App\Traits\DescribableNullableEntity;
use App\Traits\IdentifiableEntity;
use App\Repository\CurriculumInventorySequenceRepository;

#[ORM\Table(name: 'curriculum_inventory_sequence')]
#[ORM\Entity(repositoryClass: CurriculumInventorySequenceRepository::class)]
#[IA\Entity]
class CurriculumInventorySequence implements CurriculumInventorySequenceInterface
{
    use IdentifiableEntity;
    use DescribableNullableEntity;
    use StringableIdEntity;

    #[ORM\Column(name: 'sequence_id', type: 'integer')]
    #[ORM\Id]
    #[ORM\GeneratedValue(strategy: 'IDENTITY')]
    #[IA\Expose]
    #[IA\Type('integer')]
    #[IA\OnlyReadable]
    #[Assert\Type(type: 'integer')]
    protected int $id;

    #[ORM\OneToOne(inversedBy: 'sequence', targetEntity: 'CurriculumInventoryReport')]
    #[ORM\JoinColumn(
        name: 'report_id',
        referencedColumnName: 'report_id',
        unique: true,
        nullable: false,
        onDelete: 'cascade'
    )]
    #[IA\Expose]
    #[IA\Type('entity')]
    #[Assert\NotNull]
    protected CurriculumInventoryReportInterface $report;

    #[ORM\Column(name: 'description', type: 'text', nullable: true)]
    #[IA\Expose]
    #[IA\Type('string')]
    #[Assert\Type(type: 'string')]
    #[Assert\Length(max: 65000)]
    protected ?string $description = null;

    public function setReport(CurriculumInventoryReportInterface $report)
    {
        $this->report = $report;
    }

    public function getReport(): CurriculumInventoryReportInterface
    {
        return $this->report;
    }
}
