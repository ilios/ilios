<?php

declare(strict_types=1);

namespace App\Entity;

use App\Traits\DescribableNullableEntityInterface;

interface CurriculumInventorySequenceInterface extends
    DescribableNullableEntityInterface,
    LoggableEntityInterface
{
    public function setReport(CurriculumInventoryReportInterface $report);
    public function getReport(): CurriculumInventoryReportInterface;
}
