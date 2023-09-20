<?php

declare(strict_types=1);

namespace App\ServiceTokenVoter;

use App\Classes\VoterPermissions;
use App\Entity\CourseInterface;

class Course extends AbstractReadWriteEntityVoter
{
    public function __construct()
    {
        parent::__construct(
            CourseInterface::class,
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

    protected function getSchoolIdFromEntity(mixed $subject): int
    {
        return $subject->getSchool()->getId();
    }
}
