<?php

declare(strict_types=1);

namespace App\Entity;

use App\Entity\DescribableNullableEntityInterface;

interface CurriculumInventorySequenceInterface extends
    DescribableNullableEntityInterface,
    LoggableEntityInterface
{
    public function setReport(CurriculumInventoryReportInterface $report): void;
    public function getReport(): CurriculumInventoryReportInterface;
}
