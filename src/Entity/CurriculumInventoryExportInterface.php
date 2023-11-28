<?php

declare(strict_types=1);

namespace App\Entity;

use App\Traits\CreatedAtEntityInterface;
use App\Traits\IdentifiableEntityInterface;

interface CurriculumInventoryExportInterface extends
    CreatedAtEntityInterface,
    IdentifiableEntityInterface,
    LoggableEntityInterface
{
    public function setDocument(string $document): void;
    public function getDocument(): string;

    public function setReport(CurriculumInventoryReportInterface $report): void;
    public function getReport(): CurriculumInventoryReportInterface;

    public function setCreatedBy(UserInterface $createdBy): void;
    public function getCreatedBy(): UserInterface;
}
