<?php

declare(strict_types=1);

namespace App\ServiceTokenVoter;

use App\Classes\VoterPermissions;
use App\Entity\CurriculumInventoryExportInterface;

class CurriculumInventoryExport extends AbstractReadWriteEntityVoter
{
    public function __construct()
    {
        parent::__construct(
            CurriculumInventoryExportInterface::class,
            [
                VoterPermissions::CREATE,
                VoterPermissions::VIEW,
            ]
        );
    }

    protected function getSchoolIdFromEntity(object $subject): int
    {
        return $subject->getReport()->getSchool()->getId();
    }
}
