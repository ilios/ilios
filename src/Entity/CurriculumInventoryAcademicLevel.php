<?php

declare(strict_types=1);

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use App\Traits\SequenceBlocksEntity;
use App\Annotation as IS;
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
 *   uniqueConstraints={
 *   },
 *   indexes={
 *   }
 * )
 * @IS\Entity
 */
#[ORM\Table(name: 'curriculum_inventory_academic_level')]
#[ORM\UniqueConstraint(name: 'report_id_level', columns: ['report_id', 'level'])]
#[ORM\Index(name: 'IDX_B4D3296D4BD2A4C0', columns: ['report_id'])]
#[ORM\Entity(repositoryClass: CurriculumInventoryAcademicLevelRepository::class)]
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
     * @IS\Expose
     * @IS\Type("integer")
     * @IS\ReadOnly
     */
    #[ORM\Id]
    #[ORM\Column(name: 'academic_level_id', type: 'integer')]
    #[ORM\GeneratedValue(strategy: 'AUTO')]
    protected $id;
    /**
     * @var string
     * @Assert\NotBlank()
     * @Assert\Type(type="string")
     * @Assert\Length(
     *      min = 1,
     *      max = 50
     * )
     * @IS\Expose
     * @IS\Type("string")
     */
    #[ORM\Column(type: 'string', length: 50)]
    protected $name;
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
     * @var int
     * @Assert\NotBlank()
     * @Assert\Type(type="integer")
     * @IS\Expose
     * @IS\Type("integer")
     */
    #[ORM\Column(name: 'level', type: 'integer')]
    protected $level;
    /**
     * @var CurriculumInventoryReportInterface
     * })
     * @IS\Expose
     * @IS\Type("entity")
     */
    #[ORM\ManyToOne(targetEntity: 'CurriculumInventoryReport', inversedBy: 'academicLevels')]
    #[ORM\JoinColumn(name: 'report_id', referencedColumnName: 'report_id', onDelete: 'cascade')]
    protected $report;
    /**
     * @var ArrayCollection|CurriculumInventorySequenceBlockInterface[]
     * @IS\Expose
     * @IS\Type("entityCollection")
     */
    #[ORM\OneToMany(targetEntity: 'CurriculumInventorySequenceBlock', mappedBy: 'academicLevel')]
    #[ORM\OrderBy(['id' => 'ASC'])]
    protected $sequenceBlocks;
    /**
     * Constructor
     */
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
