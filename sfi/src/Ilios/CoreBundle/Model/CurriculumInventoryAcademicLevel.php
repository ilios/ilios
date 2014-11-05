<?php

namespace Ilios\CoreBundle\Model;

use Doctrine\ORM\Mapping as ORM;

use Ilios\CoreBundle\Traits\DescribableEntity;
use Ilios\CoreBundle\Traits\IdentifiableEntity;
use Ilios\CoreBundle\Traits\NameableEntity;

use Ilios\CoreBundle\Model\CurriculumInventoryReportInterface;

/**
 * Class CurriculumInventoryAcademicLevel
 * @package Ilios\CoreBundle\Model
 *
 * @ORM\Entity
 * @ORM\Table(name="competency")
 */
class CurriculumInventoryAcademicLevel implements CurriculumInventoryAcademicLevelInterface
{
//    use IdentifiableEntity; //Implement on 3.1
    use NameableEntity;
    use DescribableEntity;

    /**
     * @deprecated To be removed in 3.1, replaced by ID by enabling trait.
     * @var int
     *
     * @ORM\Id
     * @ORM\Column(type="integer", length=10)
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $academicLevelId;

    /**
     * @var int
     */
    protected $id;

    /**
     * @var int
     *
     * @ORM\Column(type="integer", length=2)
     */
    protected $level;

    /**
     * @var CurriculumInventoryReportInterface
     *
     * @ORM\ManyToOne(targetEntity="Report", inversedBy="curriculumInventoryAcademicLevels")
     * @ORM\JoinColumn(name="report_id", referencedColumnName="report_id")
     */
    protected $report;

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
