<?php

declare(strict_types=1);

namespace App\ServiceTokenVoter;

use App\Classes\VoterPermissions;
use App\Entity\CohortInterface;

class Cohort extends AbstractReadWriteEntityVoter
{
    public function __construct()
    {
        parent::__construct(
            CohortInterface::class,
            [
                VoterPermissions::CREATE,
                VoterPermissions::VIEW,
                VoterPermissions::EDIT,
                VoterPermissions::DELETE,
            ]
        );
    }

    protected function getSchoolIdFromEntity(mixed $subject): int
    {
        return $subject->getProgramYear()->getProgram()->getSchool()->getId();
    }
}
