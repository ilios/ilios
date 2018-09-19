<?php
namespace Tests\App\RelationshipVoter;

use App\RelationshipVoter\AbstractVoter;
use App\RelationshipVoter\CurriculumInventoryReport as Voter;
use App\Service\PermissionChecker;
use App\Entity\CurriculumInventoryExport;
use App\Entity\CurriculumInventoryReport;
use App\Entity\School;
use App\Service\Config;
use Mockery as m;
use Symfony\Component\Security\Core\Authorization\Voter\VoterInterface;

class CurriculumInventoryReportTest extends AbstractBase
{
    public function setup()
    {
        $this->permissionChecker = m::mock(PermissionChecker::class);
        $this->voter = new Voter($this->permissionChecker);
    }

    public function testAllowsRootFullAccess()
    {
        $report = m::mock(CurriculumInventoryReport::class);
        $report->shouldReceive('getExport')->andReturn(null);
        $this->checkRootEntityAccess($report);
    }

    public function testCanView()
    {
        $token = $this->createMockTokenWithNonRootSessionUser();
        $entity = m::mock(CurriculumInventoryReport::class);
        $response = $this->voter->vote($token, $entity, [AbstractVoter::VIEW]);
        $this->assertEquals(VoterInterface::ACCESS_GRANTED, $response, "View allowed");
    }

    public function testCanEdit()
    {
        $token = $this->createMockTokenWithNonRootSessionUser();
        $entity = m::mock(CurriculumInventoryReport::class);
        $entity->shouldReceive('getExport')->andReturn(null);
        $entity->shouldReceive('getId')->andReturn(1);
        $school = m::mock(School::class);
        $school->shouldReceive('getId')->andReturn(1);
        $entity->shouldReceive('getSchool')->andReturn($school);
        $this->permissionChecker->shouldReceive('canUpdateCurriculumInventoryReport')->andReturn(true);
        $response = $this->voter->vote($token, $entity, [AbstractVoter::EDIT]);
        $this->assertEquals(VoterInterface::ACCESS_GRANTED, $response, "Edit allowed");
    }

    public function testCanNotEdit()
    {
        $token = $this->createMockTokenWithNonRootSessionUser();
        $entity = m::mock(CurriculumInventoryReport::class);
        $entity->shouldReceive('getId')->andReturn(1);
        $entity->shouldReceive('getExport')->andReturn(null);
        $school = m::mock(School::class);
        $school->shouldReceive('getId')->andReturn(1);
        $entity->shouldReceive('getSchool')->andReturn($school);
        $this->permissionChecker->shouldReceive('canUpdateCurriculumInventoryReport')->andReturn(false);
        $response = $this->voter->vote($token, $entity, [AbstractVoter::EDIT]);
        $this->assertEquals(VoterInterface::ACCESS_DENIED, $response, "Edit denied");
    }

    public function testCanDelete()
    {
        $token = $this->createMockTokenWithNonRootSessionUser();
        $entity = m::mock(CurriculumInventoryReport::class);
        $entity->shouldReceive('getId')->andReturn(1);
        $entity->shouldReceive('getExport')->andReturn(null);
        $school = m::mock(School::class);
        $school->shouldReceive('getId')->andReturn(1);
        $entity->shouldReceive('getSchool')->andReturn($school);
        $this->permissionChecker->shouldReceive('canDeleteCurriculumInventoryReport')->andReturn(true);
        $response = $this->voter->vote($token, $entity, [AbstractVoter::DELETE]);
        $this->assertEquals(VoterInterface::ACCESS_GRANTED, $response, "Delete allowed");
    }

    public function testCanNotDelete()
    {
        $token = $this->createMockTokenWithNonRootSessionUser();
        $entity = m::mock(CurriculumInventoryReport::class);
        $entity->shouldReceive('getId')->andReturn(1);
        $entity->shouldReceive('getExport')->andReturn(null);
        $school = m::mock(School::class);
        $school->shouldReceive('getId')->andReturn(1);
        $entity->shouldReceive('getSchool')->andReturn($school);
        $this->permissionChecker->shouldReceive('canDeleteCurriculumInventoryReport')->andReturn(false);
        $response = $this->voter->vote($token, $entity, [AbstractVoter::DELETE]);
        $this->assertEquals(VoterInterface::ACCESS_DENIED, $response, "Delete denied");
    }

    public function testCanCreate()
    {
        $token = $this->createMockTokenWithNonRootSessionUser();
        $entity = m::mock(CurriculumInventoryReport::class);
        $entity->shouldReceive('getExport')->andReturn(null);
        $school = m::mock(School::class);
        $school->shouldReceive('getId')->andReturn(1);
        $entity->shouldReceive('getSchool')->andReturn($school);
        $this->permissionChecker->shouldReceive('canCreateCurriculumInventoryReport')->andReturn(true);
        $response = $this->voter->vote($token, $entity, [AbstractVoter::CREATE]);
        $this->assertEquals(VoterInterface::ACCESS_GRANTED, $response, "Create allowed");
    }

    public function testCanNotCreate()
    {
        $token = $this->createMockTokenWithNonRootSessionUser();
        $entity = m::mock(CurriculumInventoryReport::class);
        $entity->shouldReceive('getExport')->andReturn(null);
        $school = m::mock(School::class);
        $school->shouldReceive('getId')->andReturn(1);
        $entity->shouldReceive('getSchool')->andReturn($school);
        $this->permissionChecker->shouldReceive('canCreateCurriculumInventoryReport')->andReturn(false);
        $response = $this->voter->vote($token, $entity, [AbstractVoter::CREATE]);
        $this->assertEquals(VoterInterface::ACCESS_DENIED, $response, "Create denied");
    }

    public function testRootCannotCreateEditDeleteFinalizedReport()
    {
        $token = $this->createMockTokenWithRootSessionUser();
        $entity = m::mock(CurriculumInventoryReport::class);
        $entity->shouldReceive('getExport')->andReturn(m::mock(CurriculumInventoryExport::class));
        foreach ([ AbstractVoter::CREATE, AbstractVoter::EDIT, AbstractVoter::DELETE ] as $attr) {
            $response = $this->voter->vote($token, $entity, [ $attr ]);
            $this->assertEquals(VoterInterface::ACCESS_DENIED, $response, "${attr} allowed");
        }
    }

    public function testCannotCreateEditDeleteFinalizedReport()
    {
        $token = $this->createMockTokenWithNonRootSessionUser();
        $entity = m::mock(CurriculumInventoryReport::class);
        $entity->shouldReceive('getExport')->andReturn(m::mock(CurriculumInventoryExport::class));
        foreach ([ AbstractVoter::CREATE, AbstractVoter::EDIT, AbstractVoter::DELETE ] as $attr) {
            $response = $this->voter->vote($token, $entity, [ $attr ]);
            $this->assertEquals(VoterInterface::ACCESS_DENIED, $response, "${attr} allowed");
        }
    }
}
