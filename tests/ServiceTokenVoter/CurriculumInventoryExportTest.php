<?php

declare(strict_types=1);

namespace App\Tests\ServiceTokenVoter;

use App\Classes\VoterPermissions;
use App\Entity\CurriculumInventoryExportInterface;
use App\Entity\CurriculumInventoryReportInterface;
use App\Entity\SchoolInterface;
use App\ServiceTokenVoter\CurriculumInventoryExport as Voter;
use Mockery as m;

final class CurriculumInventoryExportTest extends AbstractReadWriteBase
{
    public function setUp(): void
    {
        parent::setUp();
        $this->voter = new Voter();
    }

    public static function supportsTypeProvider(): array
    {
        return [
            [CurriculumInventoryExportInterface::class, true],
            [self::class, false],
        ];
    }

    public static function supportsAttributesProvider(): array
    {
        return [
            [VoterPermissions::VIEW, true],
            [VoterPermissions::CREATE, true],
            [VoterPermissions::DELETE, false],
            [VoterPermissions::EDIT, false],
            [VoterPermissions::LOCK, false],
            [VoterPermissions::UNLOCK, false],
            [VoterPermissions::ROLLOVER, false],
            [VoterPermissions::CREATE_TEMPORARY_FILE, false],
            [VoterPermissions::VIEW_DRAFT_CONTENTS, false],
            [VoterPermissions::VIEW_VIRTUAL_LINK, false],
            [VoterPermissions::ARCHIVE, false],
        ];
    }

    public static function writePermissionsProvider(): array
    {
        return [
            [VoterPermissions::CREATE],
        ];
    }

    protected function createMockSubjectWithSchoolContext(int $schoolId): m\MockInterface
    {
        $subject = $this->createMockSubject();
        $report = m::mock(CurriculumInventoryReportInterface::class);
        $school = m::mock(SchoolInterface::class);

        $subject->shouldReceive('getReport')->andReturn($report);
        $report->shouldReceive('getSchool')->andReturn($school);
        $school->shouldReceive('getId')->andReturn($schoolId);

        return $subject;
    }

    protected function createMockSubject(): m\MockInterface
    {
        return m::mock(CurriculumInventoryExportInterface::class);
    }
}
