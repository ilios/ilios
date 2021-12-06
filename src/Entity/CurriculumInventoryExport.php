<?php

declare(strict_types=1);

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use App\Attribute as IA;
use Symfony\Component\Validator\Constraints as Assert;
use App\Entity\CurriculumInventoryReportInterface;
use App\Entity\UserInterface;
use App\Traits\IdentifiableEntity;
use App\Traits\StringableIdEntity;
use App\Repository\CurriculumInventoryExportRepository;

/**
 * Class CurriculumInventoryExport
 */
#[ORM\Table(name: 'curriculum_inventory_export')]
#[ORM\Index(columns: ['created_by'], name: 'fkey_curriculum_inventory_export_user_id')]
#[ORM\Entity(repositoryClass: CurriculumInventoryExportRepository::class)]
#[IA\Entity]
class CurriculumInventoryExport implements CurriculumInventoryExportInterface
{
    use IdentifiableEntity;
    use StringableIdEntity;

    /**
     * @var int
     * @Assert\Type(type="integer")
     */
    #[ORM\Column(name: 'export_id', type: 'integer')]
    #[ORM\Id]
    #[ORM\GeneratedValue(strategy: 'IDENTITY')]
    #[IA\Expose]
    #[IA\Type('integer')]
    #[IA\ReadOnly]
    protected $id;

    /**
     * @var CurriculumInventoryReportInterface
     */
    #[ORM\OneToOne(inversedBy: 'export', targetEntity: 'CurriculumInventoryReport')]
    #[ORM\JoinColumn(name: 'report_id', referencedColumnName: 'report_id', unique: true, nullable: false)]
    #[IA\Expose]
    #[IA\Type('entity')]
    protected $report;

    /**
     * @var string
     * @Assert\NotBlank()
     * @Assert\Type(type="string")
     * @Assert\Length(
     *      min = 1,
     *      max = 16000000
     * )
     */
    #[ORM\Column(name: 'document', type: 'text')]
    #[IA\Type('string')]
    protected $document;

    /**
     * @var UserInterface
     */
    #[ORM\ManyToOne(targetEntity: 'User')]
    #[ORM\JoinColumn(name: 'created_by', referencedColumnName: 'user_id')]
    #[IA\Expose]
    #[IA\Type('entity')]
    protected $createdBy;

    /**
     * @var \DateTime
     * @Assert\NotBlank()
     */
    #[ORM\Column(name: 'created_on', type: 'datetime')]
    #[IA\Expose]
    #[IA\ReadOnly]
    #[IA\Type('dateTime')]
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
    public function getReport(): CurriculumInventoryReportInterface
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
    public function getDocument(): string
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
    public function getCreatedBy(): UserInterface
    {
        return $this->createdBy;
    }

    /**
     * @return \DateTime
     */
    public function getCreatedAt(): \DateTime
    {
        return $this->createdAt;
    }
}
