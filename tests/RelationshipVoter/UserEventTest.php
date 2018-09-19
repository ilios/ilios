<?php

namespace Tests\App\RelationshipVoter;

use App\RelationshipVoter\AbstractVoter;
use App\RelationshipVoter\UserEvent as Voter;
use App\Service\PermissionChecker;
use App\Classes\UserEvent;
use App\Service\Config;
use Mockery as m;
use Symfony\Component\Security\Core\Authorization\Voter\VoterInterface;

class UserEventTest extends AbstractBase
{
    public function setup()
    {
        $this->permissionChecker = m::mock(PermissionChecker::class);
        $this->voter = new Voter($this->permissionChecker);
    }

    public function testAllowsRootFullAccess()
    {
        $this->checkRootEntityAccess(m::mock(UserEvent::class), [AbstractVoter::VIEW]);
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

        $this->assertEquals(VoterInterface::ACCESS_GRANTED, $response, "View allowed");
    }

    public function testCanViewOwnUnpublishedEventsIfUserPerformsNonStudentFunction()
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

        $this->assertEquals(VoterInterface::ACCESS_GRANTED, $response, "View allowed");
    }


    public function testCanViewOtherPublishedEventsIfUserPerformsNonStudentFunction()
    {
        $token = $this->createMockTokenWithNonRootSessionUser();
        $entity = m::mock(UserEvent::class);
        $sessionUser = $token->getUser();

        $entity->isPublished = true;
        $sessionUser->shouldReceive('performsNonLearnerFunction')->andReturn(true);

        $response = $this->voter->vote($token, $entity, [AbstractVoter::VIEW]);

        $this->assertEquals(VoterInterface::ACCESS_GRANTED, $response, "View allowed");
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

    public function testCanNotViewOtherUsersUnpublishedEventsEvenIfUserPerformsNonStudentFunction()
    {
        $userId = 1;
        $otherUserId = 2;
        $token = $this->createMockTokenWithNonRootSessionUser();
        $entity = m::mock(UserEvent::class);
        $sessionUser = $token->getUser();

        $entity->user = $otherUserId;
        $entity->isPublished = false;
        $sessionUser->shouldReceive('performsNonLearnerFunction')->andReturn(true);
        $sessionUser->shouldReceive('getId')->andReturn($userId);

        $response = $this->voter->vote($token, $entity, [AbstractVoter::VIEW]);

        $this->assertEquals(VoterInterface::ACCESS_DENIED, $response, "View denied");
    }
}
