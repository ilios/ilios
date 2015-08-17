<?php

namespace Ilios\CoreBundle\Traits;

use Ilios\CoreBundle\Entity\SchoolInterface;

/**
 * Interface SchoolEntityInterface
 * @package Ilios\CoreBundle\Traits
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
    public function setSchool($school);
}
