<?php

namespace Ilios\CoreBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Ilios\CoreBundle\Traits\SequenceBlocksEntity;
use Ilios\ApiBundle\Annotation as IS;
use Symfony\Component\Validator\Constraints as Assert;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;

use Ilios\CoreBundle\Traits\DescribableEntity;
use Ilios\CoreBundle\Traits\IdentifiableEntity;
use Ilios\CoreBundle\Traits\NameableEntity;
use Ilios\CoreBundle\Traits\StringableIdEntity;

use Ilios\CoreBundle\Entity\CurriculumInventoryReportInterface;

/**
 * Class CurriculumInventoryAcademicLevel
 *
 * @ORM\Table(name="curriculum_inventory_academic_level",
 *   uniqueConstraints={
 *     @ORM\UniqueConstraint(name="report_id_level", columns={"report_id", "level"})
 *   },
 *   indexes={
 *     @ORM\Index(name="IDX_B4D3296D4BD2A4C0", columns={"report_id"})
 *   }
 * )
 * @ORM\Entity(repositoryClass="Ilios\CoreBundle\Entity\Repository\CurriculumInventoryAcademicLevelRepository")
 *
 * @IS\Entity
 */
class CurriculumInventoryAcademicLevel implements CurriculumInventoryAcademicLevelInterface
{
    use IdentifiableEntity;
    use NameableEntity;
    use DescribableEntity;
    use StringableIdEntity;
    use SequenceBlocksEntity;

    /**
     * @var int
     *
     * @ORM\Id
     * @ORM\Column(name="academic_level_id", type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     *
     * @Assert\Type(type="integer")
     *
     * @IS\Expose
     * @IS\Type("integer")
     * @IS\ReadOnly
     */
    protected $id;

    /**
     * @var string
     *
     * @ORM\Column(type="string", length=50)
     *
     * @Assert\NotBlank()
     * @Assert\Type(type="string")
     * @Assert\Length(
     *      min = 1,
     *      max = 50
     * )
     *
     * @IS\Expose
     * @IS\Type("string")
     *
    */
    protected $name;

    /**
     * @var string
     *
     * @ORM\Column(name="description", type="text", nullable=true)
     *
     * @Assert\Type(type="string")
     * @Assert\Length(
     *      min = 1,
     *      max = 65000
     * )
     *
     * @IS\Expose
     * @IS\Type("string")
    */
    protected $description;

    /**
     * @var int
     *
     * @ORM\Column(name="level", type="integer")
     *
     * @Assert\NotBlank()
     * @Assert\Type(type="integer")
     *
     * @IS\Expose
     * @IS\Type("integer")
     */
    protected $level;

    /**
     * @var CurriculumInventoryReportInterface
     *
     * @ORM\ManyToOne(targetEntity="CurriculumInventoryReport", inversedBy="academicLevels")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="report_id", referencedColumnName="report_id", onDelete="cascade")
     * })
     *
     * @IS\Expose
     * @IS\Type("entity")
     */
    protected $report;

    /**
    * @var ArrayCollection|CurriculumInventorySequenceBlockInterface[]
    *
    * @ORM\OneToMany(targetEntity="CurriculumInventorySequenceBlock", mappedBy="academicLevel")
    *
    * @IS\Expose
    * @IS\Type("entityCollection")
    */
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
