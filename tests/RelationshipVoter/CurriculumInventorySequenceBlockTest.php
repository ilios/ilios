<?php

declare(strict_types=1);

namespace App\Tests\RelationshipVoter;

use App\Classes\VoterPermissions;
use App\Entity\CurriculumInventoryExportInterface;
use App\Entity\CurriculumInventoryReportInterface;
use App\Entity\CurriculumInventorySequenceBlockInterface;
use App\Entity\SchoolInterface;
use App\RelationshipVoter\CurriculumInventorySequenceBlock as Voter;
use App\Service\SessionUserPermissionChecker;
use Mockery as m;
use Symfony\Component\Security\Core\Authorization\Voter\VoterInterface;

final class CurriculumInventorySequenceBlockTest extends AbstractBase
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
        $block = m::mock(CurriculumInventorySequenceBlockInterface::class);
        $block->shouldReceive('getReport')->andReturn($report);
        $this->checkRootEntityAccess($block);
    }

    public function testCanView(): void
    {
        $user = $this->createMockNonRootSessionUser();
        $token = $this->createMockTokenWithMockSessionUser($user);
        $entity = m::mock(CurriculumInventorySequenceBlockInterface::class);
        $response = $this->voter->vote($token, $entity, [VoterPermissions::VIEW]);
        $this->assertEquals(VoterInterface::ACCESS_GRANTED, $response, "View allowed");
    }

    public function testCanEdit(): void
    {
        $user = $this->createMockNonRootSessionUser();
        $token = $this->createMockTokenWithMockSessionUser($user);
        $entity = m::mock(CurriculumInventorySequenceBlockInterface::class);
        $entity->shouldReceive('getId')->andReturn(1);
        $report = m::mock(CurriculumInventoryReportInterface::class);
        $report->shouldReceive('getId')->andReturn(1);
        $report->shouldReceive('getExport')->andReturn(null);
        $entity->shouldReceive('getReport')->andReturn($report);
        $school = m::mock(SchoolInterface::class);
        $school->shouldReceive('getId')->andReturn(1);
        $report->shouldReceive('getSchool')->andReturn($school);
        $this->permissionChecker->shouldReceive('canUpdateCurriculumInventoryReport')->andReturn(true);
        $response = $this->voter->vote($token, $entity, [VoterPermissions::EDIT]);
        $this->assertEquals(VoterInterface::ACCESS_GRANTED, $response, "Edit allowed");
    }

    public function testCanNotEdit(): void
    {
        $user = $this->createMockNonRootSessionUser();
        $token = $this->createMockTokenWithMockSessionUser($user);
        $entity = m::mock(CurriculumInventorySequenceBlockInterface::class);
        $entity->shouldReceive('getId')->andReturn(1);
        $report = m::mock(CurriculumInventoryReportInterface::class);
        $report->shouldReceive('getId')->andReturn(1);
        $report->shouldReceive('getExport')->andReturn(null);
        $entity->shouldReceive('getReport')->andReturn($report);
        $school = m::mock(SchoolInterface::class);
        $school->shouldReceive('getId')->andReturn(1);
        $report->shouldReceive('getSchool')->andReturn($school);
        $this->permissionChecker->shouldReceive('canUpdateCurriculumInventoryReport')->andReturn(false);
        $response = $this->voter->vote($token, $entity, [VoterPermissions::EDIT]);
        $this->assertEquals(VoterInterface::ACCESS_DENIED, $response, "Edit denied");
    }

    public function testCanDelete(): void
    {
        $user = $this->createMockNonRootSessionUser();
        $token = $this->createMockTokenWithMockSessionUser($user);
        $entity = m::mock(CurriculumInventorySequenceBlockInterface::class);
        $entity->shouldReceive('getId')->andReturn(1);
        $report = m::mock(CurriculumInventoryReportInterface::class);
        $report->shouldReceive('getId')->andReturn(1);
        $report->shouldReceive('getExport')->andReturn(null);
        $entity->shouldReceive('getReport')->andReturn($report);
        $school = m::mock(SchoolInterface::class);
        $school->shouldReceive('getId')->andReturn(1);
        $report->shouldReceive('getSchool')->andReturn($school);
        $this->permissionChecker->shouldReceive('canUpdateCurriculumInventoryReport')->andReturn(true);
        $response = $this->voter->vote($token, $entity, [VoterPermissions::DELETE]);
        $this->assertEquals(VoterInterface::ACCESS_GRANTED, $response, "Delete allowed");
    }

    public function testCanNotDelete(): void
    {
        $user = $this->createMockNonRootSessionUser();
        $token = $this->createMockTokenWithMockSessionUser($user);
        $entity = m::mock(CurriculumInventorySequenceBlockInterface::class);
        $entity->shouldReceive('getId')->andReturn(1);
        $report = m::mock(CurriculumInventoryReportInterface::class);
        $report->shouldReceive('getId')->andReturn(1);
        $report->shouldReceive('getExport')->andReturn(null);
        $entity->shouldReceive('getReport')->andReturn($report);
        $school = m::mock(SchoolInterface::class);
        $school->shouldReceive('getId')->andReturn(1);
        $report->shouldReceive('getSchool')->andReturn($school);
        $this->permissionChecker->shouldReceive('canUpdateCurriculumInventoryReport')->andReturn(false);
        $response = $this->voter->vote($token, $entity, [VoterPermissions::DELETE]);
        $this->assertEquals(VoterInterface::ACCESS_DENIED, $response, "Delete denied");
    }

    public function testCanCreate(): void
    {
        $user = $this->createMockNonRootSessionUser();
        $token = $this->createMockTokenWithMockSessionUser($user);
        $entity = m::mock(CurriculumInventorySequenceBlockInterface::class);
        $report = m::mock(CurriculumInventoryReportInterface::class);
        $report->shouldReceive('getId')->andReturn(1);
        $report->shouldReceive('getExport')->andReturn(null);
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
        $entity = m::mock(CurriculumInventorySequenceBlockInterface::class);
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

    public function testRootCannotCreateEditDeleteSequenceBlockOnFinalizedReport(): void
    {
        $user = $this->createMockRootSessionUser();
        $token = $this->createMockTokenWithMockSessionUser($user);
        $report = m::mock(CurriculumInventoryReportInterface::class);
        $report->shouldReceive('getExport')->andReturn(m::mock(CurriculumInventoryExportInterface::class));
        $entity = m::mock(CurriculumInventorySequenceBlockInterface::class);
        $entity->shouldReceive('getReport')->andReturn($report);
        foreach ([ VoterPermissions::CREATE, VoterPermissions::EDIT, VoterPermissions::DELETE ] as $attr) {
            $response = $this->voter->vote($token, $entity, [ $attr ]);
            $this->assertEquals(VoterInterface::ACCESS_DENIED, $response, "{$attr} allowed");
        }
    }

    public function testCannotCreateEditDeleteSequenceBlockOnFinalizedReport(): void
    {
        $user = $this->createMockNonRootSessionUser();
        $token = $this->createMockTokenWithMockSessionUser($user);
        $report = m::mock(CurriculumInventoryReportInterface::class);
        $report->shouldReceive('getExport')->andReturn(m::mock(CurriculumInventoryExportInterface::class));
        $entity = m::mock(CurriculumInventorySequenceBlockInterface::class);
        $entity->shouldReceive('getReport')->andReturn($report);
        foreach ([ VoterPermissions::CREATE, VoterPermissions::EDIT, VoterPermissions::DELETE ] as $attr) {
            $response = $this->voter->vote($token, $entity, [ $attr ]);
            $this->assertEquals(VoterInterface::ACCESS_DENIED, $response, "{$attr} allowed");
        }
    }

    public static function supportsTypeProvider(): array
    {
        return [
            [CurriculumInventorySequenceBlockInterface::class, true],
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
            [VoterPermissions::ROLLOVER, false],
            [VoterPermissions::CREATE_TEMPORARY_FILE, false],
            [VoterPermissions::VIEW_DRAFT_CONTENTS, false],
            [VoterPermissions::VIEW_VIRTUAL_LINK, false],
            [VoterPermissions::ARCHIVE, false],
        ];
    }
}
