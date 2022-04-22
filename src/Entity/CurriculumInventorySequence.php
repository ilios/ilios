<?php

declare(strict_types=1);

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use App\Traits\StringableIdEntity;
use App\Attribute as IA;
use Symfony\Component\Validator\Constraints as Assert;
use App\Traits\DescribableEntity;
use App\Traits\IdentifiableEntity;
use App\Repository\CurriculumInventorySequenceRepository;

/**
 * Class CurriculumInventorySequence
 */
#[ORM\Table(name: 'curriculum_inventory_sequence')]
#[ORM\Entity(repositoryClass: CurriculumInventorySequenceRepository::class)]
#[IA\Entity]
class CurriculumInventorySequence implements CurriculumInventorySequenceInterface
{
    use IdentifiableEntity;
    use DescribableEntity;
    use StringableIdEntity;

    /**
     * @var int
     * @Assert\Type(type="integer")
     */
    #[ORM\Column(name: 'sequence_id', type: 'integer')]
    #[ORM\Id]
    #[ORM\GeneratedValue(strategy: 'IDENTITY')]
    #[IA\Expose]
    #[IA\Type('integer')]
    #[IA\OnlyReadable]
    protected $id;

    /**
     * @var CurriculumInventoryReportInterface
     * @Assert\NotNull()
     */
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
    protected $report;

    /**
     * @var string
     * @Assert\Type(type="string")
     * @Assert\AtLeastOneOf({
     *     @Assert\Blank,
     *     @Assert\Length(min=1,max=65000)
     * })
     */
    #[ORM\Column(name: 'description', type: 'text', nullable: true)]
    #[IA\Expose]
    #[IA\Type('string')]
    protected $description;

    public function setReport(CurriculumInventoryReportInterface $report)
    {
        $this->report = $report;
    }

    public function getReport(): CurriculumInventoryReportInterface
    {
        return $this->report;
    }
}
