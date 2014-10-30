<?php

namespace Ilios\CoreBundle\Model;

use Doctrine\ORM\Mapping as ORM;

use Ilios\CoreBundle\Traits\IdentifiableEntity;

use Ilios\CoreBundle\Model\CurriculumInventoryReportInterface;
use Symfony\Component\Security\Core\User\UserInterface;

/**
 * @Entity
 *
 * Class CurriculumInventoryExport
 * @package Ilios\CoreBundle\Model
 */
class CurriculumInventoryExport implements CurriculumInventoryExportInterface
{
    /**
     * @ORM\Id
     * @ORM\OneToOne(targetEntity="CurriculumInventoryReport")
     *
     * @var CurriculumInventoryReportInterface
     */
    protected $report;

    /**
     * @ORM\Column(type="text")
     *
     * @var string
     */
    protected $document;

    /**
     * @ORM\Column(name="created_by", type="integer")
     *
     * @var UserInterface
     */
    protected $createdBy;

    /**
     * @ORM\Column(name="created_on", type="datetime")
     *
     * @var \DateTime
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
