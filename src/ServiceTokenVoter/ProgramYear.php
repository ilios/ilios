<?php

declare(strict_types=1);

namespace App\ServiceTokenVoter;

use App\Classes\VoterPermissions;
use App\Entity\ProgramYearInterface;

class ProgramYear extends AbstractReadWriteEntityVoter
{
    public function __construct()
    {
        parent::__construct(
            ProgramYearInterface::class,
            [
                VoterPermissions::CREATE,
                VoterPermissions::VIEW,
                VoterPermissions::EDIT,
                VoterPermissions::DELETE,
                VoterPermissions::UNLOCK,
                VoterPermissions::LOCK,
                VoterPermissions::ARCHIVE,
            ]
        );
    }

    protected function getSchoolIdFromEntity(object $subject): int
    {
        return $subject->getProgram()->getSchool()->getId();
    }
}
