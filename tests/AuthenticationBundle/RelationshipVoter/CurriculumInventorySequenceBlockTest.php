<?php
namespace Tests\AuthenticationBundle\RelationshipVoter;

use Ilios\AuthenticationBundle\RelationshipVoter\AbstractVoter;
use Ilios\AuthenticationBundle\RelationshipVoter\CurriculumInventorySequenceBlock as Voter;
use Ilios\AuthenticationBundle\Service\PermissionChecker;
use Ilios\CoreBundle\Entity\CurriculumInventoryReport;
use Ilios\CoreBundle\Entity\CurriculumInventorySequenceBlock;
use Ilios\CoreBundle\Entity\DTO\CurriculumInventorySequenceBlockDTO;
use Ilios\CoreBundle\Entity\School;
use Mockery as m;
use Symfony\Component\Security\Core\Authorization\Voter\VoterInterface;

class CurriculumInventorySequenceBlockTest extends AbstractBase
{
    public function setup()
    {
        $this->permissionChecker = m::mock(PermissionChecker::class);
        $this->voter = new Voter($this->permissionChecker);
    }

    public function testAllowsRootFullAccess()
    {
        $this->checkRootEntityAccess(CurriculumInventorySequenceBlock::class);
    }

    public function testCanView()
    {
        $token = $this->createMockTokenWithNonRootSessionUser();
        $entity = m::mock(CurriculumInventorySequenceBlock::class);
        $entity->shouldReceive('getId')->andReturn(1);
        $report = m::mock(CurriculumInventoryReport::class);
        $report->shouldReceive('getId')->andReturn(1);
        $entity->shouldReceive('getReport')->andReturn($report);
        $school = m::mock(School::class);
        $school->shouldReceive('getId')->andReturn(1);
        $report->shouldReceive('getSchool')->andReturn($school);
        $this->permissionChecker->shouldReceive('canReadCurriculumInventoryReport')->andReturn(true);
        $response = $this->voter->vote($token, $entity, [AbstractVoter::VIEW]);
        $this->assertEquals(VoterInterface::ACCESS_GRANTED, $response, "View allowed");
    }

    public function testCanNotView()
    {
        $token = $this->createMockTokenWithNonRootSessionUser();
        $entity = m::mock(CurriculumInventorySequenceBlock::class);
        $entity->shouldReceive('getId')->andReturn(1);
        $report = m::mock(CurriculumInventoryReport::class);
        $report->shouldReceive('getId')->andReturn(1);
        $entity->shouldReceive('getReport')->andReturn($report);
        $school = m::mock(School::class);
        $school->shouldReceive('getId')->andReturn(1);
        $report->shouldReceive('getSchool')->andReturn($school);
        $this->permissionChecker->shouldReceive('canReadCurriculumInventoryReport')->andReturn(false);
        $response = $this->voter->vote($token, $entity, [AbstractVoter::VIEW]);
        $this->assertEquals(VoterInterface::ACCESS_DENIED, $response, "View denied");
    }

    public function testCanEdit()
    {
        $token = $this->createMockTokenWithNonRootSessionUser();
        $entity = m::mock(CurriculumInventorySequenceBlock::class);
        $entity->shouldReceive('getId')->andReturn(1);
        $report = m::mock(CurriculumInventoryReport::class);
        $report->shouldReceive('getId')->andReturn(1);
        $entity->shouldReceive('getReport')->andReturn($report);
        $school = m::mock(School::class);
        $school->shouldReceive('getId')->andReturn(1);
        $report->shouldReceive('getSchool')->andReturn($school);
        $this->permissionChecker->shouldReceive('canUpdateCurriculumInventoryReport')->andReturn(true);
        $response = $this->voter->vote($token, $entity, [AbstractVoter::EDIT]);
        $this->assertEquals(VoterInterface::ACCESS_GRANTED, $response, "Edit allowed");
    }

    public function testCanNotEdit()
    {
        $token = $this->createMockTokenWithNonRootSessionUser();
        $entity = m::mock(CurriculumInventorySequenceBlock::class);
        $entity->shouldReceive('getId')->andReturn(1);
        $report = m::mock(CurriculumInventoryReport::class);
        $report->shouldReceive('getId')->andReturn(1);
        $entity->shouldReceive('getReport')->andReturn($report);
        $school = m::mock(School::class);
        $school->shouldReceive('getId')->andReturn(1);
        $report->shouldReceive('getSchool')->andReturn($school);
        $this->permissionChecker->shouldReceive('canUpdateCurriculumInventoryReport')->andReturn(false);
        $response = $this->voter->vote($token, $entity, [AbstractVoter::EDIT]);
        $this->assertEquals(VoterInterface::ACCESS_DENIED, $response, "Edit denied");
    }

    public function testCanDelete()
    {
        $token = $this->createMockTokenWithNonRootSessionUser();
        $entity = m::mock(CurriculumInventorySequenceBlock::class);
        $entity->shouldReceive('getId')->andReturn(1);
        $report = m::mock(CurriculumInventoryReport::class);
        $report->shouldReceive('getId')->andReturn(1);
        $entity->shouldReceive('getReport')->andReturn($report);
        $school = m::mock(School::class);
        $school->shouldReceive('getId')->andReturn(1);
        $report->shouldReceive('getSchool')->andReturn($school);
        $this->permissionChecker->shouldReceive('canUpdateCurriculumInventoryReport')->andReturn(true);
        $response = $this->voter->vote($token, $entity, [AbstractVoter::DELETE]);
        $this->assertEquals(VoterInterface::ACCESS_GRANTED, $response, "Delete allowed");
    }

    public function testCanNotDelete()
    {
        $token = $this->createMockTokenWithNonRootSessionUser();
        $entity = m::mock(CurriculumInventorySequenceBlock::class);
        $entity->shouldReceive('getId')->andReturn(1);
        $report = m::mock(CurriculumInventoryReport::class);
        $report->shouldReceive('getId')->andReturn(1);
        $entity->shouldReceive('getReport')->andReturn($report);
        $school = m::mock(School::class);
        $school->shouldReceive('getId')->andReturn(1);
        $report->shouldReceive('getSchool')->andReturn($school);
        $this->permissionChecker->shouldReceive('canUpdateCurriculumInventoryReport')->andReturn(false);
        $response = $this->voter->vote($token, $entity, [AbstractVoter::DELETE]);
        $this->assertEquals(VoterInterface::ACCESS_DENIED, $response, "Delete denied");
    }

    public function testCanCreate()
    {
        $token = $this->createMockTokenWithNonRootSessionUser();
        $entity = m::mock(CurriculumInventorySequenceBlock::class);
        $report = m::mock(CurriculumInventoryReport::class);
        $report->shouldReceive('getId')->andReturn(1);
        $entity->shouldReceive('getReport')->andReturn($report);
        $school = m::mock(School::class);
        $school->shouldReceive('getId')->andReturn(1);
        $report->shouldReceive('getSchool')->andReturn($school);
        $this->permissionChecker->shouldReceive('canUpdateCurriculumInventoryReport')->andReturn(true);
        $response = $this->voter->vote($token, $entity, [AbstractVoter::CREATE]);
        $this->assertEquals(VoterInterface::ACCESS_GRANTED, $response, "Create allowed");
    }

    public function testCanNotCreate()
    {
        $token = $this->createMockTokenWithNonRootSessionUser();
        $entity = m::mock(CurriculumInventorySequenceBlock::class);
        $report = m::mock(CurriculumInventoryReport::class);
        $report->shouldReceive('getId')->andReturn(1);
        $entity->shouldReceive('getReport')->andReturn($report);
        $school = m::mock(School::class);
        $school->shouldReceive('getId')->andReturn(1);
        $report->shouldReceive('getSchool')->andReturn($school);
        $this->permissionChecker->shouldReceive('canUpdateCurriculumInventoryReport')->andReturn(false);
        $response = $this->voter->vote($token, $entity, [AbstractVoter::CREATE]);
        $this->assertEquals(VoterInterface::ACCESS_DENIED, $response, "Create denied");
    }
}
