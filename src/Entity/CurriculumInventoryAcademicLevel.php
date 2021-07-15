<?php

declare(strict_types=1);

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use App\Traits\SequenceBlocksEntity;
use App\Attribute as IA;
use Symfony\Component\Validator\Constraints as Assert;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use App\Traits\DescribableEntity;
use App\Traits\IdentifiableEntity;
use App\Traits\NameableEntity;
use App\Traits\StringableIdEntity;
use App\Entity\CurriculumInventoryReportInterface;
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
    use SequenceBlocksEntity;

    /**
     * @var int
     * @Assert\Type(type="integer")
     */
    #[ORM\Id]
    #[ORM\Column(name: 'academic_level_id', type: 'integer')]
    #[ORM\GeneratedValue(strategy: 'AUTO')]
    #[IA\Expose]
    #[IA\Type('integer')]
    #[IA\ReadOnly]
    protected $id;

    /**
     * @var string
     * @Assert\NotBlank()
     * @Assert\Type(type="string")
     * @Assert\Length(
     *      min = 1,
     *      max = 50
     * )
     */
    #[ORM\Column(type: 'string', length: 50)]
    #[IA\Expose]
    #[IA\Type('string')]
    protected $name;

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

    /**
     * @var int
     * @Assert\NotBlank()
     * @Assert\Type(type="integer")
     */
    #[ORM\Column(name: 'level', type: 'integer')]
    #[IA\Expose]
    #[IA\Type('integer')]
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
    #[ORM\OneToMany(mappedBy: 'academicLevel', targetEntity: 'CurriculumInventorySequenceBlock')]
    #[ORM\OrderBy(['id' => 'ASC'])]
    #[IA\Expose]
    #[IA\Type('entityCollection')]
    protected $sequenceBlocks;

    public function __construct()
    {
        $this->sequenceBlocks = new ArrayCollection();
    }

    /**
     * @param int $level
     */
    public function setLevel($level)
    {
        $this->level = $level;
    }

    /**
     * @return int
     */
    public function getLevel()
    {
        return $this->level;
    }

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
