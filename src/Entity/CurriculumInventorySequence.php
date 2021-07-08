<?php

declare(strict_types=1);

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use App\Traits\StringableIdEntity;
use App\Annotation as IS;
use Symfony\Component\Validator\Constraints as Assert;
use App\Traits\DescribableEntity;
use App\Traits\IdentifiableEntity;
use App\Repository\CurriculumInventorySequenceRepository;

/**
 * Class CurriculumInventorySequence
 * @IS\Entity
 */
#[ORM\Table(name: 'curriculum_inventory_sequence')]
#[ORM\Entity(repositoryClass: CurriculumInventorySequenceRepository::class)]
class CurriculumInventorySequence implements CurriculumInventorySequenceInterface
{
    use IdentifiableEntity;
    use DescribableEntity;
    use StringableIdEntity;
    /**
     * @var int
     * @Assert\Type(type="integer")
     * @IS\Expose
     * @IS\Type("integer")
     * @IS\ReadOnly
     */
    #[ORM\Column(name: 'sequence_id', type: 'integer')]
    #[ORM\Id]
    #[ORM\GeneratedValue(strategy: 'IDENTITY')]
    protected $id;
    /**
     * @var CurriculumInventoryReportInterface
     * @Assert\NotNull()
     *     name="report_id",
     *     referencedColumnName="report_id",
     *     unique=true,
     *     nullable=false,
     *     onDelete="cascade"
     *   )
     * })
     * @IS\Expose
     * @IS\Type("entity")
     */
    #[ORM\OneToOne(targetEntity: 'CurriculumInventoryReport', inversedBy: 'sequence')]
    #[ORM\JoinColumn(
        name: 'report_id',
        referencedColumnName: 'report_id',
        unique: true,
        nullable: false,
        onDelete: 'cascade'
    )]
    protected $report;
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
     * @param CurriculumInventoryReportInterface $report
     */
    public function setReport(CurriculumInventoryReportInterface $report)
    {
        $this->report = $report;
    }
    /**
     * @return CurriculumInventoryReportInterface
     */
    public function getReport()
    {
        return $this->report;
    }
}
