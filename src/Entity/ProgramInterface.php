<?php

declare(strict_types=1);

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use App\Traits\DirectorsEntityInterface;
use App\Traits\IdentifiableEntityInterface;
use App\Traits\PublishableEntityInterface;
use App\Traits\SchoolEntityInterface;
use App\Traits\TitledEntityInterface;
use App\Traits\StringableEntityInterface;
use App\Traits\ProgramYearsEntityInterface;

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
    DirectorsEntityInterface
{
    /**
     * @param string $shortTitle
     */
    public function setShortTitle($shortTitle);

    /**
     * @return string
     */
    public function getShortTitle(): string;

    /**
     * @param int $duration
     */
    public function setDuration($duration);

    /**
     * @return int
     */
    public function getDuration(): int;

    public function setCurriculumInventoryReports(Collection $reports);

    public function addCurriculumInventoryReport(CurriculumInventoryReportInterface $report);

    public function removeCurriculumInventoryReport(CurriculumInventoryReportInterface $report);


    /**
     * @return CurriculumInventoryReportInterface[]|ArrayCollection
     */
    public function getCurriculumInventoryReports(): Collection;
}
