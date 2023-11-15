<?php

declare(strict_types=1);

namespace App\ServiceTokenVoter;

use App\Classes\VoterPermissions;
use App\Entity\TermInterface;

class Term extends AbstractReadWriteEntityVoter
{
    public function __construct()
    {
        parent::__construct(
            TermInterface::class,
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
        return $subject->getVocabulary()->getSchool()->getId();
    }
}
