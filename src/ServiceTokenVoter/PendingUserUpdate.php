<?php

declare(strict_types=1);

namespace App\ServiceTokenVoter;

use App\Classes\VoterPermissions;
use App\Entity\PendingUserUpdateInterface;

class PendingUserUpdate extends AbstractReadWriteEntityVoter
{
    public function __construct()
    {
        parent::__construct(
            PendingUserUpdateInterface::class,
            [
                VoterPermissions::VIEW,
                VoterPermissions::EDIT,
                VoterPermissions::DELETE,
            ]
        );
    }

    protected function getSchoolIdFromEntity(object $subject): int
    {
        return $subject->getUser()->getSchool()->getId();
    }
}
