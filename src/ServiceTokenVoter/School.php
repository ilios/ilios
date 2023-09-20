<?php

declare(strict_types=1);

namespace App\ServiceTokenVoter;

use App\Classes\VoterPermissions;
use App\Entity\SchoolInterface;

class School extends AbstractReadWriteEntityVoter
{
    public function __construct()
    {
        parent::__construct(
            SchoolInterface::class,
            [
                VoterPermissions::VIEW,
                VoterPermissions::EDIT,
            ]
        );
    }

    protected function getSchoolIdFromEntity(mixed $subject): int
    {
        return $subject->getId();
    }
}
