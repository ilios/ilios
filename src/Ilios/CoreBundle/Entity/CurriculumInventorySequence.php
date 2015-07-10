<?php

namespace Ilios\CoreBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use JMS\Serializer\Annotation as JMS;
use Symfony\Component\Validator\Constraints as Assert;

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
    use IdentifiableEntity;
    use DescribableEntity;


    /**
     * @var int
     *
     * @ORM\Column(name="sequence_id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     *
     * @Assert\Type(type="integer")
     *
     * @JMS\Expose
     * @JMS\Type("integer")
     */
    protected $id;

    /**
     * @var CurriculumInventoryReportInterface
     *
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
     *
     * @Assert\Type(type="string")
     * @Assert\Length(
     *      min = 1,
     *      max = 65000
     * )
     *
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
