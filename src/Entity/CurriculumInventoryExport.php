<?php

declare(strict_types=1);

namespace App\Entity;

use DateTime;
use Doctrine\ORM\Mapping as ORM;
use App\Attributes as IA;
use Symfony\Component\Validator\Constraints as Assert;
use App\Traits\IdentifiableEntity;
use App\Traits\StringableIdEntity;
use App\Repository\CurriculumInventoryExportRepository;

#[ORM\Table(name: 'curriculum_inventory_export')]
#[ORM\Index(columns: ['created_by'], name: 'fkey_curriculum_inventory_export_user_id')]
#[ORM\Entity(repositoryClass: CurriculumInventoryExportRepository::class)]
#[IA\Entity]
class CurriculumInventoryExport implements CurriculumInventoryExportInterface
{
    use IdentifiableEntity;
    use StringableIdEntity;

    #[ORM\Column(name: 'export_id', type: 'integer')]
    #[ORM\Id]
    #[ORM\GeneratedValue(strategy: 'IDENTITY')]
    #[IA\Expose]
    #[IA\Type('integer')]
    #[IA\OnlyReadable]
    #[Assert\Type(type: 'integer')]
    protected int $id;

    #[ORM\OneToOne(inversedBy: 'export', targetEntity: 'CurriculumInventoryReport')]
    #[ORM\JoinColumn(name: 'report_id', referencedColumnName: 'report_id', unique: true, nullable: false)]
    #[IA\Expose]
    #[IA\Type('entity')]
    protected CurriculumInventoryReportInterface $report;

    #[ORM\Column(name: 'document', type: 'text')]
    #[IA\Type('string')]
    #[Assert\NotBlank]
    #[Assert\Type(type: 'string')]
    #[Assert\Length(min: 1, max: 16000000)]
    protected string $document;

    #[ORM\ManyToOne(targetEntity: 'User')]
    #[ORM\JoinColumn(name: 'created_by', referencedColumnName: 'user_id')]
    #[IA\Expose]
    #[IA\Type('entity')]
    protected UserInterface $createdBy;

    #[ORM\Column(name: 'created_on', type: 'datetime')]
    #[IA\Expose]
    #[IA\OnlyReadable]
    #[IA\Type('dateTime')]
    #[Assert\NotBlank]
    protected DateTime $createdAt;

    public function __construct()
    {
        $this->createdAt = new DateTime();
    }

    public function setReport(CurriculumInventoryReportInterface $report)
    {
        $this->report = $report;
    }

    public function getReport(): CurriculumInventoryReportInterface
    {
        return $this->report;
    }

    public function setDocument(string $document)
    {
        $this->document = $document;
    }

    public function getDocument(): string
    {
        return $this->document;
    }

    public function setCreatedBy(UserInterface $createdBy)
    {
        $this->createdBy = $createdBy;
    }

    public function getCreatedBy(): UserInterface
    {
        return $this->createdBy;
    }

    public function getCreatedAt(): DateTime
    {
        return $this->createdAt;
    }
}
