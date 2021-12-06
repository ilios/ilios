<?php

declare(strict_types=1);

namespace App\Entity;

use App\Traits\BlameableEntityInterface;
use App\Traits\IdentifiableEntityInterface;
use App\Entity\CurriculumInventoryReportInterface;

/**
 * Interface CurriculumInventoryExportInterface
 */
interface CurriculumInventoryExportInterface extends
    IdentifiableEntityInterface,
    LoggableEntityInterface
{
    /**
     * @param string $document
     */
    public function setDocument($document);

    public function getDocument(): string;

    public function setReport(CurriculumInventoryReportInterface $report);

    public function getReport(): CurriculumInventoryReportInterface;

    public function setCreatedBy(UserInterface $createdBy);

    public function getCreatedBy(): UserInterface;

    public function getCreatedAt(): \DateTime;
}
