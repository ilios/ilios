<?php

declare(strict_types=1);

namespace App\Tests\RelationshipVoter;

use App\Classes\VoterPermissions;
use App\Entity\CourseInterface;
use App\Entity\CourseLearningMaterialInterface;
use App\Entity\SchoolInterface;
use App\RelationshipVoter\CourseLearningMaterial as Voter;
use App\Service\SessionUserPermissionChecker;
use Mockery as m;
use Symfony\Component\Security\Core\Authorization\Voter\VoterInterface;

final class CourseLearningMaterialTest extends AbstractBase
{
    public function setUp(): void
    {
        parent::setUp();
        $this->permissionChecker = m::mock(SessionUserPermissionChecker::class);
        $this->voter = new Voter($this->permissionChecker);
    }

    public function testAllowsRootFullAccess(): void
    {
        $this->checkRootEntityAccess(m::mock(CourseLearningMaterialInterface::class));
    }

    public function testCanViewUserPerformingNonLearnerFunction(): void
    {
        $user = $this->createMockSessionUserPerformingNonLearnerFunction();
        $token = $this->createMockTokenWithMockSessionUser($user);
        $entity = m::mock(CourseLearningMaterialInterface::class);
        $response = $this->voter->vote($token, $entity, [VoterPermissions::VIEW]);
        $this->assertEquals(VoterInterface::ACCESS_GRANTED, $response, "View allowed");
    }

    public function testCanNotViewAsLearnerOnly(): void
    {
        $user = $this->createMockSessionUserPerformingOnlyLearnerFunction();
        $token = $this->createMockTokenWithMockSessionUser($user);
        $entity = m::mock(CourseLearningMaterialInterface::class);
        $response = $this->voter->vote($token, $entity, [VoterPermissions::VIEW]);
        $this->assertEquals(VoterInterface::ACCESS_DENIED, $response, "View allowed");
    }

    public function testCanEdit(): void
    {
        $user = $this->createMockNonRootSessionUser();
        $token = $this->createMockTokenWithMockSessionUser($user);
        $entity = m::mock(CourseLearningMaterialInterface::class);
        $course = m::mock(CourseInterface::class);
        $course->shouldReceive('getId')->andReturn(1);
        $school = m::mock(SchoolInterface::class);
        $entity->shouldReceive('getCourse')->andReturn($course);
        $school->shouldReceive('getId')->andReturn(1);
        $course->shouldReceive('getSchool')->andReturn($school);
        $this->permissionChecker->shouldReceive('canUpdateCourse')->andReturn(true);
        $response = $this->voter->vote($token, $entity, [VoterPermissions::EDIT]);
        $this->assertEquals(VoterInterface::ACCESS_GRANTED, $response, "Edit allowed");
    }

    public function testCanNotEdit(): void
    {
        $user = $this->createMockNonRootSessionUser();
        $token = $this->createMockTokenWithMockSessionUser($user);
        $entity = m::mock(CourseLearningMaterialInterface::class);
        $course = m::mock(CourseInterface::class);
        $course->shouldReceive('getId')->andReturn(1);
        $school = m::mock(SchoolInterface::class);
        $school->shouldReceive('getId')->andReturn(1);
        $entity->shouldReceive('getCourse')->andReturn($course);
        $course->shouldReceive('getSchool')->andReturn($school);
        $this->permissionChecker->shouldReceive('canUpdateCourse')->andReturn(false);
        $response = $this->voter->vote($token, $entity, [VoterPermissions::EDIT]);
        $this->assertEquals(VoterInterface::ACCESS_DENIED, $response, "Edit denied");
    }

    public function testCanDelete(): void
    {
        $user = $this->createMockNonRootSessionUser();
        $token = $this->createMockTokenWithMockSessionUser($user);
        $entity = m::mock(CourseLearningMaterialInterface::class);
        $course = m::mock(CourseInterface::class);
        $course->shouldReceive('getId')->andReturn(1);
        $school = m::mock(SchoolInterface::class);
        $school->shouldReceive('getId')->andReturn(1);
        $entity->shouldReceive('getCourse')->andReturn($course);
        $course->shouldReceive('getSchool')->andReturn($school);
        $this->permissionChecker->shouldReceive('canUpdateCourse')->andReturn(true);
        $response = $this->voter->vote($token, $entity, [VoterPermissions::DELETE]);
        $this->assertEquals(VoterInterface::ACCESS_GRANTED, $response, "Delete allowed");
    }

    public function testCanNotDelete(): void
    {
        $user = $this->createMockNonRootSessionUser();
        $token = $this->createMockTokenWithMockSessionUser($user);
        $entity = m::mock(CourseLearningMaterialInterface::class);
        $course = m::mock(CourseInterface::class);
        $course->shouldReceive('getId')->andReturn(1);
        $school = m::mock(SchoolInterface::class);
        $school->shouldReceive('getId')->andReturn(1);
        $entity->shouldReceive('getCourse')->andReturn($course);
        $course->shouldReceive('getSchool')->andReturn($school);
        $this->permissionChecker->shouldReceive('canUpdateCourse')->andReturn(false);
        $response = $this->voter->vote($token, $entity, [VoterPermissions::DELETE]);
        $this->assertEquals(VoterInterface::ACCESS_DENIED, $response, "Delete denied");
    }

    public function testCanCreate(): void
    {
        $user = $this->createMockNonRootSessionUser();
        $token = $this->createMockTokenWithMockSessionUser($user);
        $entity = m::mock(CourseLearningMaterialInterface::class);
        $course = m::mock(CourseInterface::class);
        $course->shouldReceive('getId')->andReturn(1);
        $school = m::mock(SchoolInterface::class);
        $school->shouldReceive('getId')->andReturn(1);
        $entity->shouldReceive('getCourse')->andReturn($course);
        $course->shouldReceive('getSchool')->andReturn($school);
        $this->permissionChecker->shouldReceive('canUpdateCourse')->andReturn(true);
        $response = $this->voter->vote($token, $entity, [VoterPermissions::CREATE]);
        $this->assertEquals(VoterInterface::ACCESS_GRANTED, $response, "Create allowed");
    }

    public function testCanNotCreate(): void
    {
        $user = $this->createMockNonRootSessionUser();
        $token = $this->createMockTokenWithMockSessionUser($user);
        $entity = m::mock(CourseLearningMaterialInterface::class);
        $course = m::mock(CourseInterface::class);
        $course->shouldReceive('getId')->andReturn(1);
        $school = m::mock(SchoolInterface::class);
        $school->shouldReceive('getId')->andReturn(1);
        $entity->shouldReceive('getCourse')->andReturn($course);
        $course->shouldReceive('getSchool')->andReturn($school);
        $this->permissionChecker->shouldReceive('canUpdateCourse')->andReturn(false);
        $response = $this->voter->vote($token, $entity, [VoterPermissions::CREATE]);
        $this->assertEquals(VoterInterface::ACCESS_DENIED, $response, "Create denied");
    }

    public static function supportsTypeProvider(): array
    {
        return [
            [CourseLearningMaterialInterface::class, true],
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
