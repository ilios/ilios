<?php

namespace Ilios\CoreBundle\Model;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;

use Ilios\CoreBundle\Traits\IdentifiableTraitIntertface;
use Ilios\CoreBundle\Traits\TitleTraitInterface;

/**
 * Interface DisciplineInterface
 */
interface DisciplineInterface extends IdentifiableTraitIntertface, TitleTraitInterface
{
    /**
     * @param SchoolInterface $school
     */
    public function setOwningSchool(SchoolInterface $school);

    /**
     * @return SchoolInterface
     */
    public function getOwningSchool();

    /**
     * @param CourseInterface $course
     */
    public function addCourse(CourseInterface $course);

    /**
     * @return ArrayCollection|CourseInterface[]
     */
    public function getCourses();

    /**
     * @param Collection $programYears
     */
    public function setProgramYears(Collection $programYears);

    /**
     * @param ProgramYearInterface $programYear
     */
    public function addProgramYear(ProgramYearInterface $programYear);

    /**
     * @return ArrayCollection|ProgramYearInterface[]
     */
    public function getProgramYears();

    /**
     * @param Collection $sessions
     */
    public function setSessions(Collection $sessions);

    /**
     * @param SessionInterface $session
     */
    public function addSession(SessionInterface $session);

    /**
     * @return ArrayCollection|SessionInterface[]
     */
    public function getSessions();
}

