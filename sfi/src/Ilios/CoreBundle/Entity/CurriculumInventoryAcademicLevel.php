<?php

namespace Ilios\CoreBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use JMS\Serializer\Annotation as JMS;

use Ilios\CoreBundle\Traits\DescribableEntity;
use Ilios\CoreBundle\Traits\IdentifiableEntity;
use Ilios\CoreBundle\Traits\NameableEntity;
use Ilios\CoreBundle\Traits\StringableIdEntity;

use Ilios\CoreBundle\Entity\CurriculumInventoryReportInterface;

/**
 * Class CurriculumInventoryAcademicLevel
 * @package Ilios\CoreBundle\Entity
 *
 * @ORM\Table(name="curriculum_inventory_academic_level",
 *   uniqueConstraints={
 *     @ORM\UniqueConstraint(name="report_id_level", columns={"report_id", "level"})
 *   },
 *   indexes={
 *     @ORM\Index(name="IDX_B4D3296D4BD2A4C0", columns={"report_id"})
 *   }
 * )
 * @ORM\Entity
 *
 * @JMS\ExclusionPolicy("all")
 */
class CurriculumInventoryAcademicLevel implements CurriculumInventoryAcademicLevelInterface
{
//    use IdentifiableEntity; //Implement on 3.1
    use NameableEntity;
    use DescribableEntity;
    use StringableIdEntity;

    /**
     * @deprecated To be removed in 3.1, replaced by ID by enabling trait.
     * @var int
     *
     * @ORM\Id
     * @ORM\Column(name="academic_level_id", type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     *
     * @JMS\Expose
     * @JMS\Type("integer")
     */
    protected $id;

    /**
    * @var string
    *
    * @ORM\Column(type="string", length=50)
    */
    protected $name;

    /**
    * @ORM\Column(name="description", type="text", nullable=true)
    * @var string
    */
    protected $description;

    /**
     * @var int
     *
     * @ORM\Column(name="level", type="integer")
     */
    protected $level;

    /**
     * @var CurriculumInventoryReportInterface
     *
     * @ORM\ManyToOne(targetEntity="CurriculumInventoryReport", inversedBy="curriculumInventoryAcademicLevels")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="report_id", referencedColumnName="report_id")
     * })
     *
     * @JMS\Expose
     * @JMS\Type("string")
     */
    protected $report;

    /**
    * @var ArrayCollection|CurriculumInventorySequenceBlockInterface[]
    *
    * @ORM\OneToMany(targetEntity="CurriculumInventorySequenceBlock", mappedBy="academicLevel")
    *
    * @JMS\Expose
    * @JMS\Type("array<string>")
    */
    protected $sequenceBlocks;

    /**
     * @param int $id
     */
    public function setId($id)
    {
        $this->academicLevelId = $id;
        $this->id = $id;
    }

    /**
     * @return int
     */
    public function getId()
    {
        return ($this->id === null) ? $this->academicLevelId : $this->id;
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
