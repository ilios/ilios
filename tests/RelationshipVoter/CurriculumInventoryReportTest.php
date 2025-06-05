<?php

declare(strict_types=1);

namespace App\Tests\RelationshipVoter;

use App\Classes\VoterPermissions;
use App\Entity\CurriculumInventoryExportInterface;
use App\Entity\CurriculumInventoryReportInterface;
use App\Entity\SchoolInterface;
use App\RelationshipVoter\CurriculumInventoryReport as Voter;
use App\Service\SessionUserPermissionChecker;
use Mockery as m;
use Symfony\Component\Security\Core\Authorization\Voter\VoterInterface;

final class CurriculumInventoryReportTest extends AbstractBase
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
        $this->checkRootEntityAccess($report);
    }

    public function testCanView(): void
    {
        $user = $this->createMockNonRootSessionUser();
        $token = $this->createMockTokenWithMockSessionUser($user);
        $entity = m::mock(CurriculumInventoryReportInterface::class);
        $response = $this->voter->vote($token, $entity, [VoterPermissions::VIEW]);
        $this->assertEquals(VoterInterface::ACCESS_GRANTED, $response, "View allowed");
    }

    public function testCanEdit(): void
    {
        $user = $this->createMockNonRootSessionUser();
        $token = $this->createMockTokenWithMockSessionUser($user);
        $entity = m::mock(CurriculumInventoryReportInterface::class);
        $entity->shouldReceive('getExport')->andReturn(null);
        $entity->shouldReceive('getId')->andReturn(1);
        $school = m::mock(SchoolInterface::class);
        $school->shouldReceive('getId')->andReturn(1);
        $entity->shouldReceive('getSchool')->andReturn($school);
        $this->permissionChecker->shouldReceive('canUpdateCurriculumInventoryReport')->andReturn(true);
        $response = $this->voter->vote($token, $entity, [VoterPermissions::EDIT]);
        $this->assertEquals(VoterInterface::ACCESS_GRANTED, $response, "Edit allowed");
    }

    public function testCanNotEdit(): void
    {
        $user = $this->createMockNonRootSessionUser();
        $token = $this->createMockTokenWithMockSessionUser($user);
        $entity = m::mock(CurriculumInventoryReportInterface::class);
        $entity->shouldReceive('getId')->andReturn(1);
        $entity->shouldReceive('getExport')->andReturn(null);
        $school = m::mock(SchoolInterface::class);
        $school->shouldReceive('getId')->andReturn(1);
        $entity->shouldReceive('getSchool')->andReturn($school);
        $this->permissionChecker->shouldReceive('canUpdateCurriculumInventoryReport')->andReturn(false);
        $response = $this->voter->vote($token, $entity, [VoterPermissions::EDIT]);
        $this->assertEquals(VoterInterface::ACCESS_DENIED, $response, "Edit denied");
    }

    public function testCanDelete(): void
    {
        $user = $this->createMockNonRootSessionUser();
        $token = $this->createMockTokenWithMockSessionUser($user);
        $entity = m::mock(CurriculumInventoryReportInterface::class);
        $entity->shouldReceive('getId')->andReturn(1);
        $entity->shouldReceive('getExport')->andReturn(null);
        $school = m::mock(SchoolInterface::class);
        $school->shouldReceive('getId')->andReturn(1);
        $entity->shouldReceive('getSchool')->andReturn($school);
        $this->permissionChecker->shouldReceive('canDeleteCurriculumInventoryReport')->andReturn(true);
        $response = $this->voter->vote($token, $entity, [VoterPermissions::DELETE]);
        $this->assertEquals(VoterInterface::ACCESS_GRANTED, $response, "Delete allowed");
    }

    public function testCanNotDelete(): void
    {
        $user = $this->createMockNonRootSessionUser();
        $token = $this->createMockTokenWithMockSessionUser($user);
        $entity = m::mock(CurriculumInventoryReportInterface::class);
        $entity->shouldReceive('getId')->andReturn(1);
        $entity->shouldReceive('getExport')->andReturn(null);
        $school = m::mock(SchoolInterface::class);
        $school->shouldReceive('getId')->andReturn(1);
        $entity->shouldReceive('getSchool')->andReturn($school);
        $this->permissionChecker->shouldReceive('canDeleteCurriculumInventoryReport')->andReturn(false);
        $response = $this->voter->vote($token, $entity, [VoterPermissions::DELETE]);
        $this->assertEquals(VoterInterface::ACCESS_DENIED, $response, "Delete denied");
    }

    public function testCanCreate(): void
    {
        $user = $this->createMockNonRootSessionUser();
        $token = $this->createMockTokenWithMockSessionUser($user);
        $entity = m::mock(CurriculumInventoryReportInterface::class);
        $entity->shouldReceive('getExport')->andReturn(null);
        $school = m::mock(SchoolInterface::class);
        $school->shouldReceive('getId')->andReturn(1);
        $entity->shouldReceive('getSchool')->andReturn($school);
        $this->permissionChecker->shouldReceive('canCreateCurriculumInventoryReport')->andReturn(true);
        $response = $this->voter->vote($token, $entity, [VoterPermissions::CREATE]);
        $this->assertEquals(VoterInterface::ACCESS_GRANTED, $response, "Create allowed");
    }

    public function testCanNotCreate(): void
    {
        $user = $this->createMockNonRootSessionUser();
        $token = $this->createMockTokenWithMockSessionUser($user);
        $entity = m::mock(CurriculumInventoryReportInterface::class);
        $entity->shouldReceive('getExport')->andReturn(null);
        $school = m::mock(SchoolInterface::class);
        $school->shouldReceive('getId')->andReturn(1);
        $entity->shouldReceive('getSchool')->andReturn($school);
        $this->permissionChecker->shouldReceive('canCreateCurriculumInventoryReport')->andReturn(false);
        $response = $this->voter->vote($token, $entity, [VoterPermissions::CREATE]);
        $this->assertEquals(VoterInterface::ACCESS_DENIED, $response, "Create denied");
    }

    public function testRootCannotCreateEditDeleteFinalizedReport(): void
    {
        $user = $this->createMockRootSessionUser();
        $token = $this->createMockTokenWithMockSessionUser($user);
        $entity = m::mock(CurriculumInventoryReportInterface::class);
        $entity->shouldReceive('getExport')->andReturn(m::mock(CurriculumInventoryExportInterface::class));
        foreach ([ VoterPermissions::CREATE, VoterPermissions::EDIT, VoterPermissions::DELETE ] as $attr) {
            $response = $this->voter->vote($token, $entity, [ $attr ]);
            $this->assertEquals(VoterInterface::ACCESS_DENIED, $response, "{$attr} allowed");
        }
    }

    public function testCannotCreateEditDeleteFinalizedReport(): void
    {
        $user = $this->createMockNonRootSessionUser();
        $token = $this->createMockTokenWithMockSessionUser($user);
        $entity = m::mock(CurriculumInventoryReportInterface::class);
        $entity->shouldReceive('getExport')->andReturn(m::mock(CurriculumInventoryExportInterface::class));
        foreach ([ VoterPermissions::CREATE, VoterPermissions::EDIT, VoterPermissions::DELETE ] as $attr) {
            $response = $this->voter->vote($token, $entity, [ $attr ]);
            $this->assertEquals(VoterInterface::ACCESS_DENIED, $response, "{$attr} allowed");
        }
    }

    public static function supportsTypeProvider(): array
    {
        return [
            [CurriculumInventoryReportInterface::class, true],
            [self::class, false],
        ];
    }

    public static function supportsAttributesProvider(): array
    {
        return [
            [VoterPermissions::VIEW, true],
            [VoterPermissions::CREATE, true],
            [VoterPermissions::DELETE, true],
            [VoterPermissions::EDIT, true],
            [VoterPermissions::LOCK, false],
            [VoterPermissions::UNLOCK, false],
            [VoterPermissions::ROLLOVER, true],
            [VoterPermissions::CREATE_TEMPORARY_FILE, false],
            [VoterPermissions::VIEW_DRAFT_CONTENTS, false],
            [VoterPermissions::VIEW_VIRTUAL_LINK, false],
            [VoterPermissions::ARCHIVE, false],
        ];
    }
}
