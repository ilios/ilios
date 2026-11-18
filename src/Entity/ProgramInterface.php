<?php

declare(strict_types=1);

namespace App\Entity;

use App\Entity\DirectorsEntityInterface;
use App\Entity\IdentifiableEntityInterface;
use App\Entity\ProgramYearsEntityInterface;
use App\Entity\SchoolEntityInterface;
use App\Entity\TitledEntityInterface;
use Doctrine\Common\Collections\Collection;

interface ProgramInterface extends
    IdentifiableEntityInterface,
    TitledEntityInterface,
    ProgramYearsEntityInterface,
    SchoolEntityInterface,
    LoggableEntityInterface,
    DirectorsEntityInterface
{
    public function setShortTitle(?string $shortTitle): void;
    public function getShortTitle(): ?string;

    public function setDuration(int $duration): void;
    public function getDuration(): int;

    public function setCurriculumInventoryReports(Collection $reports): void;
    public function addCurriculumInventoryReport(CurriculumInventoryReportInterface $report): void;
    public function removeCurriculumInventoryReport(CurriculumInventoryReportInterface $report): void;
    public function getCurriculumInventoryReports(): Collection;
}
