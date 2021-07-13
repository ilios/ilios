<?php

declare(strict_types=1);

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use App\Annotation as IS;
use Symfony\Component\Validator\Constraints as Assert;
use App\Entity\CurriculumInventoryReportInterface;
use App\Entity\UserInterface;
use App\Traits\IdentifiableEntity;
use App\Traits\StringableIdEntity;
use App\Repository\CurriculumInventoryExportRepository;

/**
 * Class CurriculumInventoryExport
 * @IS\Entity
 */
#[ORM\Table(name: 'curriculum_inventory_export')]
#[ORM\Index(columns: ['created_by'], name: 'fkey_curriculum_inventory_export_user_id')]
#[ORM\Entity(repositoryClass: CurriculumInventoryExportRepository::class)]
class CurriculumInventoryExport implements CurriculumInventoryExportInterface
{
    use IdentifiableEntity;
    use StringableIdEntity;

    /**
     * @var int
     * @Assert\Type(type="integer")
     * @IS\Expose
     * @IS\Type("integer")
     * @IS\ReadOnly
     */
    #[ORM\Column(name: 'export_id', type: 'integer')]
    #[ORM\Id]
    #[ORM\GeneratedValue(strategy: 'IDENTITY')]
    protected $id;

    /**
     * @var CurriculumInventoryReportInterface
     * @IS\Expose
     * @IS\Type("entity")
     */
    #[ORM\OneToOne(inversedBy: 'export', targetEntity: 'CurriculumInventoryReport')]
    #[ORM\JoinColumn(name: 'report_id', referencedColumnName: 'report_id', unique: true, nullable: false)]
    protected $report;

    /**
     * @var string
     * @Assert\NotBlank()
     * @Assert\Type(type="string")
     * @Assert\Length(
     *      min = 1,
     *      max = 16000000
     * )
     * @IS\Type("string")
     */
    #[ORM\Column(name: 'document', type: 'text')]
    protected $document;

    /**
     * @var UserInterface
     * @IS\Expose
     * @IS\Type("entity")
     */
    #[ORM\ManyToOne(targetEntity: 'User')]
    #[ORM\JoinColumn(name: 'created_by', referencedColumnName: 'user_id')]
    protected $createdBy;

    /**
     * @var \DateTime
     * @Assert\NotBlank()
     * @IS\Expose
     * @IS\ReadOnly
     * @IS\Type("dateTime")
     */
    #[ORM\Column(name: 'created_on', type: 'datetime')]
    protected $createdAt;

    public function __construct()
    {
        $this->createdAt = new \DateTime();
    }

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
