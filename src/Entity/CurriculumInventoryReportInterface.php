<?php

declare(strict_types=1);

namespace App\Entity;

use DateTime;
use Doctrine\Common\Collections\Collection;
use App\Traits\AdministratorsEntityInterface;
use App\Traits\DescribableNullableEntityInterface;
use App\Traits\IdentifiableEntityInterface;
use App\Traits\SequenceBlocksEntityInterface;
use App\Traits\StringableEntityInterface;

interface CurriculumInventoryReportInterface extends
    IdentifiableEntityInterface,
    DescribableNullableEntityInterface,
    StringableEntityInterface,
    LoggableEntityInterface,
    SequenceBlocksEntityInterface,
    AdministratorsEntityInterface
{
    public function setName(?string $name);
    public function getName(): ?string;

    public function setYear(int $year);
    public function getYear(): int;

    public function setStartDate(DateTime $startDate);
    public function getStartDate(): DateTime;

    public function setEndDate(DateTime $endDate);
    public function getEndDate(): DateTime;

    public function setExport(CurriculumInventoryExportInterface $export = null);
    public function getExport(): ?CurriculumInventoryExportInterface;

    public function setSequence(CurriculumInventorySequenceInterface $sequence = null);
    public function getSequence(): CurriculumInventorySequenceInterface;

    public function setProgram(ProgramInterface $program = null);
    public function getProgram(): ?ProgramInterface;

    public function setAcademicLevels(Collection $academicLevels = null);
    public function addAcademicLevel(CurriculumInventoryAcademicLevelInterface $academicLevel);
    public function removeAcademicLevel(CurriculumInventoryAcademicLevelInterface $academicLevel);
    public function getAcademicLevels(): Collection;

    /**
     * Gets the school that the program being reported on belongs to.
     */
    public function getSchool(): ?SchoolInterface;

    public function getToken(): string;

    /**
     * Generate a random token for use in downloading
     */
    public function generateToken();
}
