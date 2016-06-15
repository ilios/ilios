<?php

namespace Ilios\CoreBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use JMS\Serializer\Annotation as JMS;
use Symfony\Component\Validator\Constraints as Assert;

use Ilios\CoreBundle\Entity\CurriculumInventoryReportInterface;
use Ilios\CoreBundle\Entity\UserInterface;

use Ilios\CoreBundle\Traits\IdentifiableEntity;
use Ilios\CoreBundle\Traits\StringableIdEntity;

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
    use IdentifiableEntity;
    use StringableIdEntity;

    /**
     * @var int
     *
     * @ORM\Column(name="export_id", type="integer")
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
     * @ORM\OneToOne(targetEntity="CurriculumInventoryReport", inversedBy="export")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="report_id", referencedColumnName="report_id", unique=true, nullable=false)
     * })
     *
     * @JMS\Expose
     * @JMS\Type("string")
     * @JMS\SerializedName("report")
     */
    protected $report;

    /**
     * @var string
     *
     * @ORM\Column(name="document", type="text")
     *
     * @Assert\NotBlank()
     * @Assert\Type(type="string")
     * @Assert\Length(
     *      min = 1,
     *      max = 16000000
     * )
     * @JMS\Type("string")
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
     * @JMS\SerializedName("createdBy")
     */
    protected $createdBy;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="created_on", type="datetime")
     *
     * @Assert\NotBlank()
     * @JMS\Expose
     * @JMS\Type("DateTime<'c'>")
     * @JMS\SerializedName("createdAt")
     */
    protected $createdAt;
    
    /**
     * Constructor
     */
    public function __construct()
    {
        $this->createdAt = new \DateTime();
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
     * @return \DateTime
     */
    public function getCreatedAt()
    {
        return $this->createdAt;
    }
}
