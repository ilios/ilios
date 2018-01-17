<?php

namespace Tests\AuthenticationBundle\RelationshipVoter;

use Ilios\AuthenticationBundle\RelationshipVoter\AbstractVoter;
use Ilios\AuthenticationBundle\RelationshipVoter\UserEvent as Voter;
use Ilios\AuthenticationBundle\Service\PermissionChecker;

use Ilios\CoreBundle\Classes\UserEvent;
use Mockery as m;
use Symfony\Component\Security\Core\Authorization\Voter\VoterInterface;

class UserEventTest extends AbstractBase
{
    public function setup()
    {
        $this->permissionChecker = m::mock(PermissionChecker::class);
        $this->voter = new Voter($this->permissionChecker, true);
    }

    public function testAllowsRootFullAccess()
    {
        $this->checkRootEntityAccess(UserEvent::class, [AbstractVoter::VIEW]);
    }

    public function testCanViewOwnPublishedEvents()
    {
        $userId = 1;
        $token = $this->createMockTokenWithNonRootSessionUser();
        $entity = m::mock(UserEvent::class);
        $sessionUser = $token->getUser();

        $entity->user = $userId;
        $entity->isPublished = true;
        $sessionUser->shouldReceive('performsNonLearnerFunction')->andReturn(false);
        $sessionUser->shouldReceive('getId')->andReturn($userId);

        $response = $this->voter->vote($token, $entity, [AbstractVoter::VIEW]);

        $this->assertEquals(VoterInterface::ACCESS_GRANTED, $response, "View granted");
    }

    public function testCanViewOOwnUnpublishedEventsIfUserPerformsNonStudentFunction()
    {
        $userId = 1;
        $token = $this->createMockTokenWithNonRootSessionUser();
        $entity = m::mock(UserEvent::class);
        $sessionUser = $token->getUser();

        $entity->user = $userId;
        $entity->isPublished = false;
        $sessionUser->shouldReceive('performsNonLearnerFunction')->andReturn(true);
        $sessionUser->shouldReceive('getId')->andReturn($userId);

        $response = $this->voter->vote($token, $entity, [AbstractVoter::VIEW]);

        $this->assertEquals(VoterInterface::ACCESS_GRANTED, $response, "View granted");
    }

    public function testCanNotViewOtherUsersEvents()
    {
        $userId = 1;
        $otherUserId = 2;
        $token = $this->createMockTokenWithNonRootSessionUser();
        $entity = m::mock(UserEvent::class);
        $sessionUser = $token->getUser();

        $entity->user = $otherUserId;
        $entity->isPublished = true;
        $sessionUser->shouldReceive('performsNonLearnerFunction')->andReturn(false);
        $sessionUser->shouldReceive('getId')->andReturn($userId);

        $response = $this->voter->vote($token, $entity, [AbstractVoter::VIEW]);

        $this->assertEquals(VoterInterface::ACCESS_DENIED, $response, "View denied");

    }

    public function testCanNotViewOtherUsersEventsEvenIfUserPerformsNonStudentFunction()
    {
        $userId = 1;
        $otherUserId = 2;
        $token = $this->createMockTokenWithNonRootSessionUser();
        $entity = m::mock(UserEvent::class);
        $sessionUser = $token->getUser();

        $entity->user = $otherUserId;
        $entity->isPublished = true;
        $sessionUser->shouldReceive('performsNonLearnerFunction')->andReturn(true);
        $sessionUser->shouldReceive('getId')->andReturn($userId);

        $response = $this->voter->vote($token, $entity, [AbstractVoter::VIEW]);

        $this->assertEquals(VoterInterface::ACCESS_DENIED, $response, "View denied");
    }
}


