<?php

namespace Ilios\CoreBundle\Traits;

use Ilios\CoreBundle\Entity\SchoolInterface;

/**
 * Interface SchoolEntityInterface
 */
interface SchoolEntityInterface
{
    /**
     * @return SchoolInterface|null
     */
    public function getSchool();

    /**
     * @param SchoolInterface $school
     */
    public function setSchool(SchoolInterface $school);
}
