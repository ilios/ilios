<?php

declare(strict_types=1);

namespace App\Traits;

use App\Entity\SchoolInterface;

/**
 * Interface SchoolEntityInterface
 */
interface SchoolEntityInterface
{
    /**
     * @return SchoolInterface|null
     */
    public function getSchool(): ?SchoolInterface;

    public function setSchool(SchoolInterface $school);
}
