<?php

declare(strict_types=1);

namespace App\Entity\DTO;

use App\Annotation as IS;
use DateTime;

/**
 * Class CurriculumInventoryExport
 * @IS\DTO("curriculumInventoryExports")
 */
class CurriculumInventoryExportDTO
{
    /**
     * @IS\Id
     * @IS\Expose
     * @IS\Type("integer")
     */
    public int $id;

    /**
     * @IS\Expose
     * @IS\Related("curriculumInventoryReports")
     * @IS\Type("integer")
     */
    public int $report;

    /**
     * @IS\Type("string")
     * Not exposed.
     */
    public string $document;

    /**
     * @IS\Expose
     * @IS\Type("string")
     */
    public int $createdBy;

    /**
     * @IS\Expose
     * @IS\Type("dateTime")
     */
    public DateTime $createdAt;

    public function __construct(int $id, string $document, DateTime $createdAt)
    {
        $this->id = $id;
        $this->document = $document;
        $this->createdAt = $createdAt;
    }
}
