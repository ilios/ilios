<?php

namespace Ilios\CoreBundle\Model;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;

use Ilios\CoreBundle\Traits\DescribableEntityInterface;
use Ilios\CoreBundle\Traits\IdentifiableEntityInterface;
use Ilios\CoreBundle\Traits\NameableEntityInterface;

/**
 * Interface CurriculumInventoryReportInterface
 */
interface CurriculumInventoryReportInterface extends
    IdentifiableEntityInterface,
    NameableEntityInterface,
    DescribableEntityInterface
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
     * @param string $name
     */
    public function setName($name);

    /**
     * @return string
     */
    public function getName();

    /**
     * @param string $description
     */
    public function setDescription($description);

    /**
     * @return string
     */
    public function getDescription();

    /**
     * @param \DateTime $startDate
     */
    public function setStartDate($startDate);

    /**
     * @return \DateTime
     */
    public function getStartDate();

    /**
     * @param \DateTime $endDate
     */
    public function setEndDate($endDate);

    /**
     * @return \DateTime
     */
    public function getEndDate();

    /**
     * @param Collection $exports
     */
    public function setExports(Collection $exports);

    /**
     * @param CurriculumInventoryExportInterface $export
     */
    public function addExport(CurriculumInventoryExportInterface $export);

    /**
     * @return ArrayCollection|CurriculumInventoryExportInterface[]
     */
    public function getExports();

    /**
     * @param Collection $sequences
     */
    public function setSequences(Collection $sequences);

    /**
     * @param CurriculumInventorySequenceInterface $sequence
     */
    public function addSequence(CurriculumInventorySequenceInterface $sequence);

    /**
     * @return ArrayCollection|CurriculumInventorySequenceInterface[]
     */
    public function getSequence();

    /**
     * @param ProgramInterface $program
     */
    public function setProgram(ProgramInterface $program);

    /**
     * @return ProgramInterface
     */
    public function getProgram();
}

