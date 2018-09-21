<?php

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

    /**
     * @return int
     */
    public function getLevel();

    /**
     * @param CurriculumInventoryReportInterface $report
     */
    public function setReport(CurriculumInventoryReportInterface $report);

    /**
     * @return CurriculumInventoryReportInterface
     */
    public function getReport();
}
