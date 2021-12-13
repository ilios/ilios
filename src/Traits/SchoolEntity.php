<?php

declare(strict_types=1);

namespace App\Traits;

use App\Entity\SchoolInterface;

/**
 * Class SchoolEntity
 */
trait SchoolEntity
{
    public function getSchool(): ?SchoolInterface
    {
        return $this->school;
    }

    public function setSchool(SchoolInterface $school)
    {
        $this->school = $school;
    }
}
