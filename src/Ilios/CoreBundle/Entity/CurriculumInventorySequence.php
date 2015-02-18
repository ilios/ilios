<?php

namespace Ilios\CoreBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use JMS\Serializer\Annotation as JMS;

use Ilios\CoreBundle\Traits\DescribableEntity;
use Ilios\CoreBundle\Traits\IdentifiableEntity;

/**
 * Class CurriculumInventorySequence
 * @package Ilios\CoreBundle\Entity
 *
 * @ORM\Table(name="curriculum_inventory_sequence")
 * @ORM\Entity
 *
 * @JMS\ExclusionPolicy("all")
 */
class CurriculumInventorySequence implements CurriculumInventorySequenceInterface
{
//    use IdentifiableEntity;
    use DescribableEntity;

    /**
     * @var CurriculumInventoryReportInterface
     *
     * @ORM\Id
     * @ORM\OneToOne(targetEntity="CurriculumInventoryReport", inversedBy="sequence")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="report_id", referencedColumnName="report_id", unique=true)
     * })
     *
     * @JMS\Expose
     * @JMS\Type("string")
     */
    protected $report;

    /**
    * @ORM\Column(name="description", type="text", nullable=true)
    * @var string
    */
    protected $description;

    /**
     * @param int $id
     */
    public function setId($id)
    {
        throw new \LogicException('This is not implemented yet.');
    }

    /**
     * @return int
     */
    public function getId()
    {
        return $this->report->getId();
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

    /**
     * @return string
     */
    public function __toString()
    {
        return (string) $this->report;
    }
}
