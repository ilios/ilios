<?php

namespace Ilios\CoreBundle\Traits;

use Ilios\CoreBundle\Entity\SchoolInterface;

/**
 * Class SchoolEntity
 * @package Ilios\CoreBundle\Traits
 */
trait SchoolEntity
{
    /**
     * @return SchoolInterface|null
     */
    public function getSchool()
    {
        if ($this->school && !$this->school->isDeleted()) {
            return $this->school;
        }

        return null;
    }

    /**
     * @param SchoolInterface $school
     */
    public function setArchived($school)
    {
        $this->school = $school;
    }
}
