<?php

declare(strict_types=1);

namespace App\Tests\ServiceTokenVoter;

use App\Classes\VoterPermissions;
use App\Entity\SchoolInterface;
use App\ServiceTokenVoter\School as Voter;
use Mockery as m;

final class SchoolTest extends AbstractReadWriteBase
{
    public function setUp(): void
    {
        parent::setUp();
        $this->voter = new Voter();
    }

    public static function supportsTypeProvider(): array
    {
        return [
            [SchoolInterface::class, true],
            [self::class, false],
        ];
    }

    public static function supportsAttributesProvider(): array
    {
        return [
            [VoterPermissions::VIEW, true],
            [VoterPermissions::CREATE, false],
            [VoterPermissions::DELETE, false],
            [VoterPermissions::EDIT, true],
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
            [VoterPermissions::EDIT],
        ];
    }

    protected function createMockSubjectWithSchoolContext(int $schoolId): m\MockInterface
    {
        $subject = $this->createMockSubject();

        $subject->shouldReceive('getId')->andReturn($schoolId);

        return $subject;
    }

    protected function createMockSubject(): m\MockInterface
    {
        return m::mock(SchoolInterface::class);
    }
}
