<?php

declare(strict_types=1);

namespace App\ServiceTokenVoter;

use App\Classes\VoterPermissions;
use App\Entity\CurriculumInventoryReportInterface;

class CurriculumInventoryReport extends AbstractReadWriteEntityVoter
{
    public function __construct()
    {
        parent::__construct(
            CurriculumInventoryReportInterface::class,
            [
                VoterPermissions::CREATE,
                VoterPermissions::VIEW,
                VoterPermissions::EDIT,
                VoterPermissions::DELETE,
                VoterPermissions::ROLLOVER,
            ]
        );
    }

    protected function getSchoolIdFromEntity(mixed $subject): int
    {
        return $subject->getSchool()->getId();
    }
}
