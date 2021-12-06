<?php

declare(strict_types=1);

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use App\Traits\AdministratorsEntityInterface;
use App\Traits\DescribableEntityInterface;
use App\Traits\IdentifiableEntityInterface;
use App\Traits\NameableEntityInterface;
use App\Traits\SequenceBlocksEntityInterface;
use App\Traits\StringableEntityInterface;

/**
 * Interface CurriculumInventoryReportInterface
 */
interface CurriculumInventoryReportInterface extends
    IdentifiableEntityInterface,
    NameableEntityInterface,
    DescribableEntityInterface,
    StringableEntityInterface,
    LoggableEntityInterface,
    SequenceBlocksEntityInterface,
    AdministratorsEntityInterface
{
    /**
     * @param int $year
     */
    public function setYear($year);

    /**
     * @return int
     */
    public function getYear(): int;

    /**
     * @param \DateTime $startDate
     */
    public function setStartDate($startDate = null);

    /**
     * @return \DateTime
     */
    public function getStartDate(): \DateTime;

    /**
     * @param \DateTime $endDate
     */
    public function setEndDate($endDate = null);

    /**
     * @return \DateTime
     */
    public function getEndDate(): \DateTime;

    public function setExport(CurriculumInventoryExportInterface $export = null);

    /**
     * @return CurriculumInventoryExportInterface
     */
    public function getExport(): CurriculumInventoryExportInterface;

    public function setSequence(CurriculumInventorySequenceInterface $sequence = null);

    /**
     * @return CurriculumInventorySequenceInterface
     */
    public function getSequence(): CurriculumInventorySequenceInterface;

    public function setProgram(ProgramInterface $program = null);

    /**
     * @return ProgramInterface
     */
    public function getProgram(): ProgramInterface;
    public function setAcademicLevels(Collection $academicLevels = null);

    public function addAcademicLevel(CurriculumInventoryAcademicLevelInterface $academicLevel);

    public function removeAcademicLevel(CurriculumInventoryAcademicLevelInterface $academicLevel);

    /**
     * @return ArrayCollection|CurriculumInventoryAcademicLevelInterface[]
     */
    public function getAcademicLevels(): Collection;

    /**
     * Gets the school that the program being reported on belongs to.
     * @return SchoolInterface|null
     */
    public function getSchool(): ?SchoolInterface;

    /**
     * @return string
     */
    public function getToken(): string;

    /**
     * Generate a random token for use in downloading
     */
    public function generateToken();
}
