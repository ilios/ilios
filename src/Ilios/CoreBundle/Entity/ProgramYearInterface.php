<?php

namespace Ilios\CoreBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;

use Ilios\CoreBundle\Traits\ArchivableEntityInterface;
use Ilios\CoreBundle\Traits\IdentifiableEntityInterface;
use Ilios\CoreBundle\Traits\LockableEntityInterface;
use Ilios\CoreBundle\Traits\ObjectivesEntityInterface;
use Ilios\CoreBundle\Traits\PublishableEntityInterface;
use Ilios\CoreBundle\Traits\StewardedEntityInterface;

/**
 * Interface ProgramYearInterface
 * @package Ilios\CoreBundle\Entity
 */
interface ProgramYearInterface extends
    IdentifiableEntityInterface,
    LockableEntityInterface,
    ArchivableEntityInterface,
    LoggableEntityInterface,
    StewardedEntityInterface,
    ObjectivesEntityInterface,
    PublishableEntityInterface
{
    /**
     * @param int $startYear
     */
    public function setStartYear($startYear);

    /**
     * @return int
     */
    public function getStartYear();

    /**
     * @param ProgramInterface $program
     */
    public function setProgram(ProgramInterface $program);

    /**
     * @return ProgramInterface
     */
    public function getProgram();

    /**
     * @param Collection $directors
     */
    public function setDirectors(Collection $directors);

    /**
     * @param UserInterface $director
     */
    public function addDirector(UserInterface $director);

    /**
     * @return ArrayCollection|UserInterface[]
     */
    public function getDirectors();

    /**
     * @param Collection $competencies
     */
    public function setCompetencies(Collection $competencies);

    /**
     * @param CompetencyInterface $competency
     */
    public function addCompetency(CompetencyInterface $competency);

    /**
     * @return ArrayCollection|CompetencyInterface[]
     */
    public function getCompetencies();

    /**
     * @param Collection $topics
     */
    public function setTopics(Collection $topics);

    /**
     * @param TopicInterface $topic
     */
    public function addTopic(TopicInterface $topic);

    /**
     * @return ArrayCollection|TopicInterface[]
     */
    public function getTopics();

    /**
     * @param CohortInterface
     */
    public function setCohort(CohortInterface $cohort);

    /**
     * @return CohortInterface
     */
    public function getCohort();

    /**
     * Gets the school that this program year belongs to.
     * @return SchoolInterface|null
     */
    public function getSchool();
}
