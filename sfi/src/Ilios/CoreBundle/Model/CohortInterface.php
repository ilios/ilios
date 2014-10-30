<?php

namespace Ilios\CoreBundle\Model;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;

use Ilios\CoreBundle\Traits\IdentifiableEntityInterface;

use Ilios\CoreBundle\Model\CourseInterface;
use Ilios\CoreBundle\Model\ProgramYearInterface;
use Ilios\CoreBundle\Traits\TitledEntityInterface;

/**
 * Interface CohortInterface
 */
interface CohortInterface extends IdentifiableEntityInterface, TitledEntityInterface
{
    /**
     * @param ProgramYearInterface $programYear
     */
    public function setProgramYear(ProgramYearInterface $programYear = null);

    /**
     * @return ProgramYearInterface
     */
    public function getProgramYear();

    /**
     * @param Collection $courses
     */
    public function setCourses(Collection $courses);

    /**
     * @param CourseInterface $course
     */
    public function addCourse(CourseInterface $course);

    /**
     * @return CourseInterface[]|ArrayCollection
     */
    public function getCourses();
}

