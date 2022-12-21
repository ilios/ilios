<?php

declare(strict_types=1);

namespace App\Entity;

use Doctrine\Common\Collections\Collection;
use App\Traits\DirectorsEntityInterface;
use App\Traits\IdentifiableEntityInterface;
use App\Traits\SchoolEntityInterface;
use App\Traits\TitledEntityInterface;
use App\Traits\StringableEntityInterface;
use App\Traits\ProgramYearsEntityInterface;

interface ProgramInterface extends
    IdentifiableEntityInterface,
    TitledEntityInterface,
    StringableEntityInterface,
    ProgramYearsEntityInterface,
    SchoolEntityInterface,
    LoggableEntityInterface,
    DirectorsEntityInterface
{
    public function setShortTitle(?string $shortTitle);
    public function getShortTitle(): ?string;

    public function setDuration(int $duration);
    public function getDuration(): int;

    public function setCurriculumInventoryReports(Collection $reports);
    public function addCurriculumInventoryReport(CurriculumInventoryReportInterface $report);
    public function removeCurriculumInventoryReport(CurriculumInventoryReportInterface $report);
    public function getCurriculumInventoryReports(): Collection;
}
