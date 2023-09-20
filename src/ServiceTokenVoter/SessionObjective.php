<?php

declare(strict_types=1);

namespace App\ServiceTokenVoter;

use App\Classes\VoterPermissions;
use App\Entity\SessionObjectiveInterface;

class SessionObjective extends AbstractReadWriteEntityVoter
{
    public function __construct()
    {
        parent::__construct(
            SessionObjectiveInterface::class,
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
        return $subject->getSession()->getCourse()->getSchool()->getId();
    }
}
