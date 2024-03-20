<?php

declare(strict_types=1);

namespace App\Entity;

use DateTime;
use Doctrine\Common\Collections\Collection;
use App\Traits\AdministratorsEntityInterface;
use App\Traits\DescribableNullableEntityInterface;
use App\Traits\IdentifiableEntityInterface;
use App\Traits\SequenceBlocksEntityInterface;

interface CurriculumInventoryReportInterface extends
    IdentifiableEntityInterface,
    DescribableNullableEntityInterface,
    LoggableEntityInterface,
    SequenceBlocksEntityInterface,
    AdministratorsEntityInterface
{
    public function setName(?string $name): void;
    public function getName(): ?string;

    public function setYear(int $year): void;
    public function getYear(): int;

    public function setStartDate(DateTime $startDate): void;
    public function getStartDate(): DateTime;

    public function setEndDate(DateTime $endDate): void;
    public function getEndDate(): DateTime;

    public function setExport(?CurriculumInventoryExportInterface $export = null): void;
    public function getExport(): ?CurriculumInventoryExportInterface;

    public function setSequence(?CurriculumInventorySequenceInterface $sequence = null): void;
    public function getSequence(): ?CurriculumInventorySequenceInterface;

    public function setProgram(?ProgramInterface $program = null): void;
    public function getProgram(): ?ProgramInterface;

    public function setAcademicLevels(?Collection $academicLevels = null): void;
    public function addAcademicLevel(CurriculumInventoryAcademicLevelInterface $academicLevel): void;
    public function removeAcademicLevel(CurriculumInventoryAcademicLevelInterface $academicLevel): void;
    public function getAcademicLevels(): Collection;

    /**
     * Gets the school that the program being reported on belongs to.
     */
    public function getSchool(): ?SchoolInterface;

    public function getToken(): string;

    /**
     * Generate a random token for use in downloading
     */
    public function generateToken(): void;
}
