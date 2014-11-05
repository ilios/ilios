<?php

namespace Ilios\CoreBundle\Model;

use Doctrine\ORM\Mapping as ORM;

use Ilios\CoreBundle\Model\CurriculumInventoryReportInterface;
use Ilios\CoreBundle\Model\UserInterface;

/**
 * Class CurriculumInventoryExport
 * @package Ilios\CoreBundle\Model
 *
 * @ORM\Entity
 * @ORM\Table(name="curriculum_inventory_export")
 */
class CurriculumInventoryExport implements CurriculumInventoryExportInterface
{
    /**
     * @var CurriculumInventoryReportInterface
     *
     * @ORM\Id
     * @ORM\OneToOne(targetEntity="CurriculumInventoryReport")
     * @ORM\JoinColumn(name="report_id", referencedColumnName="report_id")
     */
    protected $report;

    /**
     * @var string
     *
     * @ORM\Column(type="text")
     */
    protected $document;

    /**
     * @var UserInterface
     *
     * @ORM\Column(name="created_by", type="int")
     */
    protected $createdBy;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="created_on", type="datetime")
     */
    protected $createdAt;

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
     * @param string $document
     */
    public function setDocument($document)
    {
        $this->document = $document;
    }

    /**
     * @return string
     */
    public function getDocument()
    {
        return $this->document;
    }

    /**
     * @param UserInterface $createdBy
     */
    public function setCreatedBy(UserInterface $createdBy)
    {
        $this->createdBy = $createdBy;
    }

    /**
     * @return UserInterface
     */
    public function getCreatedBy()
    {
        return $this->createdBy;
    }

    /**
     * @param \DateTime $createdAt
     */
    public function setCreatedAt(\DateTime $createdAt)
    {
        $this->createdAt = $createdAt;
    }

    /**
     * @return \DateTime
     */
    public function getCreatedAt()
    {
        return $this->createdAt;
    }
}
