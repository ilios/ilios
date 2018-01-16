<?php
namespace Tests\AuthenticationBundle\RelationshipVoter;

use Ilios\AuthenticationBundle\RelationshipVoter\AbstractVoter;
use Ilios\AuthenticationBundle\RelationshipVoter\School as Voter;
use Ilios\AuthenticationBundle\Service\PermissionChecker;
use Ilios\CoreBundle\Entity\School;
use Ilios\CoreBundle\Entity\DTO\SchoolDTO;
use Mockery as m;
use Symfony\Component\Security\Core\Authorization\Voter\VoterInterface;

class SchoolTest extends AbstractBase
{
    public function setup()
    {
        $this->permissionChecker = m::mock(PermissionChecker::class);
        $this->voter = new Voter($this->permissionChecker, true);
    }

    public function testAllowsRootFullAccess()
    {
        $this->checkRootEntityAccess(School::class);
    }

    public function testCanView()
    {
        $token = $this->createMockTokenWithNonRootSessionUser();
        $entity = m::mock(School::class);
        $response = $this->voter->vote($token, $entity, [AbstractVoter::VIEW]);
        $this->assertEquals(VoterInterface::ACCESS_GRANTED, $response, "View allowed");
    }

    public function testCanEdit()
    {
        $token = $this->createMockTokenWithNonRootSessionUser();
        $entity = m::mock(School::class);
        $entity->shouldReceive('getId')->andReturn(1);
        $entity->shouldReceive('getSchool')->andReturn(1);
        $this->permissionChecker->shouldReceive('canUpdateSchool')->andReturn(true);
        $response = $this->voter->vote($token, $entity, [AbstractVoter::EDIT]);
        $this->assertEquals(VoterInterface::ACCESS_GRANTED, $response, "Edit allowed");
    }

    public function testCanNotEdit()
    {
        $token = $this->createMockTokenWithNonRootSessionUser();
        $entity = m::mock(School::class);
        $entity->shouldReceive('getId')->andReturn(1);
        $entity->shouldReceive('getSchool')->andReturn(1);
        $this->permissionChecker->shouldReceive('canUpdateSchool')->andReturn(false);
        $response = $this->voter->vote($token, $entity, [AbstractVoter::EDIT]);
        $this->assertEquals(VoterInterface::ACCESS_DENIED, $response, "Edit denied");
    }

    public function testCanDelete()
    {
        $token = $this->createMockTokenWithNonRootSessionUser();
        $entity = m::mock(School::class);
        $entity->shouldReceive('getId')->andReturn(1);
        $entity->shouldReceive('getSchool')->andReturn(1);
        $this->permissionChecker->shouldReceive('canDeleteSchool')->andReturn(true);
        $response = $this->voter->vote($token, $entity, [AbstractVoter::DELETE]);
        $this->assertEquals(VoterInterface::ACCESS_GRANTED, $response, "Delete allowed");
    }

    public function testCanNotDelete()
    {
        $token = $this->createMockTokenWithNonRootSessionUser();
        $entity = m::mock(School::class);
        $entity->shouldReceive('getId')->andReturn(1);
        $entity->shouldReceive('getSchool')->andReturn(1);
        $this->permissionChecker->shouldReceive('canDeleteSchool')->andReturn(false);
        $response = $this->voter->vote($token, $entity, [AbstractVoter::DELETE]);
        $this->assertEquals(VoterInterface::ACCESS_DENIED, $response, "Delete denied");
    }

    public function testCanNotCreate()
    {
        $token = $this->createMockTokenWithNonRootSessionUser();
        $entity = m::mock(School::class);
        $response = $this->voter->vote($token, $entity, [AbstractVoter::CREATE]);
        $this->assertEquals(VoterInterface::ACCESS_DENIED, $response, "Create denied");
    }
}
