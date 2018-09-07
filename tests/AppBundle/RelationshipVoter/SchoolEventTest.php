<?php

namespace Tests\AppBundle\RelationshipVoter;

use AppBundle\RelationshipVoter\AbstractVoter;
use AppBundle\RelationshipVoter\SchoolEvent as Voter;
use AppBundle\Service\PermissionChecker;
use AppBundle\Classes\SchoolEvent;
use AppBundle\Service\Config;
use Mockery as m;
use Symfony\Component\Security\Core\Authorization\Voter\VoterInterface;

class SchoolEventTest extends AbstractBase
{
    public function setup()
    {
        $this->permissionChecker = m::mock(PermissionChecker::class);
        $this->voter = new Voter($this->permissionChecker);
    }

    public function testAllowsRootFullAccess()
    {
        $this->checkRootEntityAccess(m::mock(SchoolEvent::class), [AbstractVoter::VIEW]);
    }

    public function testCanViewPublishedSchoolEventInPrimarySchool()
    {
        $primarySchoolId = 1;
        $eventSchoolId = $primarySchoolId;
        $token = $this->createMockTokenWithNonRootSessionUser();
        $entity = m::mock(SchoolEvent::class);
        $sessionUser = $token->getUser();

        $entity->school = $eventSchoolId;
        $entity->isPublished = true;
        $sessionUser->shouldReceive('performsNonLearnerFunction')->andReturn(false);
        $sessionUser->shouldReceive('getSchoolId')->andReturn($primarySchoolId);

        $response = $this->voter->vote($token, $entity, [AbstractVoter::VIEW]);

        $this->assertEquals(VoterInterface::ACCESS_GRANTED, $response, "View allowed");
    }

    public function testCanViewUnpublishedSchoolEventInPrimarySchoolIfUserPerformsNonStudentFunction()
    {
        $primarySchoolId = 1;
        $schoolIds = [2, 3];
        $eventSchoolId = $primarySchoolId;
        $token = $this->createMockTokenWithNonRootSessionUser();
        $entity = m::mock(SchoolEvent::class);
        $sessionUser = $token->getUser();

        $entity->school = $eventSchoolId;
        $entity->isPublished = false;
        $sessionUser->shouldReceive('performsNonLearnerFunction')->andReturn(true);
        $sessionUser->shouldReceive('getSchoolId')->andReturn($primarySchoolId);
        $sessionUser->shouldReceive('getAssociatedSchoolIdsInNonLearnerFunction')->andReturn($schoolIds);

        $response = $this->voter->vote($token, $entity, [AbstractVoter::VIEW]);

        $this->assertEquals(VoterInterface::ACCESS_GRANTED, $response, "View allowed");
    }

    public function testCanViewUnpublishedSchoolEventInAssociatedSchoolIfUserPerformsNonStudentFunction()
    {
        $primarySchoolId = 1;
        $eventSchoolId = 4;
        $schoolIds = [2, 3, $eventSchoolId];
        $token = $this->createMockTokenWithNonRootSessionUser();
        $entity = m::mock(SchoolEvent::class);
        $sessionUser = $token->getUser();

        $entity->school = $eventSchoolId;
        $entity->isPublished = false;
        $sessionUser->shouldReceive('performsNonLearnerFunction')->andReturn(true);
        $sessionUser->shouldReceive('getSchoolId')->andReturn($primarySchoolId);
        $sessionUser->shouldReceive('getAssociatedSchoolIdsInNonLearnerFunction')->andReturn($schoolIds);

        $response = $this->voter->vote($token, $entity, [AbstractVoter::VIEW]);

        $this->assertEquals(VoterInterface::ACCESS_GRANTED, $response, "View allowed");
    }

    public function testCanNotViewUnpublishedSchoolEventInPrimarySchool()
    {
        $schoolId = 1;
        $token = $this->createMockTokenWithNonRootSessionUser();
        $entity = m::mock(SchoolEvent::class);
        $sessionUser = $token->getUser();

        $entity->school = $schoolId;
        $entity->isPublished = false;
        $sessionUser->shouldReceive('performsNonLearnerFunction')->andReturn(false);
        $sessionUser->shouldReceive('getSchoolId')->andReturn($schoolId);

        $response = $this->voter->vote($token, $entity, [AbstractVoter::VIEW]);

        $this->assertEquals(VoterInterface::ACCESS_DENIED, $response, "View denied");
    }

    public function testCanNotViewSchoolEventOutsideOfPrimarySchool()
    {
        $primarySchoolId = 1;
        $eventSchoolId = 4;
        $token = $this->createMockTokenWithNonRootSessionUser();
        $entity = m::mock(SchoolEvent::class);
        $sessionUser = $token->getUser();

        $entity->school = $eventSchoolId;
        $entity->isPublished = true;
        $sessionUser->shouldReceive('performsNonLearnerFunction')->andReturn(false);
        $sessionUser->shouldReceive('getSchoolId')->andReturn($primarySchoolId);

        $response = $this->voter->vote($token, $entity, [AbstractVoter::VIEW]);

        $this->assertEquals(VoterInterface::ACCESS_DENIED, $response, "View denied");
    }

    public function testCanNotViewSchoolEventOutsideOfPrimarySchoolEventIfUserPerformsNonLearnerFunction()
    {
        $primarySchoolId = 1;
        $schoolIds = [2, 3];
        $eventSchoolId = 4;
        $token = $this->createMockTokenWithNonRootSessionUser();
        $entity = m::mock(SchoolEvent::class);
        $sessionUser = $token->getUser();

        $entity->school = $eventSchoolId;
        $entity->isPublished = true;
        $sessionUser->shouldReceive('performsNonLearnerFunction')->andReturn(true);
        $sessionUser->shouldReceive('getSchoolId')->andReturn($primarySchoolId);
        $sessionUser->shouldReceive('getAssociatedSchoolIdsInNonLearnerFunction')->andReturn($schoolIds);

        $response = $this->voter->vote($token, $entity, [AbstractVoter::VIEW]);

        $this->assertEquals(VoterInterface::ACCESS_DENIED, $response, "View denied");
    }
}
