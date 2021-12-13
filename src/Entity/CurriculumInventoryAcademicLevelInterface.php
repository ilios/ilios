<?php

declare(strict_types=1);

namespace App\Entity;

use App\Traits\DescribableEntityInterface;
use App\Traits\IdentifiableEntityInterface;
use App\Traits\NameableEntityInterface;
use Doctrine\Common\Collections\Collection;
use App\Traits\SequenceBlocksEntityInterface;

/**
 * Interface CurriculumInventoryAcademicLevelInterface
 */
interface CurriculumInventoryAcademicLevelInterface extends
    IdentifiableEntityInterface,
    NameableEntityInterface,
    DescribableEntityInterface,
    LoggableEntityInterface,
    SequenceBlocksEntityInterface
{
    /**
     * @param int $level
     */
    public function setLevel($level);

    public function getLevel(): int;

    public function setReport(CurriculumInventoryReportInterface $report);

    public function getReport(): CurriculumInventoryReportInterface;
}
