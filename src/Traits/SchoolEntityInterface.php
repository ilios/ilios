<?php

declare(strict_types=1);

namespace App\Traits;

use App\Entity\SchoolInterface;

interface SchoolEntityInterface
{
    public function getSchool(): SchoolInterface;

    public function setSchool(SchoolInterface $school);
}
