<?php

declare(strict_types=1);

namespace App\Tests\RelationshipVoter;

use App\Classes\VoterPermissions;
use App\Entity\CurriculumInventoryReportInterface;
use App\Entity\CurriculumInventoryExportInterface;
use App\Entity\SchoolInterface;
use App\RelationshipVoter\CurriculumInventoryExport as Voter;
use App\Service\SessionUserPermissionChecker;
use Mockery as m;
use Symfony\Component\Security\Core\Authorization\Voter\VoterInterface;

final class CurriculumInventoryExportTest extends AbstractBase
{
    public function setUp(): void
    {
        parent::setUp();
        $this->permissionChecker = m::mock(SessionUserPermissionChecker::class);
        $this->voter = new Voter($this->permissionChecker);
    }

    public function testAllowsRootFullAccess(): void
    {
        $report = m::mock(CurriculumInventoryReportInterface::class);
        $report->shouldReceive('getExport')->andReturn(null);
        $export = m::mock(CurriculumInventoryExportInterface::class);
        $export->shouldReceive('getReport')->andReturn($report);
        $this->checkRootEntityAccess(
            $export,
            [VoterPermissions::VIEW, VoterPermissions::CREATE]
        );
    }

    public function testCanView(): void
    {
        $user = $this->createMockNonRootSessionUser();
        $token = $this->createMockTokenWithMockSessionUser($user);
        $entity = m::mock(CurriculumInventoryExportInterface::class);
        $response = $this->voter->vote($token, $entity, [VoterPermissions::VIEW]);
        $this->assertEquals(VoterInterface::ACCESS_GRANTED, $response, "View allowed");
    }

    public function testCanCreate(): void
    {
        $user = $this->createMockNonRootSessionUser();
        $token = $this->createMockTokenWithMockSessionUser($user);
        $entity = m::mock(CurriculumInventoryExportInterface::class);
        $report = m::mock(CurriculumInventoryReportInterface::class);
        $report->shouldReceive('getExport')->andReturn(null);
        $report->shouldReceive('getId')->andReturn(1);
        $entity->shouldReceive('getReport')->andReturn($report);
        $school = m::mock(SchoolInterface::class);
        $school->shouldReceive('getId')->andReturn(1);
        $report->shouldReceive('getSchool')->andReturn($school);
        $this->permissionChecker->shouldReceive('canUpdateCurriculumInventoryReport')->andReturn(true);
        $response = $this->voter->vote($token, $entity, [VoterPermissions::CREATE]);
        $this->assertEquals(VoterInterface::ACCESS_GRANTED, $response, "Create allowed");
    }

    public function testCanNotCreate(): void
    {
        $user = $this->createMockNonRootSessionUser();
        $token = $this->createMockTokenWithMockSessionUser($user);
        $entity = m::mock(CurriculumInventoryExportInterface::class);
        $report = m::mock(CurriculumInventoryReportInterface::class);
        $report->shouldReceive('getId')->andReturn(1);
        $report->shouldReceive('getExport')->andReturn(null);
        $entity->shouldReceive('getReport')->andReturn($report);
        $school = m::mock(SchoolInterface::class);
        $school->shouldReceive('getId')->andReturn(1);
        $report->shouldReceive('getSchool')->andReturn($school);
        $this->permissionChecker->shouldReceive('canUpdateCurriculumInventoryReport')->andReturn(false);
        $response = $this->voter->vote($token, $entity, [VoterPermissions::CREATE]);
        $this->assertEquals(VoterInterface::ACCESS_DENIED, $response, "Create denied");
    }

    public function testRootCannotCreateExportOnFinalizedReport(): void
    {
        $user = $this->createMockRootSessionUser();
        $token = $this->createMockTokenWithMockSessionUser($user);
        $report = m::mock(CurriculumInventoryReportInterface::class);
        $report->shouldReceive('getExport')->andReturn(m::mock(CurriculumInventoryExportInterface::class));
        $entity = m::mock(CurriculumInventoryExportInterface::class);
        $entity->shouldReceive('getReport')->andReturn($report);
        $response = $this->voter->vote($token, $entity, [ VoterPermissions::CREATE ]);
        $this->assertEquals(VoterInterface::ACCESS_DENIED, $response, "Create allowed");
    }

    public function testCannotCreateExportOnFinalizedReport(): void
    {
        $user = $this->createMockNonRootSessionUser();
        $token = $this->createMockTokenWithMockSessionUser($user);
        $report = m::mock(CurriculumInventoryReportInterface::class);
        $report->shouldReceive('getExport')->andReturn(m::mock(CurriculumInventoryExportInterface::class));
        $entity = m::mock(CurriculumInventoryExportInterface::class);
        $entity->shouldReceive('getReport')->andReturn($report);
        $response = $this->voter->vote($token, $entity, [ VoterPermissions::CREATE ]);
        $this->assertEquals(VoterInterface::ACCESS_DENIED, $response, "Create allowed");
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
}
