<?php

namespace Ilios\CoreBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use JMS\Serializer\Annotation as JMS;

use Ilios\CoreBundle\Entity\CurriculumInventoryReportInterface;
use Ilios\CoreBundle\Entity\UserInterface;

/**
 * Class CurriculumInventoryExport
 * @package Ilios\CoreBundle\Entity
 *
 * @ORM\Table(name="curriculum_inventory_export",
 *   indexes={
 *     @ORM\Index(name="fkey_curriculum_inventory_export_user_id", columns={"created_by"})
 *   }
 * )
 * @ORM\Entity
 * @JMS\ExclusionPolicy("all")
 */
class CurriculumInventoryExport implements CurriculumInventoryExportInterface
{
    /**
     * @var CurriculumInventoryReportInterface
     *
     * @ORM\Id
     * @ORM\OneToOne(targetEntity="CurriculumInventoryReport", inversedBy="export")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="report_id", referencedColumnName="report_id", unique=true)
     * })
     *
     * @JMS\Expose
     * @JMS\Type("string")
     */
    protected $report;

    /**
     * @var string
     *
     * @ORM\Column(name="document", type="text")
     */
    protected $document;

    /**
     * @var UserInterface
     *
     * @ORM\ManyToOne(targetEntity="User")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="created_by", referencedColumnName="user_id")
     * })
     *
     * @JMS\Expose
     * @JMS\Type("string")
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

    /**
     * @return string
     */
    public function __toString()
    {
        return (string) $this->report;
    }
}
