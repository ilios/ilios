<?php

declare(strict_types=1);

namespace App\Entity;

use App\Traits\DescribableEntityInterface;

interface CurriculumInventorySequenceInterface extends
    DescribableEntityInterface,
    LoggableEntityInterface
{
    public function setReport(CurriculumInventoryReportInterface $report);
    public function getReport(): CurriculumInventoryReportInterface;
}
