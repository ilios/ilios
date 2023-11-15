<?php

declare(strict_types=1);

namespace App\ServiceTokenVoter;

use App\Classes\VoterPermissions;
use App\Entity\CourseObjectiveInterface;

class CourseObjective extends AbstractReadWriteEntityVoter
{
    public function __construct()
    {
        parent::__construct(
            CourseObjectiveInterface::class,
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
        return $subject->getCourse()->getSchool()->getId();
    }
}
