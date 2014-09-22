<?php

namespace Ilios\CoreBundle\Model;



/**
 * Interface ProgramYearInterface
 */
interface ProgramYearInterface 
{
    public function getProgramYearId();

    public function setStartYear($startYear);

    public function getStartYear();

    public function setDeleted($deleted);

    public function getDeleted();

    public function setLocked($locked);

    public function getLocked();

    public function setArchived($archived);

    public function getArchived();

    public function setPublishedAsTbd($publishedAsTbd);

    public function getPublishedAsTbd();

    public function setProgram(\Ilios\CoreBundle\Model\Program $program = null);

    public function getProgram();

    public function addDirector(\Ilios\CoreBundle\Model\User $directors);

    public function removeDirector(\Ilios\CoreBundle\Model\User $directors);

    public function getDirectors();

    public function addCompetency(\Ilios\CoreBundle\Model\Competency $competencies);

    public function removeCompetency(\Ilios\CoreBundle\Model\Competency $competencies);

    public function getCompetencies();

    public function addDiscipline(\Ilios\CoreBundle\Model\Discipline $disciplines);

    public function removeDiscipline(\Ilios\CoreBundle\Model\Discipline $disciplines);

    public function getDisciplines();

    public function addObjective(\Ilios\CoreBundle\Model\Objective $objectives);

    public function removeObjective(\Ilios\CoreBundle\Model\Objective $objectives);

    public function getObjectives();

    public function setPublishEvent(\Ilios\CoreBundle\Model\PublishEvent $publishEvent = null);

    public function getPublishEvent();
}
