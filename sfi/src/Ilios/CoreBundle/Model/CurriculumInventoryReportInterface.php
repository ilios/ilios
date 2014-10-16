<?php

namespace Ilios\CoreBundle\Model;

use Ilios\CoreBundle\Traits\DescribableTraitInterface;
use Ilios\CoreBundle\Traits\IdentifiableTraitIntertface;
use Ilios\CoreBundle\Traits\NameableTraitInterface;

/**
 * Interface CurriculumInventoryReportInterface
 */
interface CurriculumInventoryReportInterface extends
    IdentifiableTraitIntertface,
    NameableTraitInterface,
    DescribableTraitInterface
{
    /**
     * @param integer $year
     */
    public function setYear($year);

    /**
     * @return integer
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
     * @param CurriculumInventoryExportInterface $export
     */
    public function setExport(CurriculumInventoryExportInterface $export);

    /**
     * @return CurriculumInventoryExportInterface
     */
    public function getExport();

    /**
     * @param CurriculumInventorySequenceInterface $sequence
     */
    public function setSequence(CurriculumInventorySequenceInterface $sequence);

    /**
     * @return CurriculumInventorySequenceInterface
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

