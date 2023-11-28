<?php

declare(strict_types=1);

namespace App\Tests\RelationshipVoter;

use App\Classes\SessionUserInterface;
use App\Classes\VoterPermissions;
use App\Entity\CourseInterface;
use App\Entity\SchoolInterface;
use App\Entity\SessionInterface;
use App\Entity\SessionLearningMaterialInterface;
use App\RelationshipVoter\SessionLearningMaterial as Voter;
use App\Service\SessionUserPermissionChecker;
use DateTime;
use Mockery as m;
use Symfony\Component\Security\Core\Authorization\Voter\VoterInterface;

class SessionLearningMaterialTest extends AbstractBase
{
    public function setUp(): void
    {
        parent::setUp();
        $this->permissionChecker = m::mock(SessionUserPermissionChecker::class);
        $this->voter = new Voter($this->permissionChecker);
    }

    public function testAllowsRootFullAccess()
    {
        $this->checkRootEntityAccess(m::mock(SessionLearningMaterialInterface::class));
    }

    public function testCanViewUserPerformingNonLearnerFunction()
    {
        $token = $this->createMockTokenWithSessionUserPerformingNonLearnerFunction();
        $entity = m::mock(SessionLearningMaterialInterface::class);
        $response = $this->voter->vote($token, $entity, [VoterPermissions::VIEW]);
        $this->assertEquals(VoterInterface::ACCESS_GRANTED, $response, "View allowed");
    }

    public function testCanNotViewAsLearnerOnly()
    {
        $sessionUser = m::mock(SessionUserInterface::class);
        $sessionUser->shouldReceive('isRoot')->andReturn(false);
        $sessionUser->shouldReceive('performsNonLearnerFunction')->andReturn(false);
        $sessionUser->shouldReceive('isLearnerInSession')->andReturn(false);
        $token = $this->createMockTokenWithSessionUser($sessionUser);
        $entity = m::mock(SessionLearningMaterialInterface::class);
        $session = m::mock(SessionInterface::class);
        $session->shouldReceive('getId')->andReturn(13);
        $entity->shouldReceive('getStartDate')->andReturn(null);
        $entity->shouldReceive('getEndDate')->andReturn(null);
        $entity->shouldReceive('getSession')->andReturn($session);
        $response = $this->voter->vote($token, $entity, [VoterPermissions::VIEW]);
        $this->assertEquals(VoterInterface::ACCESS_DENIED, $response, "View allowed");
    }

    public function testCanNotViewAsLearnerBeforeStartDate()
    {
        $sessionUser = m::mock(SessionUserInterface::class);
        $sessionUser->shouldReceive('isRoot')->andReturn(false);
        $sessionUser->shouldReceive('performsNonLearnerFunction')->andReturn(false);
        $sessionUser->shouldReceive('isLearnerInSession')->andReturn(true);
        $token = $this->createMockTokenWithSessionUser($sessionUser);
        $entity = m::mock(SessionLearningMaterialInterface::class);
        $session = m::mock(SessionInterface::class);
        $session->shouldReceive('getId')->andReturn(13);
        $entity->shouldReceive('getStartDate')->andReturn(new DateTime('tomorrow'));
        $entity->shouldReceive('getEndDate')->andReturn(null);
        $entity->shouldReceive('getSession')->andReturn($session);
        $response = $this->voter->vote($token, $entity, [VoterPermissions::VIEW]);
        $this->assertEquals(VoterInterface::ACCESS_DENIED, $response, "View allowed");
    }

    public function testCanNotViewAsLearnerAfterEndDate()
    {
        $sessionUser = m::mock(SessionUserInterface::class);
        $sessionUser->shouldReceive('isRoot')->andReturn(false);
        $sessionUser->shouldReceive('performsNonLearnerFunction')->andReturn(false);
        $sessionUser->shouldReceive('isLearnerInSession')->andReturn(true);
        $token = $this->createMockTokenWithSessionUser($sessionUser);
        $entity = m::mock(SessionLearningMaterialInterface::class);
        $session = m::mock(SessionInterface::class);
        $session->shouldReceive('getId')->andReturn(13);
        $entity->shouldReceive('getStartDate')->andReturn(null);
        $entity->shouldReceive('getEndDate')->andReturn(new DateTime('yesterday'));
        $entity->shouldReceive('getSession')->andReturn($session);
        $response = $this->voter->vote($token, $entity, [VoterPermissions::VIEW]);
        $this->assertEquals(VoterInterface::ACCESS_DENIED, $response, "View allowed");
    }

    public function testCanViewAsLearnerInSession()
    {
        $sessionUser = m::mock(SessionUserInterface::class);
        $sessionUser->shouldReceive('isRoot')->andReturn(false);
        $sessionUser->shouldReceive('performsNonLearnerFunction')->andReturn(false);
        $sessionUser->shouldReceive('isLearnerInSession')->andReturn(true);
        $token = $this->createMockTokenWithSessionUser($sessionUser);
        $entity = m::mock(SessionLearningMaterialInterface::class);
        $session = m::mock(SessionInterface::class);
        $session->shouldReceive('getId')->andReturn(13);
        $entity->shouldReceive('getStartDate')->andReturn(null);
        $entity->shouldReceive('getEndDate')->andReturn(null);
        $entity->shouldReceive('getSession')->andReturn($session);
        $response = $this->voter->vote($token, $entity, [VoterPermissions::VIEW]);
        $this->assertEquals(VoterInterface::ACCESS_GRANTED, $response, "View allowed");
    }

    public function testCanEdit()
    {
        $token = $this->createMockTokenWithNonRootSessionUser();
        $entity = m::mock(SessionLearningMaterialInterface::class);
        $session = m::mock(SessionInterface::class);
        $session->shouldReceive('getId')->andReturn(1);
        $course = m::mock(CourseInterface::class);
        $course->shouldReceive('getId')->andReturn(1);
        $school = m::mock(SchoolInterface::class);
        $school->shouldReceive('getId')->andReturn(1);
        $entity->shouldReceive('getSession')->andReturn($session);
        $session->shouldReceive('getCourse')->andReturn($course);
        $course->shouldReceive('getSchool')->andReturn($school);
        $this->permissionChecker->shouldReceive('canUpdateSession')->andReturn(true);
        $response = $this->voter->vote($token, $entity, [VoterPermissions::EDIT]);
        $this->assertEquals(VoterInterface::ACCESS_GRANTED, $response, "Edit allowed");
    }

    public function testCanNotEdit()
    {
        $token = $this->createMockTokenWithNonRootSessionUser();
        $entity = m::mock(SessionLearningMaterialInterface::class);
        $session = m::mock(SessionInterface::class);
        $session->shouldReceive('getId')->andReturn(1);
        $course = m::mock(CourseInterface::class);
        $course->shouldReceive('getId')->andReturn(1);
        $school = m::mock(SchoolInterface::class);
        $school->shouldReceive('getId')->andReturn(1);
        $entity->shouldReceive('getSession')->andReturn($session);
        $session->shouldReceive('getCourse')->andReturn($course);
        $course->shouldReceive('getSchool')->andReturn($school);
        $this->permissionChecker->shouldReceive('canUpdateSession')->andReturn(false);
        $response = $this->voter->vote($token, $entity, [VoterPermissions::EDIT]);
        $this->assertEquals(VoterInterface::ACCESS_DENIED, $response, "Edit denied");
    }

    public function testCanDelete()
    {
        $token = $this->createMockTokenWithNonRootSessionUser();
        $entity = m::mock(SessionLearningMaterialInterface::class);
        $session = m::mock(SessionInterface::class);
        $session->shouldReceive('getId')->andReturn(1);
        $course = m::mock(CourseInterface::class);
        $course->shouldReceive('getId')->andReturn(1);
        $school = m::mock(SchoolInterface::class);
        $school->shouldReceive('getId')->andReturn(1);
        $entity->shouldReceive('getSession')->andReturn($session);
        $session->shouldReceive('getCourse')->andReturn($course);
        $course->shouldReceive('getSchool')->andReturn($school);
        $this->permissionChecker->shouldReceive('canUpdateSession')->andReturn(true);
        $response = $this->voter->vote($token, $entity, [VoterPermissions::DELETE]);
        $this->assertEquals(VoterInterface::ACCESS_GRANTED, $response, "Delete allowed");
    }

    public function testCanNotDelete()
    {
        $token = $this->createMockTokenWithNonRootSessionUser();
        $entity = m::mock(SessionLearningMaterialInterface::class);
        $session = m::mock(SessionInterface::class);
        $session->shouldReceive('getId')->andReturn(1);
        $course = m::mock(CourseInterface::class);
        $course->shouldReceive('getId')->andReturn(1);
        $school = m::mock(SchoolInterface::class);
        $school->shouldReceive('getId')->andReturn(1);
        $entity->shouldReceive('getSession')->andReturn($session);
        $session->shouldReceive('getCourse')->andReturn($course);
        $course->shouldReceive('getSchool')->andReturn($school);
        $this->permissionChecker->shouldReceive('canUpdateSession')->andReturn(false);
        $response = $this->voter->vote($token, $entity, [VoterPermissions::DELETE]);
        $this->assertEquals(VoterInterface::ACCESS_DENIED, $response, "Delete denied");
    }

    public function testCanCreate()
    {
        $token = $this->createMockTokenWithNonRootSessionUser();
        $entity = m::mock(SessionLearningMaterialInterface::class);
        $session = m::mock(SessionInterface::class);
        $session->shouldReceive('getId')->andReturn(1);
        $course = m::mock(CourseInterface::class);
        $course->shouldReceive('getId')->andReturn(1);
        $school = m::mock(SchoolInterface::class);
        $school->shouldReceive('getId')->andReturn(1);
        $entity->shouldReceive('getSession')->andReturn($session);
        $session->shouldReceive('getCourse')->andReturn($course);
        $course->shouldReceive('getSchool')->andReturn($school);
        $this->permissionChecker->shouldReceive('canUpdateSession')->andReturn(true);
        $response = $this->voter->vote($token, $entity, [VoterPermissions::CREATE]);
        $this->assertEquals(VoterInterface::ACCESS_GRANTED, $response, "Create allowed");
    }

    public function testCanNotCreate()
    {
        $token = $this->createMockTokenWithNonRootSessionUser();
        $entity = m::mock(SessionLearningMaterialInterface::class);
        $session = m::mock(SessionInterface::class);
        $session->shouldReceive('getId')->andReturn(1);
        $course = m::mock(CourseInterface::class);
        $course->shouldReceive('getId')->andReturn(1);
        $school = m::mock(SchoolInterface::class);
        $school->shouldReceive('getId')->andReturn(1);
        $entity->shouldReceive('getSession')->andReturn($session);
        $session->shouldReceive('getCourse')->andReturn($course);
        $course->shouldReceive('getSchool')->andReturn($school);
        $this->permissionChecker->shouldReceive('canUpdateSession')->andReturn(false);
        $response = $this->voter->vote($token, $entity, [VoterPermissions::CREATE]);
        $this->assertEquals(VoterInterface::ACCESS_DENIED, $response, "Create denied");
    }

    public function supportsTypeProvider(): array
    {
        return [
            [SessionLearningMaterialInterface::class, true],
            [self::class, false],
        ];
    }

    public function supportsAttributesProvider(): array
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
