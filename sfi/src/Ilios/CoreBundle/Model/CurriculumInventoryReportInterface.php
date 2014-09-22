<?php

namespace Ilios\CoreBundle\Model;

/**
 * Interface CurriculumInventoryReportInterface
 */
interface CurriculumInventoryReportInterface 
{
    public function getReportId();

    public function setYear($year);

    public function getYear();

    public function setName($name);

    public function getName();

    public function setDescription($description);

    public function getDescription();

    public function setStartDate($startDate);

    public function getStartDate();

    public function setEndDate($endDate);

    public function getEndDate();

    public function setExport(\Ilios\CoreBundle\Model\CurriculumInventoryExport $export = null);

    public function getExport();

    public function setSequence(\Ilios\CoreBundle\Model\CurriculumInventorySequence $sequence = null);

    public function getSequence();

    public function setProgram(\Ilios\CoreBundle\Model\Program $program = null);

    public function getProgram();
}

