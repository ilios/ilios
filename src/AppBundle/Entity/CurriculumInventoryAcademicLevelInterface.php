<?php

namespace AppBundle\Entity;

use AppBundle\Traits\DescribableEntityInterface;
use AppBundle\Traits\IdentifiableEntityInterface;
use AppBundle\Traits\NameableEntityInterface;
use Doctrine\Common\Collections\Collection;
use AppBundle\Traits\SequenceBlocksEntityInterface;

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
