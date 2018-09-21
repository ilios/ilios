<?php

namespace App\Traits;

use App\Entity\SchoolInterface;

/**
 * Class SchoolEntity
 */
trait SchoolEntity
{
    /**
     * @return SchoolInterface|null
     */
    public function getSchool()
    {
        return $this->school;
    }

    /**
     * @param SchoolInterface $school
     */
    public function setSchool(SchoolInterface $school)
    {
        $this->school = $school;
    }
}
