<?php

namespace Ilios\CoreBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Ilios\CoreBundle\Traits\DirectorsEntityInterface;
use Ilios\CoreBundle\Traits\IdentifiableEntityInterface;
use Ilios\CoreBundle\Traits\PublishableEntityInterface;
use Ilios\CoreBundle\Traits\SchoolEntityInterface;
use Ilios\CoreBundle\Traits\TitledEntityInterface;
use Ilios\CoreBundle\Traits\StringableEntityInterface;
use Ilios\CoreBundle\Traits\ProgramYearsEntityInterface;

/**
 * Interface ProgramInterface
 */
interface ProgramInterface extends
    IdentifiableEntityInterface,
    TitledEntityInterface,
    StringableEntityInterface,
    ProgramYearsEntityInterface,
    SchoolEntityInterface,
    LoggableEntityInterface,
    PublishableEntityInterface,
    DirectorsEntityInterface
{
    /**
     * @param string $shortTitle
     */
    public function setShortTitle($shortTitle);

    /**
     * @return string
     */
    public function getShortTitle();

    /**
     * @param int $duration
     */
    public function setDuration($duration);

    /**
     * @return int
     */
    public function getDuration();

    /**
     * @param Collection $reports
     */
    public function setCurriculumInventoryReports(Collection $reports);

    /**
     * @param CurriculumInventoryReportInterface $report
     */
    public function addCurriculumInventoryReport(CurriculumInventoryReportInterface $report);

    /**
     * @param CurriculumInventoryReportInterface $report
     */
    public function removeCurriculumInventoryReport(CurriculumInventoryReportInterface $report);


    /**
     * @return CurriculumInventoryReportInterface[]|ArrayCollection
     */
    public function getCurriculumInventoryReports();
}
