<?php
namespace Tests\AuthenticationBundle\RelationshipVoter;

use Ilios\AuthenticationBundle\RelationshipVoter\AbstractVoter;
use Ilios\AuthenticationBundle\RelationshipVoter\CurriculumInventorySequence as Voter;
use Ilios\AuthenticationBundle\Service\PermissionChecker;
use Ilios\CoreBundle\Entity\CurriculumInventoryExport;
use Ilios\CoreBundle\Entity\CurriculumInventoryReport;
use Ilios\CoreBundle\Entity\CurriculumInventorySequence;
use Ilios\CoreBundle\Entity\School;
use Ilios\CoreBundle\Service\Config;
use Mockery as m;
use Symfony\Component\Security\Core\Authorization\Voter\VoterInterface;

class CurriculumInventorySequenceTest extends AbstractBase
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
        $sequence = m::mock(CurriculumInventorySequence::class);
        $sequence->shouldReceive('getReport')->andReturn($report);
        $this->checkRootEntityAccess($sequence);
    }

    public function testCanView()
    {
        $token = $this->createMockTokenWithNonRootSessionUser();
        $entity = m::mock(CurriculumInventorySequence::class);
        $response = $this->voter->vote($token, $entity, [AbstractVoter::VIEW]);
        $this->assertEquals(VoterInterface::ACCESS_GRANTED, $response, "View allowed");
    }

    public function testCanEdit()
    {
        $token = $this->createMockTokenWithNonRootSessionUser();
        $entity = m::mock(CurriculumInventorySequence::class);
        $entity->shouldReceive('getId')->andReturn(1);
        $report = m::mock(CurriculumInventoryReport::class);
        $report->shouldReceive('getId')->andReturn(1);
        $report->shouldReceive('getExport')->andReturn(null);
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
        $entity = m::mock(CurriculumInventorySequence::class);
        $entity->shouldReceive('getId')->andReturn(1);
        $report = m::mock(CurriculumInventoryReport::class);
        $report->shouldReceive('getId')->andReturn(1);
        $report->shouldReceive('getExport')->andReturn(null);
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
        $entity = m::mock(CurriculumInventorySequence::class);
        $entity->shouldReceive('getId')->andReturn(1);
        $report = m::mock(CurriculumInventoryReport::class);
        $report->shouldReceive('getId')->andReturn(1);
        $report->shouldReceive('getExport')->andReturn(null);
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
        $entity = m::mock(CurriculumInventorySequence::class);
        $entity->shouldReceive('getId')->andReturn(1);
        $report = m::mock(CurriculumInventoryReport::class);
        $report->shouldReceive('getId')->andReturn(1);
        $report->shouldReceive('getExport')->andReturn(null);
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
        $entity = m::mock(CurriculumInventorySequence::class);
        $report = m::mock(CurriculumInventoryReport::class);
        $report->shouldReceive('getId')->andReturn(1);
        $report->shouldReceive('getExport')->andReturn(null);
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
        $entity = m::mock(CurriculumInventorySequence::class);
        $report = m::mock(CurriculumInventoryReport::class);
        $report->shouldReceive('getId')->andReturn(1);
        $report->shouldReceive('getExport')->andReturn(null);
        $entity->shouldReceive('getReport')->andReturn($report);
        $school = m::mock(School::class);
        $school->shouldReceive('getId')->andReturn(1);
        $report->shouldReceive('getSchool')->andReturn($school);
        $this->permissionChecker->shouldReceive('canUpdateCurriculumInventoryReport')->andReturn(false);
        $response = $this->voter->vote($token, $entity, [AbstractVoter::CREATE]);
        $this->assertEquals(VoterInterface::ACCESS_DENIED, $response, "Create denied");
    }

    public function testRootCannotCreateEditDeleteSequenceOnFinalizedReport()
    {
        $token = $this->createMockTokenWithRootSessionUser();
        $report = m::mock(CurriculumInventoryReport::class);
        $report->shouldReceive('getExport')->andReturn(m::mock(CurriculumInventoryExport::class));
        $entity = m::mock(CurriculumInventorySequence::class);
        $entity->shouldReceive('getReport')->andReturn($report);
        foreach ([ AbstractVoter::CREATE, AbstractVoter::EDIT, AbstractVoter::DELETE ] as $attr) {
            $response = $this->voter->vote($token, $entity, [ $attr ]);
            $this->assertEquals(VoterInterface::ACCESS_DENIED, $response, "${attr} allowed");
        }
    }

    public function testCannotCreateEditDeleteSequenceOnFinalizedReport()
    {
        $token = $this->createMockTokenWithNonRootSessionUser();
        $report = m::mock(CurriculumInventoryReport::class);
        $report->shouldReceive('getExport')->andReturn(m::mock(CurriculumInventoryExport::class));
        $entity = m::mock(CurriculumInventorySequence::class);
        $entity->shouldReceive('getReport')->andReturn($report);
        foreach ([ AbstractVoter::CREATE, AbstractVoter::EDIT, AbstractVoter::DELETE ] as $attr) {
            $response = $this->voter->vote($token, $entity, [ $attr ]);
            $this->assertEquals(VoterInterface::ACCESS_DENIED, $response, "${attr} allowed");
        }
    }
}
