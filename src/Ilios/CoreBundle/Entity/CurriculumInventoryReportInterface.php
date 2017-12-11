<?php

namespace Ilios\CoreBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Ilios\CoreBundle\Traits\AdministratorsEntityInterface;
use Ilios\CoreBundle\Traits\DescribableEntityInterface;
use Ilios\CoreBundle\Traits\IdentifiableEntityInterface;
use Ilios\CoreBundle\Traits\NameableEntityInterface;
use Ilios\CoreBundle\Traits\SequenceBlocksEntityInterface;
use Ilios\CoreBundle\Traits\StringableEntityInterface;

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
    public function getYear();

    /**
     * @param \DateTime $startDate
     */
    public function setStartDate($startDate = null);

    /**
     * @return \DateTime
     */
    public function getStartDate();

    /**
     * @param \DateTime $endDate
     */
    public function setEndDate($endDate = null);

    /**
     * @return \DateTime
     */
    public function getEndDate();

    /**
     * @param CurriculumInventoryExportInterface $export
     */
    public function setExport(CurriculumInventoryExportInterface $export = null);

    /**
     * @return CurriculumInventoryExportInterface
     */
    public function getExport();

    /**
     * @param CurriculumInventorySequenceInterface $sequence
     */
    public function setSequence(CurriculumInventorySequenceInterface $sequence = null);

    /**
     * @return CurriculumInventorySequenceInterface
     */
    public function getSequence();

    /**
     * @param ProgramInterface $program
     */
    public function setProgram(ProgramInterface $program = null);

    /**
     * @return ProgramInterface
     */
    public function getProgram();
    /**
     * @param Collection $academicLevels
     */
    public function setAcademicLevels(Collection $academicLevels = null);

    /**
     * @param CurriculumInventoryAcademicLevelInterface $academicLevel
     */
    public function addAcademicLevel(CurriculumInventoryAcademicLevelInterface $academicLevel);

    /**
     * @param CurriculumInventoryAcademicLevelInterface $academicLevel
     */
    public function removeAcademicLevel(CurriculumInventoryAcademicLevelInterface $academicLevel);

    /**
     * @return ArrayCollection|CurriculumInventoryAcademicLevelInterface[]
     */
    public function getAcademicLevels();

    /**
     * Gets the school that the program being reported on belongs to.
     * @return SchoolInterface|null
     */
    public function getSchool();

    /**
     * @return string
     */
    public function getToken();

    /**
     * Generate a random token for use in downloading
     */
    public function generateToken();
}
