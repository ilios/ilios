<?php

declare(strict_types=1);

namespace App\ServiceTokenVoter;

use App\Classes\VoterPermissions;
use App\Entity\ProgramYearObjectiveInterface;

class ProgramYearObjective extends AbstractReadWriteEntityVoter
{
    public function __construct()
    {
        parent::__construct(
            ProgramYearObjectiveInterface::class,
            [
                VoterPermissions::CREATE,
                VoterPermissions::VIEW,
                VoterPermissions::EDIT,
                VoterPermissions::DELETE,
            ]
        );
    }

    protected function getSchoolIdFromEntity(object $subject): int
    {
        return $subject->getProgramYear()->getProgram()->getSchool()->getId();
    }
}
