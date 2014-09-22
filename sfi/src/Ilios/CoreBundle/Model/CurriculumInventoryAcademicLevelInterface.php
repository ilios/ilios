<?php

namespace Ilios\CoreBundle\Model;

/**
 * Interface CurriculumInventoryAcademicLevelInterface
 */
interface CurriculumInventoryAcademicLevelInterface 
{
    public function getAcademicLevelId();

    public function setLevel($level);

    public function getLevel();

    public function setName($name);

    public function getName();

    public function setDescription($description);

    public function getDescription();

    public function setReport(\Ilios\CoreBundle\Model\CurriculumInventoryReport $report = null);

    public function getReport();
}

