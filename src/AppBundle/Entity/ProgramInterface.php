<?php

namespace AppBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use AppBundle\Traits\DirectorsEntityInterface;
use AppBundle\Traits\IdentifiableEntityInterface;
use AppBundle\Traits\PublishableEntityInterface;
use AppBundle\Traits\SchoolEntityInterface;
use AppBundle\Traits\TitledEntityInterface;
use AppBundle\Traits\StringableEntityInterface;
use AppBundle\Traits\ProgramYearsEntityInterface;

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
