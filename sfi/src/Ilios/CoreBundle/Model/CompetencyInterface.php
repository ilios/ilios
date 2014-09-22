<?php

namespace Ilios\CoreBundle\Model;



/**
 * Interface CompetencyInterface
 */
interface CompetencyInterface 
{
    public function getCompetencyId();

    public function setTitle($title);

    public function getTitle();

    public function setOwningSchool(\Ilios\CoreBundle\Model\School $school = null);

    public function getOwningSchool();

    public function setParentCompetency(\Ilios\CoreBundle\Model\Competency $parentCompetency = null);

    public function getParentCompetency();

    public function addPcrs(\Ilios\CoreBundle\Model\AamcPcrs $pcrses);

    public function removePcrs(\Ilios\CoreBundle\Model\AamcPcrs $pcrses);

    public function getPcrses();

    public function addProgramYear(\Ilios\CoreBundle\Model\ProgramYear $programYears);

    public function removeProgramYear(\Ilios\CoreBundle\Model\ProgramYear $programYears);

    public function getProgramYears();
}
