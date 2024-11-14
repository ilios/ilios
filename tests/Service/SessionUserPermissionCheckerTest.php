<?php

declare(strict_types=1);

namespace App\Tests\Service;

use App\Classes\Capabilities;
use App\Classes\PermissionMatrixInterface;
use App\Classes\SessionUserInterface;
use App\Entity\CourseInterface;
use App\Entity\ProgramInterface;
use App\Entity\ProgramYearInterface;
use App\Entity\SchoolInterface;
use App\Entity\SessionInterface;
use App\Service\SessionUserPermissionChecker;
use App\Tests\TestCase;
use Mockery as m;

/**
 * Class PermissionCheckerTest
 * @package App\Tests\Service
 */
#[\PHPUnit\Framework\Attributes\CoversClass(\App\Service\SessionUserPermissionChecker::class)]
class SessionUserPermissionCheckerTest extends TestCase
{
    protected SessionUserPermissionChecker $permissionChecker;
    protected m\MockInterface $permissionMatrix;

    public function setUp(): void
    {
        parent::setUp();
        $this->permissionMatrix = m::mock(PermissionMatrixInterface::class);
        $this->permissionChecker = new SessionUserPermissionChecker($this->permissionMatrix);
    }

    public function tearDown(): void
    {
        parent::tearDown();
        unset($this->permissionChecker);
        unset($this->permissionMatrix);
    }

    public function testCanUpdateAllCourses(): void
    {
        $rolesInSchool = ['foo'];
        $courseId = 10;
        $schoolId = 20;
        $school = m::mock(SchoolInterface::class);
        $course = m::mock(CourseInterface::class);
        $sessionUser = m::mock(SessionUserInterface::class);

        $school->shouldReceive('getId')->andReturn($schoolId);
        $course->shouldReceive('getSchool')->andReturn($school);
        $course->shouldReceive('getId')->andReturn($courseId);
        $course->shouldReceive('isArchived')->andReturn(false);
        $course->shouldReceive('isLocked')->andReturn(false);
        $sessionUser->shouldReceive('isRoot')->andReturn(false);
        $sessionUser->shouldReceive('rolesInSchool')->andReturn($rolesInSchool);
        $this->permissionMatrix
            ->shouldReceive('getPermittedRoles')
            ->withArgs([$schoolId, Capabilities::CAN_UPDATE_ALL_COURSES])
            ->andReturn([]);
        $this->permissionMatrix
            ->shouldReceive('hasPermission')
            ->withArgs([$schoolId, Capabilities::CAN_UPDATE_ALL_COURSES, $rolesInSchool])
            ->andReturn(true);

        $this->assertTrue($this->permissionChecker->canUpdateCourse($sessionUser, $course));
    }

    public function testCanUpdateTheirCourses(): void
    {
        $rolesInSchool  = ['foo'];
        $rolesInCourse = ['bar'];
        $courseId = 10;
        $schoolId = 20;
        $school = m::mock(SchoolInterface::class);
        $course = m::mock(CourseInterface::class);
        $sessionUser = m::mock(SessionUserInterface::class);

        $school->shouldReceive('getId')->andReturn($schoolId);
        $course->shouldReceive('getSchool')->andReturn($school);
        $course->shouldReceive('getId')->andReturn($courseId);
        $course->shouldReceive('isArchived')->andReturn(false);
        $course->shouldReceive('isLocked')->andReturn(false);
        $sessionUser->shouldReceive('isRoot')->andReturn(false);
        $sessionUser->shouldReceive('rolesInSchool')->andReturn($rolesInSchool);
        $sessionUser->shouldReceive('rolesInCourse')->andReturn($rolesInCourse);

        $this->permissionMatrix
            ->shouldReceive('getPermittedRoles')
            ->withArgs([$schoolId, Capabilities::CAN_UPDATE_ALL_COURSES])
            ->andReturn([]);
        $this->permissionMatrix
            ->shouldReceive('hasPermission')
            ->withArgs([$schoolId, Capabilities::CAN_UPDATE_ALL_COURSES, $rolesInSchool])
            ->andReturn(false);
        $this->permissionMatrix
            ->shouldReceive('getPermittedRoles')
            ->withArgs([$schoolId, Capabilities::CAN_UPDATE_THEIR_COURSES])
            ->andReturn([]);
        $this->permissionMatrix
            ->shouldReceive('hasPermission')
            ->withArgs([$schoolId, Capabilities::CAN_UPDATE_THEIR_COURSES, $rolesInCourse])
            ->andReturn(true);

        $this->assertTrue($this->permissionChecker->canUpdateCourse($sessionUser, $course));
    }

    public function testCanNotUpdateCourses(): void
    {
        $rolesInSchool  = ['foo'];
        $rolesInCourse = ['bar'];
        $courseId = 10;
        $schoolId = 20;
        $school = m::mock(SchoolInterface::class);
        $course = m::mock(CourseInterface::class);
        $sessionUser = m::mock(SessionUserInterface::class);

        $school->shouldReceive('getId')->andReturn($schoolId);
        $course->shouldReceive('getSchool')->andReturn($school);
        $course->shouldReceive('getId')->andReturn($courseId);
        $course->shouldReceive('isArchived')->andReturn(false);
        $course->shouldReceive('isLocked')->andReturn(false);
        $sessionUser->shouldReceive('isRoot')->andReturn(false);
        $sessionUser->shouldReceive('rolesInSchool')->andReturn($rolesInSchool);
        $sessionUser->shouldReceive('rolesInCourse')->andReturn($rolesInCourse);

        $this->permissionMatrix
            ->shouldReceive('getPermittedRoles')
            ->withArgs([$schoolId, Capabilities::CAN_UPDATE_ALL_COURSES])
            ->andReturn([]);
        $this->permissionMatrix
            ->shouldReceive('hasPermission')
            ->withArgs([$schoolId, Capabilities::CAN_UPDATE_ALL_COURSES, $rolesInSchool])
            ->andReturn(false);
        $this->permissionMatrix
            ->shouldReceive('getPermittedRoles')
            ->withArgs([$schoolId, Capabilities::CAN_UPDATE_THEIR_COURSES])
            ->andReturn([]);
        $this->permissionMatrix
            ->shouldReceive('hasPermission')
            ->withArgs([$schoolId, Capabilities::CAN_UPDATE_THEIR_COURSES, $rolesInCourse])
            ->andReturn(false);

        $this->assertFalse($this->permissionChecker->canUpdateCourse($sessionUser, $course));
    }

    public function testCanNotUpdateLockedCourses(): void
    {
        $course = m::mock(CourseInterface::class);
        $sessionUser = m::mock(SessionUserInterface::class);

        $course->shouldReceive('isArchived')->andReturn(false);
        $course->shouldReceive('isLocked')->andReturn(true);

        $this->assertFalse($this->permissionChecker->canUpdateCourse($sessionUser, $course));
    }

    public function testCanNotUpdateArchivedCourses(): void
    {
        $course = m::mock(CourseInterface::class);
        $sessionUser = m::mock(SessionUserInterface::class);

        $course->shouldReceive('isArchived')->andReturn(true);
        $course->shouldReceive('isLocked')->andReturn(false);

        $this->assertFalse($this->permissionChecker->canUpdateCourse($sessionUser, $course));
    }

    public function testCanDeleteAllCourses(): void
    {
        $rolesInSchool = ['foo'];
        $courseId = 20;
        $schoolId = 10;
        $school = m::mock(SchoolInterface::class);
        $course = m::mock(CourseInterface::class);
        $sessionUser = m::mock(SessionUserInterface::class);

        $school->shouldReceive('getId')->andReturn($schoolId);
        $course->shouldReceive('getSchool')->andReturn($school);
        $course->shouldReceive('getId')->andReturn($courseId);
        $course->shouldReceive('isArchived')->andReturn(false);
        $course->shouldReceive('isLocked')->andReturn(false);
        $sessionUser->shouldReceive('isRoot')->andReturn(false);
        $sessionUser->shouldReceive('rolesInSchool')->andReturn($rolesInSchool);

        $this->permissionMatrix
            ->shouldReceive('getPermittedRoles')
            ->withArgs([$schoolId, Capabilities::CAN_DELETE_ALL_COURSES])
            ->andReturn([]);
        $this->permissionMatrix
            ->shouldReceive('hasPermission')
            ->withArgs([$schoolId, Capabilities::CAN_DELETE_ALL_COURSES, $rolesInSchool])
            ->andReturn(true);

        $this->assertTrue($this->permissionChecker->canDeleteCourse($sessionUser, $course));
    }

    public function testCanDeleteTheirCourses(): void
    {
        $rolesInSchool  = ['foo'];
        $rolesInCourse = ['bar'];
        $courseId = 20;
        $schoolId = 10;
        $school = m::mock(SchoolInterface::class);
        $course = m::mock(CourseInterface::class);
        $sessionUser = m::mock(SessionUserInterface::class);

        $school->shouldReceive('getId')->andReturn($schoolId);
        $course->shouldReceive('getSchool')->andReturn($school);
        $course->shouldReceive('getId')->andReturn($courseId);
        $course->shouldReceive('isArchived')->andReturn(false);
        $course->shouldReceive('isLocked')->andReturn(false);
        $sessionUser->shouldReceive('isRoot')->andReturn(false);
        $sessionUser->shouldReceive('rolesInSchool')->andReturn($rolesInSchool);
        $sessionUser->shouldReceive('rolesInCourse')->andReturn($rolesInCourse);

        $this->permissionMatrix
            ->shouldReceive('getPermittedRoles')
            ->withArgs([$schoolId, Capabilities::CAN_DELETE_ALL_COURSES])
            ->andReturn([]);
        $this->permissionMatrix
            ->shouldReceive('hasPermission')
            ->withArgs([$schoolId, Capabilities::CAN_DELETE_ALL_COURSES, $rolesInSchool])
            ->andReturn(false);
        $this->permissionMatrix
            ->shouldReceive('getPermittedRoles')
            ->withArgs([$schoolId, Capabilities::CAN_DELETE_THEIR_COURSES])
            ->andReturn([]);
        $this->permissionMatrix
            ->shouldReceive('hasPermission')
            ->withArgs([$schoolId, Capabilities::CAN_DELETE_THEIR_COURSES, $rolesInCourse])
            ->andReturn(true);

        $this->assertTrue($this->permissionChecker->canDeleteCourse($sessionUser, $course));
    }

    public function testCanNotDeleteCourses(): void
    {
        $rolesInSchool  = ['foo'];
        $rolesInCourse = ['bar'];
        $courseId = 20;
        $schoolId = 10;
        $school = m::mock(SchoolInterface::class);
        $course = m::mock(CourseInterface::class);
        $sessionUser = m::mock(SessionUserInterface::class);

        $school->shouldReceive('getId')->andReturn($schoolId);
        $course->shouldReceive('getSchool')->andReturn($school);
        $course->shouldReceive('getId')->andReturn($courseId);
        $course->shouldReceive('isArchived')->andReturn(false);
        $course->shouldReceive('isLocked')->andReturn(false);
        $sessionUser->shouldReceive('isRoot')->andReturn(false);
        $sessionUser->shouldReceive('rolesInSchool')->andReturn($rolesInSchool);
        $sessionUser->shouldReceive('rolesInCourse')->andReturn($rolesInCourse);

        $this->permissionMatrix
            ->shouldReceive('getPermittedRoles')
            ->withArgs([$schoolId, Capabilities::CAN_DELETE_ALL_COURSES])
            ->andReturn([]);
        $this->permissionMatrix
            ->shouldReceive('hasPermission')
            ->withArgs([$schoolId, Capabilities::CAN_DELETE_ALL_COURSES, $rolesInSchool])
            ->andReturn(false);
        $this->permissionMatrix
            ->shouldReceive('getPermittedRoles')
            ->withArgs([$schoolId, Capabilities::CAN_DELETE_THEIR_COURSES])
            ->andReturn([]);
        $this->permissionMatrix
            ->shouldReceive('hasPermission')
            ->withArgs([$schoolId, Capabilities::CAN_DELETE_THEIR_COURSES, $rolesInCourse])
            ->andReturn(false);

        $this->assertFalse($this->permissionChecker->canDeleteCourse($sessionUser, $course));
    }

    public function testCanNotDeleteLockedCourses(): void
    {
        $course = m::mock(CourseInterface::class);
        $sessionUser = m::mock(SessionUserInterface::class);

        $course->shouldReceive('isArchived')->andReturn(false);
        $course->shouldReceive('isLocked')->andReturn(true);

        $this->assertFalse($this->permissionChecker->canDeleteCourse($sessionUser, $course));
    }

    public function testCanNotDeleteArchivedCourses(): void
    {
        $course = m::mock(CourseInterface::class);
        $sessionUser = m::mock(SessionUserInterface::class);

        $course->shouldReceive('isArchived')->andReturn(true);
        $course->shouldReceive('isLocked')->andReturn(false);

        $this->assertFalse($this->permissionChecker->canDeleteCourse($sessionUser, $course));
    }

    public function testCanCreateCourse(): void
    {
        $schoolId = 10;
        $rolesInSchool = ['foo'];
        $school = m::mock(SchoolInterface::class);
        $sessionUser = m::mock(SessionUserInterface::class);

        $school->shouldReceive('getId')->andReturn($schoolId);
        $sessionUser->shouldReceive('isRoot')->andReturn(false);
        $sessionUser->shouldReceive('rolesInSchool')->andReturn($rolesInSchool);
        $this->permissionMatrix
            ->shouldReceive('getPermittedRoles')
            ->withArgs([$schoolId, Capabilities::CAN_CREATE_COURSES])
            ->andReturn([]);
        $this->permissionMatrix
            ->shouldReceive('hasPermission')
            ->withArgs([$schoolId, Capabilities::CAN_CREATE_COURSES, $rolesInSchool])
            ->andReturn(true);

        $this->assertTrue($this->permissionChecker->canCreateCourse($sessionUser, $school));
    }

    public function testCanNotCreateCourse(): void
    {
        $schoolId = 10;
        $rolesInSchool = ['foo'];
        $school = m::mock(SchoolInterface::class);
        $sessionUser = m::mock(SessionUserInterface::class);

        $school->shouldReceive('getId')->andReturn($schoolId);
        $sessionUser->shouldReceive('isRoot')->andReturn(false);
        $sessionUser->shouldReceive('rolesInSchool')->andReturn($rolesInSchool);
        $this->permissionMatrix
            ->shouldReceive('getPermittedRoles')
            ->withArgs([$schoolId, Capabilities::CAN_CREATE_COURSES])
            ->andReturn([]);
        $this->permissionMatrix
            ->shouldReceive('hasPermission')
            ->withArgs([$schoolId, Capabilities::CAN_CREATE_COURSES, $rolesInSchool])
            ->andReturn(false);

        $this->assertFalse($this->permissionChecker->canCreateCourse($sessionUser, $school));
    }

    public function testCanUnlockAllCourses(): void
    {
        $rolesInSchool = ['foo'];
        $courseId = 20;
        $schoolId = 10;
        $school = m::mock(SchoolInterface::class);
        $course = m::mock(CourseInterface::class);
        $sessionUser = m::mock(SessionUserInterface::class);

        $school->shouldReceive('getId')->andReturn($schoolId);
        $course->shouldReceive('getSchool')->andReturn($school);
        $course->shouldReceive('getId')->andReturn($courseId);
        $course->shouldReceive('isArchived')->andReturn(false);
        $sessionUser->shouldReceive('isRoot')->andReturn(false);
        $sessionUser->shouldReceive('rolesInSchool')->andReturn($rolesInSchool);
        $this->permissionMatrix
            ->shouldReceive('getPermittedRoles')
            ->withArgs([$schoolId, Capabilities::CAN_UNLOCK_ALL_COURSES])
            ->andReturn([]);
        $this->permissionMatrix
            ->shouldReceive('hasPermission')
            ->withArgs([$schoolId, Capabilities::CAN_UNLOCK_ALL_COURSES, $rolesInSchool])
            ->andReturn(true);

        $this->assertTrue($this->permissionChecker->canUnlockCourse($sessionUser, $course));
    }

    public function testCanUnlockTheirCourses(): void
    {
        $rolesInSchool  = ['foo'];
        $rolesInCourse = ['bar'];
        $courseId = 20;
        $schoolId = 10;
        $school = m::mock(SchoolInterface::class);
        $course = m::mock(CourseInterface::class);
        $sessionUser = m::mock(SessionUserInterface::class);

        $school->shouldReceive('getId')->andReturn($schoolId);
        $course->shouldReceive('getSchool')->andReturn($school);
        $course->shouldReceive('getId')->andReturn($courseId);
        $course->shouldReceive('isArchived')->andReturn(false);
        $sessionUser->shouldReceive('isRoot')->andReturn(false);
        $sessionUser->shouldReceive('rolesInSchool')->andReturn($rolesInSchool);
        $sessionUser->shouldReceive('rolesInCourse')->andReturn($rolesInCourse);
        $this->permissionMatrix
            ->shouldReceive('getPermittedRoles')
            ->withArgs([$schoolId, Capabilities::CAN_UNLOCK_ALL_COURSES])
            ->andReturn([]);
        $this->permissionMatrix
            ->shouldReceive('hasPermission')
            ->withArgs([$schoolId, Capabilities::CAN_UNLOCK_ALL_COURSES, $rolesInSchool])
            ->andReturn(false);
        $this->permissionMatrix
            ->shouldReceive('getPermittedRoles')
            ->withArgs([$schoolId, Capabilities::CAN_UNLOCK_THEIR_COURSES])
            ->andReturn([]);
        $this->permissionMatrix
            ->shouldReceive('hasPermission')
            ->withArgs([$schoolId, Capabilities::CAN_UNLOCK_THEIR_COURSES, $rolesInCourse])
            ->andReturn(true);

        $this->assertTrue($this->permissionChecker->canUnlockCourse($sessionUser, $course));
    }

    public function testCanNotUnlockCourses(): void
    {
        $rolesInSchool  = ['foo'];
        $rolesInCourse = ['bar'];
        $courseId = 20;
        $schoolId = 10;
        $school = m::mock(SchoolInterface::class);
        $course = m::mock(CourseInterface::class);
        $sessionUser = m::mock(SessionUserInterface::class);

        $school->shouldReceive('getId')->andReturn($schoolId);
        $course->shouldReceive('getSchool')->andReturn($school);
        $course->shouldReceive('getId')->andReturn($courseId);
        $course->shouldReceive('isArchived')->andReturn(false);
        $sessionUser->shouldReceive('isRoot')->andReturn(false);
        $sessionUser->shouldReceive('rolesInSchool')->andReturn($rolesInSchool);
        $sessionUser->shouldReceive('rolesInCourse')->andReturn($rolesInCourse);

        $this->permissionMatrix
            ->shouldReceive('getPermittedRoles')
            ->withArgs([$schoolId, Capabilities::CAN_UNLOCK_ALL_COURSES])
            ->andReturn([]);
        $this->permissionMatrix
            ->shouldReceive('hasPermission')
            ->withArgs([$schoolId, Capabilities::CAN_UNLOCK_ALL_COURSES, $rolesInSchool])
            ->andReturn(false);
        $this->permissionMatrix
            ->shouldReceive('getPermittedRoles')
            ->withArgs([$schoolId, Capabilities::CAN_UNLOCK_THEIR_COURSES])
            ->andReturn([]);
        $this->permissionMatrix
            ->shouldReceive('hasPermission')
            ->withArgs([$schoolId, Capabilities::CAN_UNLOCK_THEIR_COURSES, $rolesInCourse])
            ->andReturn(false);

        $this->assertFalse($this->permissionChecker->canUnlockCourse($sessionUser, $course));
    }

    public function testCanNotUnlockCourseIfCourseIsArchived(): void
    {
        $course = m::mock(CourseInterface::class);
        $sessionUser = m::mock(SessionUserInterface::class);

        $course->shouldReceive('isArchived')->andReturn(true);

        $this->assertFalse($this->permissionChecker->canUnlockCourse($sessionUser, $course));
    }

    public function testCanLockAllCourses(): void
    {
        $rolesInSchool = ['foo'];
        $courseId = 20;
        $schoolId = 10;
        $school = m::mock(SchoolInterface::class);
        $course = m::mock(CourseInterface::class);
        $sessionUser = m::mock(SessionUserInterface::class);

        $school->shouldReceive('getId')->andReturn($schoolId);
        $course->shouldReceive('getSchool')->andReturn($school);
        $course->shouldReceive('getId')->andReturn($courseId);
        $course->shouldReceive('isArchived')->andReturn(false);
        $sessionUser->shouldReceive('isRoot')->andReturn(false);
        $sessionUser->shouldReceive('rolesInSchool')->andReturn($rolesInSchool);
        $this->permissionMatrix
            ->shouldReceive('getPermittedRoles')
            ->withArgs([$schoolId, Capabilities::CAN_LOCK_ALL_COURSES])
            ->andReturn([]);
        $this->permissionMatrix
            ->shouldReceive('hasPermission')
            ->withArgs([$schoolId, Capabilities::CAN_LOCK_ALL_COURSES, $rolesInSchool])
            ->andReturn(true);

        $this->assertTrue($this->permissionChecker->canLockCourse($sessionUser, $course));
    }

    public function testCanLockTheirCourses(): void
    {
        $rolesInSchool  = ['foo'];
        $rolesInCourse = ['bar'];
        $courseId = 20;
        $schoolId = 10;
        $school = m::mock(SchoolInterface::class);
        $course = m::mock(CourseInterface::class);
        $sessionUser = m::mock(SessionUserInterface::class);

        $school->shouldReceive('getId')->andReturn($schoolId);
        $course->shouldReceive('getSchool')->andReturn($school);
        $course->shouldReceive('getId')->andReturn($courseId);
        $course->shouldReceive('isArchived')->andReturn(false);
        $sessionUser->shouldReceive('isRoot')->andReturn(false);
        $sessionUser->shouldReceive('rolesInSchool')->andReturn($rolesInSchool);
        $sessionUser->shouldReceive('rolesInCourse')->andReturn($rolesInCourse);
        $this->permissionMatrix
            ->shouldReceive('getPermittedRoles')
            ->withArgs([$schoolId, Capabilities::CAN_LOCK_ALL_COURSES])
            ->andReturn([]);
        $this->permissionMatrix
            ->shouldReceive('hasPermission')
            ->withArgs([$schoolId, Capabilities::CAN_LOCK_ALL_COURSES, $rolesInSchool])
            ->andReturn(false);
        $this->permissionMatrix
            ->shouldReceive('getPermittedRoles')
            ->withArgs([$schoolId, Capabilities::CAN_LOCK_THEIR_COURSES])
            ->andReturn([]);
        $this->permissionMatrix
            ->shouldReceive('hasPermission')
            ->withArgs([$schoolId, Capabilities::CAN_LOCK_THEIR_COURSES, $rolesInCourse])
            ->andReturn(true);

        $this->assertTrue($this->permissionChecker->canLockCourse($sessionUser, $course));
    }

    public function testCanNotLockCourses(): void
    {
        $rolesInSchool  = ['foo'];
        $rolesInCourse = ['bar'];
        $courseId = 20;
        $schoolId = 10;
        $school = m::mock(SchoolInterface::class);
        $course = m::mock(CourseInterface::class);
        $sessionUser = m::mock(SessionUserInterface::class);

        $school->shouldReceive('getId')->andReturn($schoolId);
        $course->shouldReceive('getSchool')->andReturn($school);
        $course->shouldReceive('getId')->andReturn($courseId);
        $course->shouldReceive('isArchived')->andReturn(false);
        $sessionUser->shouldReceive('isRoot')->andReturn(false);
        $sessionUser->shouldReceive('rolesInSchool')->andReturn($rolesInSchool);
        $sessionUser->shouldReceive('rolesInCourse')->andReturn($rolesInCourse);

        $this->permissionMatrix
            ->shouldReceive('getPermittedRoles')
            ->withArgs([$schoolId, Capabilities::CAN_LOCK_ALL_COURSES])
            ->andReturn([]);
        $this->permissionMatrix
            ->shouldReceive('hasPermission')
            ->withArgs([$schoolId, Capabilities::CAN_LOCK_ALL_COURSES, $rolesInSchool])
            ->andReturn(false);
        $this->permissionMatrix
            ->shouldReceive('getPermittedRoles')
            ->withArgs([$schoolId, Capabilities::CAN_LOCK_THEIR_COURSES])
            ->andReturn([]);
        $this->permissionMatrix
            ->shouldReceive('hasPermission')
            ->withArgs([$schoolId, Capabilities::CAN_LOCK_THEIR_COURSES, $rolesInCourse])
            ->andReturn(false);

        $this->assertFalse($this->permissionChecker->canLockCourse($sessionUser, $course));
    }

    public function testCanNotLockCourseIfCourseIsArchived(): void
    {
        $course = m::mock(CourseInterface::class);
        $sessionUser = m::mock(SessionUserInterface::class);

        $course->shouldReceive('isArchived')->andReturn(true);

        $this->assertFalse($this->permissionChecker->canLockCourse($sessionUser, $course));
    }

    public function testCanArchiveAllCourses(): void
    {
        $rolesInSchool = ['foo'];
        $courseId = 20;
        $schoolId = 10;
        $school = m::mock(SchoolInterface::class);
        $course = m::mock(CourseInterface::class);
        $sessionUser = m::mock(SessionUserInterface::class);

        $school->shouldReceive('getId')->andReturn($schoolId);
        $course->shouldReceive('getSchool')->andReturn($school);
        $course->shouldReceive('getId')->andReturn($courseId);
        $sessionUser->shouldReceive('isRoot')->andReturn(false);
        $sessionUser->shouldReceive('rolesInSchool')->andReturn($rolesInSchool);
        $this->permissionMatrix
            ->shouldReceive('getPermittedRoles')
            ->withArgs([$schoolId, Capabilities::CAN_ARCHIVE_ALL_COURSES])
            ->andReturn([]);
        $this->permissionMatrix
            ->shouldReceive('hasPermission')
            ->withArgs([$schoolId, Capabilities::CAN_ARCHIVE_ALL_COURSES, $rolesInSchool])
            ->andReturn(true);

        $this->assertTrue($this->permissionChecker->canArchiveCourse($sessionUser, $course));
    }

    public function testCanArchiveTheirCourses(): void
    {
        $rolesInSchool  = ['foo'];
        $rolesInCourse = ['bar'];
        $courseId = 20;
        $schoolId = 10;
        $school = m::mock(SchoolInterface::class);
        $course = m::mock(CourseInterface::class);
        $sessionUser = m::mock(SessionUserInterface::class);

        $school->shouldReceive('getId')->andReturn($schoolId);
        $course->shouldReceive('getSchool')->andReturn($school);
        $course->shouldReceive('getId')->andReturn($courseId);
        $sessionUser->shouldReceive('isRoot')->andReturn(false);
        $sessionUser->shouldReceive('rolesInSchool')->andReturn($rolesInSchool);
        $sessionUser->shouldReceive('rolesInCourse')->andReturn($rolesInCourse);
        $this->permissionMatrix
            ->shouldReceive('getPermittedRoles')
            ->withArgs([$schoolId, Capabilities::CAN_ARCHIVE_ALL_COURSES])
            ->andReturn([]);
        $this->permissionMatrix
            ->shouldReceive('hasPermission')
            ->withArgs([$schoolId, Capabilities::CAN_ARCHIVE_ALL_COURSES, $rolesInSchool])
            ->andReturn(false);
        $this->permissionMatrix
            ->shouldReceive('getPermittedRoles')
            ->withArgs([$schoolId, Capabilities::CAN_ARCHIVE_THEIR_COURSES])
            ->andReturn([]);
        $this->permissionMatrix
            ->shouldReceive('hasPermission')
            ->withArgs([$schoolId, Capabilities::CAN_ARCHIVE_THEIR_COURSES, $rolesInCourse])
            ->andReturn(true);

        $this->assertTrue($this->permissionChecker->canArchiveCourse($sessionUser, $course));
    }

    public function testCanNotArchiveCourses(): void
    {
        $rolesInSchool  = ['foo'];
        $rolesInCourse = ['bar'];
        $courseId = 20;
        $schoolId = 10;
        $school = m::mock(SchoolInterface::class);
        $course = m::mock(CourseInterface::class);
        $sessionUser = m::mock(SessionUserInterface::class);

        $school->shouldReceive('getId')->andReturn($schoolId);
        $course->shouldReceive('getSchool')->andReturn($school);
        $course->shouldReceive('getId')->andReturn($courseId);
        $sessionUser->shouldReceive('isRoot')->andReturn(false);
        $sessionUser->shouldReceive('rolesInSchool')->andReturn($rolesInSchool);
        $sessionUser->shouldReceive('rolesInCourse')->andReturn($rolesInCourse);

        $this->permissionMatrix
            ->shouldReceive('getPermittedRoles')
            ->withArgs([$schoolId, Capabilities::CAN_ARCHIVE_ALL_COURSES])
            ->andReturn([]);
        $this->permissionMatrix
            ->shouldReceive('hasPermission')
            ->withArgs([$schoolId, Capabilities::CAN_ARCHIVE_ALL_COURSES, $rolesInSchool])
            ->andReturn(false);
        $this->permissionMatrix
            ->shouldReceive('getPermittedRoles')
            ->withArgs([$schoolId, Capabilities::CAN_ARCHIVE_THEIR_COURSES])
            ->andReturn([]);
        $this->permissionMatrix
            ->shouldReceive('hasPermission')
            ->withArgs([$schoolId, Capabilities::CAN_ARCHIVE_THEIR_COURSES, $rolesInCourse])
            ->andReturn(false);

        $this->assertFalse($this->permissionChecker->canArchiveCourse($sessionUser, $course));
    }

    public function testCanUpdateAllSessions(): void
    {
        $rolesInSchool = ['foo'];
        $schoolId = 10;
        $sessionId = 30;

        $session = m::mock(SessionInterface::class);
        $course = m::mock(CourseInterface::class);
        $school = m::mock(SchoolInterface::class);
        $sessionUser = m::mock(SessionUserInterface::class);

        $session->shouldReceive('getId')->andReturn($sessionId);
        $session->shouldReceive('getCourse')->andReturn($course);
        $session->shouldReceive('getSchool')->andReturn($school);
        $course->shouldReceive('isLocked')->andReturn(false);
        $course->shouldReceive('isArchived')->andReturn(false);
        $sessionUser->shouldReceive('isRoot')->andReturn(false);
        $school->shouldReceive('getId')->andReturn($schoolId);
        $sessionUser->shouldReceive('rolesInSchool')->andReturn($rolesInSchool);

        $this->permissionMatrix
            ->shouldReceive('getPermittedRoles')
            ->withArgs([$schoolId, Capabilities::CAN_UPDATE_ALL_SESSIONS])
            ->andReturn([]);
        $this->permissionMatrix
            ->shouldReceive('hasPermission')
            ->withArgs([$schoolId, Capabilities::CAN_UPDATE_ALL_SESSIONS, $rolesInSchool])
            ->andReturn(true);

        $this->assertTrue($this->permissionChecker->canUpdateSession($sessionUser, $session));
    }

    public function testCanUpdateTheirSessions(): void
    {
        $rolesInSchool  = ['foo'];
        $rolesInSession = ['bar'];
        $schoolId = 10;
        $sessionId = 30;

        $session = m::mock(SessionInterface::class);
        $course = m::mock(CourseInterface::class);
        $school = m::mock(SchoolInterface::class);
        $sessionUser = m::mock(SessionUserInterface::class);

        $session->shouldReceive('getId')->andReturn($sessionId);
        $session->shouldReceive('getCourse')->andReturn($course);
        $session->shouldReceive('getSchool')->andReturn($school);
        $course->shouldReceive('isLocked')->andReturn(false);
        $course->shouldReceive('isArchived')->andReturn(false);
        $school->shouldReceive('getId')->andReturn($schoolId);
        $sessionUser->shouldReceive('isRoot')->andReturn(false);
        $sessionUser->shouldReceive('rolesInSchool')->andReturn($rolesInSchool);
        $sessionUser->shouldReceive('rolesInSession')->andReturn($rolesInSession);

        $this->permissionMatrix
            ->shouldReceive('getPermittedRoles')
            ->withArgs([$schoolId, Capabilities::CAN_UPDATE_ALL_SESSIONS])
            ->andReturn([]);
        $this->permissionMatrix
            ->shouldReceive('hasPermission')
            ->withArgs([$schoolId, Capabilities::CAN_UPDATE_ALL_SESSIONS, $rolesInSchool])
            ->andReturn(false);
        $this->permissionMatrix
            ->shouldReceive('getPermittedRoles')
            ->withArgs([$schoolId, Capabilities::CAN_UPDATE_THEIR_SESSIONS])
            ->andReturn([]);
        $this->permissionMatrix
            ->shouldReceive('hasPermission')
            ->withArgs([$schoolId, Capabilities::CAN_UPDATE_THEIR_SESSIONS, $rolesInSession])
            ->andReturn(true);

        $this->assertTrue($this->permissionChecker->canUpdateSession($sessionUser, $session));
    }

    public function testCanUpdateSessionsIfUserCanUpdateCourse(): void
    {
        $rolesInSchool  = ['foo'];
        $rolesInSession = ['bar'];
        $rolesInCourse = ['baz'];
        $schoolId = 10;
        $courseId = 20;
        $sessionId = 30;

        $session = m::mock(SessionInterface::class);
        $course = m::mock(CourseInterface::class);
        $school = m::mock(SchoolInterface::class);
        $sessionUser = m::mock(SessionUserInterface::class);

        $session->shouldReceive('getId')->andReturn($sessionId);
        $session->shouldReceive('getCourse')->andReturn($course);
        $session->shouldReceive('getSchool')->andReturn($school);
        $course->shouldReceive('isLocked')->andReturn(false);
        $course->shouldReceive('isArchived')->andReturn(false);
        $course->shouldReceive('getId')->andReturn($courseId);
        $course->shouldReceive('getSchool')->andReturn($school);
        $school->shouldReceive('getId')->andReturn($schoolId);
        $sessionUser->shouldReceive('isRoot')->andReturn(false);
        $sessionUser->shouldReceive('rolesInSchool')->andReturn($rolesInSchool);
        $sessionUser->shouldReceive('rolesInSession')->andReturn($rolesInSession);
        $sessionUser->shouldReceive('rolesInCourse')->andReturn($rolesInCourse);

        $this->permissionMatrix
            ->shouldReceive('getPermittedRoles')
            ->withArgs([$schoolId, Capabilities::CAN_UPDATE_ALL_SESSIONS])
            ->andReturn([]);
        $this->permissionMatrix
            ->shouldReceive('hasPermission')
            ->withArgs([$schoolId, Capabilities::CAN_UPDATE_ALL_SESSIONS, $rolesInSchool])
            ->andReturn(false);
        $this->permissionMatrix
            ->shouldReceive('getPermittedRoles')
            ->withArgs([$schoolId, Capabilities::CAN_UPDATE_THEIR_SESSIONS])
            ->andReturn([]);
        $this->permissionMatrix
            ->shouldReceive('hasPermission')
            ->withArgs([$schoolId, Capabilities::CAN_UPDATE_THEIR_SESSIONS, $rolesInSession])
            ->andReturn(false);
        $this->permissionMatrix
            ->shouldReceive('getPermittedRoles')
            ->withArgs([$schoolId, Capabilities::CAN_UPDATE_ALL_COURSES])
            ->andReturn([]);
        $this->permissionMatrix
            ->shouldReceive('hasPermission')
            ->withArgs([$schoolId, Capabilities::CAN_UPDATE_ALL_COURSES, $rolesInSchool])
            ->andReturn(true);

        $this->assertTrue($this->permissionChecker->canUpdateSession($sessionUser, $session));
    }

    public function testCanNotUpdateSessions(): void
    {
        $rolesInSchool  = ['foo'];
        $rolesInSession = ['bar'];
        $rolesInCourse = ['baz'];
        $schoolId = 10;
        $courseId = 20;
        $sessionId = 30;

        $session = m::mock(SessionInterface::class);
        $course = m::mock(CourseInterface::class);
        $school = m::mock(SchoolInterface::class);
        $sessionUser = m::mock(SessionUserInterface::class);

        $session->shouldReceive('getId')->andReturn($sessionId);
        $session->shouldReceive('getCourse')->andReturn($course);
        $session->shouldReceive('getSchool')->andReturn($school);
        $course->shouldReceive('isLocked')->andReturn(false);
        $course->shouldReceive('isArchived')->andReturn(false);
        $course->shouldReceive('getId')->andReturn($courseId);
        $course->shouldReceive('getSchool')->andReturn($school);
        $school->shouldReceive('getId')->andReturn($schoolId);
        $sessionUser->shouldReceive('isRoot')->andReturn(false);
        $sessionUser->shouldReceive('rolesInSchool')->andReturn($rolesInSchool);
        $sessionUser->shouldReceive('rolesInSession')->andReturn($rolesInSession);
        $sessionUser->shouldReceive('rolesInCourse')->andReturn($rolesInCourse);

        $this->permissionMatrix
            ->shouldReceive('getPermittedRoles')
            ->withArgs([$schoolId, Capabilities::CAN_UPDATE_ALL_SESSIONS])
            ->andReturn([]);
        $this->permissionMatrix
            ->shouldReceive('hasPermission')
            ->withArgs([$schoolId, Capabilities::CAN_UPDATE_ALL_SESSIONS, $rolesInSchool])
            ->andReturn(false);
        $this->permissionMatrix
            ->shouldReceive('getPermittedRoles')
            ->withArgs([$schoolId, Capabilities::CAN_UPDATE_THEIR_SESSIONS])
            ->andReturn([]);
        $this->permissionMatrix
            ->shouldReceive('hasPermission')
            ->withArgs([$schoolId, Capabilities::CAN_UPDATE_THEIR_SESSIONS, $rolesInSession])
            ->andReturn(false);
        $this->permissionMatrix
            ->shouldReceive('getPermittedRoles')
            ->withArgs([$schoolId, Capabilities::CAN_UPDATE_ALL_COURSES])
            ->andReturn([]);
        $this->permissionMatrix
            ->shouldReceive('hasPermission')
            ->withArgs([$schoolId, Capabilities::CAN_UPDATE_ALL_COURSES, $rolesInSchool])
            ->andReturn(false);
        $this->permissionMatrix
            ->shouldReceive('getPermittedRoles')
            ->withArgs([$schoolId, Capabilities::CAN_UPDATE_THEIR_COURSES])
            ->andReturn([]);
        $this->permissionMatrix
            ->shouldReceive('hasPermission')
            ->withArgs([$schoolId, Capabilities::CAN_UPDATE_THEIR_COURSES, $rolesInCourse])
            ->andReturn(false);

        $this->assertFalse($this->permissionChecker->canUpdateSession($sessionUser, $session));
    }

    public function testCanNotUpdateSessionsInLockedCourse(): void
    {
        $session = m::mock(SessionInterface::class);
        $course = m::mock(CourseInterface::class);
        $sessionUser = m::mock(SessionUserInterface::class);

        $session->shouldReceive('getCourse')->andReturn($course);
        $course->shouldReceive('isLocked')->andReturn(true);
        $course->shouldReceive('isArchived')->andReturn(true);

        $this->assertFalse($this->permissionChecker->canUpdateSession($sessionUser, $session));
    }

    public function testCanNotUpdateSessionsInArchivedCourse(): void
    {
        $session = m::mock(SessionInterface::class);
        $course = m::mock(CourseInterface::class);
        $sessionUser = m::mock(SessionUserInterface::class);

        $session->shouldReceive('getCourse')->andReturn($course);
        $course->shouldReceive('isLocked')->andReturn(false);
        $course->shouldReceive('isArchived')->andReturn(true);

        $this->assertFalse($this->permissionChecker->canUpdateSession($sessionUser, $session));
    }

    public function testCanDeleteAllSessions(): void
    {
        $rolesInSchool = ['foo'];
        $schoolId = 10;
        $sessionId = 30;

        $session = m::mock(SessionInterface::class);
        $course = m::mock(CourseInterface::class);
        $school = m::mock(SchoolInterface::class);
        $sessionUser = m::mock(SessionUserInterface::class);

        $session->shouldReceive('getId')->andReturn($sessionId);
        $session->shouldReceive('getCourse')->andReturn($course);
        $session->shouldReceive('getSchool')->andReturn($school);
        $course->shouldReceive('isLocked')->andReturn(false);
        $course->shouldReceive('isArchived')->andReturn(false);
        $school->shouldReceive('getId')->andReturn($schoolId);
        $sessionUser->shouldReceive('isRoot')->andReturn(false);
        $sessionUser->shouldReceive('rolesInSchool')->andReturn($rolesInSchool);

        $this->permissionMatrix
            ->shouldReceive('getPermittedRoles')
            ->withArgs([$schoolId, Capabilities::CAN_DELETE_ALL_SESSIONS])
            ->andReturn([]);
        $this->permissionMatrix
            ->shouldReceive('hasPermission')
            ->withArgs([$schoolId, Capabilities::CAN_DELETE_ALL_SESSIONS, $rolesInSchool])
            ->andReturn(true);

        $this->assertTrue($this->permissionChecker->canDeleteSession($sessionUser, $session));
    }

    public function testCanDeleteTheirSessions(): void
    {
        $rolesInSchool  = ['foo'];
        $rolesInSession = ['bar'];
        $schoolId = 10;
        $sessionId = 30;

        $session = m::mock(SessionInterface::class);
        $course = m::mock(CourseInterface::class);
        $school = m::mock(SchoolInterface::class);
        $sessionUser = m::mock(SessionUserInterface::class);

        $session->shouldReceive('getId')->andReturn($sessionId);
        $session->shouldReceive('getCourse')->andReturn($course);
        $session->shouldReceive('getSchool')->andReturn($school);
        $course->shouldReceive('isLocked')->andReturn(false);
        $course->shouldReceive('isArchived')->andReturn(false);
        $course->shouldReceive('getSchool')->andReturn($school);
        $school->shouldReceive('getId')->andReturn($schoolId);
        $sessionUser->shouldReceive('isRoot')->andReturn(false);
        $sessionUser->shouldReceive('rolesInSchool')->andReturn($rolesInSchool);
        $sessionUser->shouldReceive('rolesInSession')->andReturn($rolesInSession);

        $this->permissionMatrix
            ->shouldReceive('getPermittedRoles')
            ->withArgs([$schoolId, Capabilities::CAN_DELETE_ALL_SESSIONS])
            ->andReturn([]);
        $this->permissionMatrix
            ->shouldReceive('hasPermission')
            ->withArgs([$schoolId, Capabilities::CAN_DELETE_ALL_SESSIONS, $rolesInSchool])
            ->andReturn(false);
        $this->permissionMatrix
            ->shouldReceive('getPermittedRoles')
            ->withArgs([$schoolId, Capabilities::CAN_DELETE_THEIR_SESSIONS])
            ->andReturn([]);
        $this->permissionMatrix
            ->shouldReceive('hasPermission')
            ->withArgs([$schoolId, Capabilities::CAN_DELETE_THEIR_SESSIONS, $rolesInSession])
            ->andReturn(true);

        $this->assertTrue($this->permissionChecker->canDeleteSession($sessionUser, $session));
    }

    public function testCanDeleteSessionsIfUserCanUpdateCourse(): void
    {
        $rolesInSchool  = ['foo'];
        $rolesInSession = ['bar'];
        $schoolId = 10;
        $courseId = 20;
        $sessionId = 30;

        $session = m::mock(SessionInterface::class);
        $course = m::mock(CourseInterface::class);
        $school = m::mock(SchoolInterface::class);
        $sessionUser = m::mock(SessionUserInterface::class);

        $session->shouldReceive('getId')->andReturn($sessionId);
        $session->shouldReceive('getCourse')->andReturn($course);
        $session->shouldReceive('getSchool')->andReturn($school);
        $course->shouldReceive('isLocked')->andReturn(false);
        $course->shouldReceive('isArchived')->andReturn(false);
        $course->shouldReceive('getId')->andReturn($courseId);
        $course->shouldReceive('getSchool')->andReturn($school);
        $school->shouldReceive('getId')->andReturn($schoolId);
        $sessionUser->shouldReceive('isRoot')->andReturn(false);
        $sessionUser->shouldReceive('rolesInSchool')->andReturn($rolesInSchool);
        $sessionUser->shouldReceive('rolesInSession')->andReturn($rolesInSession);

        $this->permissionMatrix
            ->shouldReceive('getPermittedRoles')
            ->withArgs([$schoolId, Capabilities::CAN_DELETE_ALL_SESSIONS])
            ->andReturn([]);
        $this->permissionMatrix
            ->shouldReceive('hasPermission')
            ->withArgs([$schoolId, Capabilities::CAN_DELETE_ALL_SESSIONS, $rolesInSchool])
            ->andReturn(false);
        $this->permissionMatrix
            ->shouldReceive('getPermittedRoles')
            ->withArgs([$schoolId, Capabilities::CAN_DELETE_THEIR_SESSIONS])
            ->andReturn([]);
        $this->permissionMatrix
            ->shouldReceive('hasPermission')
            ->withArgs([$schoolId, Capabilities::CAN_DELETE_THEIR_SESSIONS, $rolesInSession])
            ->andReturn(false);
        $this->permissionMatrix
            ->shouldReceive('getPermittedRoles')
            ->withArgs([$schoolId, Capabilities::CAN_UPDATE_ALL_COURSES])
            ->andReturn([]);
        $this->permissionMatrix
            ->shouldReceive('hasPermission')
            ->withArgs([$schoolId, Capabilities::CAN_UPDATE_ALL_COURSES, $rolesInSchool])
            ->andReturn(true);

        $this->assertTrue($this->permissionChecker->canDeleteSession($sessionUser, $session));
    }

    public function testCanNotDeleteSessions(): void
    {
        $rolesInSchool  = ['foo'];
        $rolesInSession = ['bar'];
        $rolesInCourse = ['baz'];
        $schoolId = 10;
        $courseId = 20;
        $sessionId = 30;

        $session = m::mock(SessionInterface::class);
        $course = m::mock(CourseInterface::class);
        $school = m::mock(SchoolInterface::class);
        $sessionUser = m::mock(SessionUserInterface::class);

        $session->shouldReceive('getId')->andReturn($sessionId);
        $session->shouldReceive('getCourse')->andReturn($course);
        $session->shouldReceive('getSchool')->andReturn($school);
        $course->shouldReceive('isLocked')->andReturn(false);
        $course->shouldReceive('isArchived')->andReturn(false);
        $course->shouldReceive('getId')->andReturn($courseId);
        $course->shouldReceive('getSchool')->andReturn($school);
        $school->shouldReceive('getId')->andReturn($schoolId);
        $sessionUser->shouldReceive('isRoot')->andReturn(false);
        $sessionUser->shouldReceive('rolesInSchool')->andReturn($rolesInSchool);
        $sessionUser->shouldReceive('rolesInSession')->andReturn($rolesInSession);
        $sessionUser->shouldReceive('rolesInCourse')->andReturn($rolesInCourse);

        $this->permissionMatrix
            ->shouldReceive('getPermittedRoles')
            ->withArgs([$schoolId, Capabilities::CAN_DELETE_ALL_SESSIONS])
            ->andReturn([]);
        $this->permissionMatrix
            ->shouldReceive('hasPermission')
            ->withArgs([$schoolId, Capabilities::CAN_DELETE_ALL_SESSIONS, $rolesInSchool])
            ->andReturn(false);
        $this->permissionMatrix
            ->shouldReceive('getPermittedRoles')
            ->withArgs([$schoolId, Capabilities::CAN_DELETE_THEIR_SESSIONS])
            ->andReturn([]);
        $this->permissionMatrix
            ->shouldReceive('hasPermission')
            ->withArgs([$schoolId, Capabilities::CAN_DELETE_THEIR_SESSIONS, $rolesInSession])
            ->andReturn(false);
        $this->permissionMatrix
            ->shouldReceive('getPermittedRoles')
            ->withArgs([$schoolId, Capabilities::CAN_UPDATE_ALL_COURSES])
            ->andReturn([]);
        $this->permissionMatrix
            ->shouldReceive('hasPermission')
            ->withArgs([$schoolId, Capabilities::CAN_UPDATE_ALL_COURSES, $rolesInSchool])
            ->andReturn(false);
        $this->permissionMatrix
            ->shouldReceive('getPermittedRoles')
            ->withArgs([$schoolId, Capabilities::CAN_UPDATE_THEIR_COURSES])
            ->andReturn([]);
        $this->permissionMatrix
            ->shouldReceive('hasPermission')
            ->withArgs([$schoolId, Capabilities::CAN_UPDATE_THEIR_COURSES, $rolesInCourse])
            ->andReturn(false);

        $this->assertFalse($this->permissionChecker->canDeleteSession($sessionUser, $session));
    }

    public function testCanNotDeleteSessionsInLockedCourse(): void
    {
        $session = m::mock(SessionInterface::class);
        $course = m::mock(CourseInterface::class);
        $sessionUser = m::mock(SessionUserInterface::class);

        $session->shouldReceive('getCourse')->andReturn($course);
        $course->shouldReceive('isLocked')->andReturn(true);
        $course->shouldReceive('isArchived')->andReturn(false);

        $this->assertFalse($this->permissionChecker->canDeleteSession($sessionUser, $session));
    }

    public function testCanNotDeleteSessionsInArchivedCourse(): void
    {
        $session = m::mock(SessionInterface::class);
        $course = m::mock(CourseInterface::class);
        $sessionUser = m::mock(SessionUserInterface::class);

        $session->shouldReceive('getCourse')->andReturn($course);
        $course->shouldReceive('isLocked')->andReturn(false);
        $course->shouldReceive('isArchived')->andReturn(true);

        $this->assertFalse($this->permissionChecker->canDeleteSession($sessionUser, $session));
    }

    public function testCanCreateSession(): void
    {
        $rolesInSchool = ['foo'];
        $schoolId = 10;
        $course = m::mock(CourseInterface::class);
        $school = m::mock(SchoolInterface::class);
        $sessionUser = m::mock(SessionUserInterface::class);

        $course->shouldReceive('getSchool')->andReturn($school);
        $course->shouldReceive('isLocked')->andReturn(false);
        $course->shouldReceive('isArchived')->andReturn(false);
        $school->shouldReceive('getId')->andReturn($schoolId);
        $sessionUser->shouldReceive('isRoot')->andReturn(false);
        $sessionUser->shouldReceive('rolesInSchool')->andReturn($rolesInSchool);

        $this->permissionMatrix
            ->shouldReceive('getPermittedRoles')
            ->withArgs([$schoolId, Capabilities::CAN_CREATE_SESSIONS])
            ->andReturn([]);
        $this->permissionMatrix
            ->shouldReceive('hasPermission')
            ->withArgs([$schoolId, Capabilities::CAN_CREATE_SESSIONS, $rolesInSchool])
            ->andReturn(true);

        $this->assertTrue($this->permissionChecker->canCreateSession($sessionUser, $course));
    }

    public function testCanCreateSessionIfUserCanUpdateCourse(): void
    {
        $rolesInSchool = ['foo'];
        $schoolId = 10;
        $course = m::mock(CourseInterface::class);
        $school = m::mock(SchoolInterface::class);
        $sessionUser = m::mock(SessionUserInterface::class);

        $course->shouldReceive('getSchool')->andReturn($school);
        $course->shouldReceive('isLocked')->andReturn(false);
        $course->shouldReceive('isArchived')->andReturn(false);
        $school->shouldReceive('getId')->andReturn($schoolId);
        $sessionUser->shouldReceive('isRoot')->andReturn(false);
        $sessionUser->shouldReceive('rolesInSchool')->andReturn($rolesInSchool);

        $this->permissionMatrix
            ->shouldReceive('getPermittedRoles')
            ->withArgs([$schoolId, Capabilities::CAN_CREATE_SESSIONS])
            ->andReturn([]);
        $this->permissionMatrix
            ->shouldReceive('hasPermission')
            ->withArgs([$schoolId, Capabilities::CAN_CREATE_SESSIONS, $rolesInSchool])
            ->andReturn(false);
        $this->permissionMatrix
            ->shouldReceive('getPermittedRoles')
            ->withArgs([$schoolId, Capabilities::CAN_UPDATE_ALL_COURSES])
            ->andReturn([]);
        $this->permissionMatrix
            ->shouldReceive('hasPermission')
            ->withArgs([$schoolId, Capabilities::CAN_UPDATE_ALL_COURSES, $rolesInSchool])
            ->andReturn(true);

        $this->assertTrue($this->permissionChecker->canCreateSession($sessionUser, $course));
    }

    public function testCanNotCreateSession(): void
    {
        $rolesInSchool  = ['foo'];
        $rolesInCourse = ['bar'];
        $schoolId = 10;
        $courseId = 20;
        $course = m::mock(CourseInterface::class);
        $school = m::mock(SchoolInterface::class);
        $sessionUser = m::mock(SessionUserInterface::class);

        $course->shouldReceive('getId')->andReturn($courseId);
        $course->shouldReceive('getSchool')->andReturn($school);
        $course->shouldReceive('isLocked')->andReturn(false);
        $course->shouldReceive('isArchived')->andReturn(false);
        $school->shouldReceive('getId')->andReturn($schoolId);
        $sessionUser->shouldReceive('isRoot')->andReturn(false);
        $sessionUser->shouldReceive('rolesInSchool')->andReturn($rolesInSchool);
        $sessionUser->shouldReceive('rolesInCourse')->andReturn($rolesInCourse);

        $this->permissionMatrix
            ->shouldReceive('getPermittedRoles')
            ->withArgs([$schoolId, Capabilities::CAN_CREATE_SESSIONS])
            ->andReturn([]);
        $this->permissionMatrix
            ->shouldReceive('hasPermission')
            ->withArgs([$schoolId, Capabilities::CAN_CREATE_SESSIONS, $rolesInSchool])
            ->andReturn(false);
        $this->permissionMatrix
            ->shouldReceive('getPermittedRoles')
            ->withArgs([$schoolId, Capabilities::CAN_UPDATE_ALL_COURSES])
            ->andReturn([]);
        $this->permissionMatrix
            ->shouldReceive('hasPermission')
            ->withArgs([$schoolId, Capabilities::CAN_UPDATE_ALL_COURSES, $rolesInSchool])
            ->andReturn(false);
        $this->permissionMatrix
            ->shouldReceive('getPermittedRoles')
            ->withArgs([$schoolId, Capabilities::CAN_UPDATE_THEIR_COURSES])
            ->andReturn([]);
        $this->permissionMatrix
            ->shouldReceive('hasPermission')
            ->withArgs([$schoolId, Capabilities::CAN_UPDATE_THEIR_COURSES, $rolesInCourse])
            ->andReturn(false);

        $this->assertFalse($this->permissionChecker->canCreateSession($sessionUser, $course));
    }

    public function testCanNotCreateSessionInLockedCourse(): void
    {
        $course = m::mock(CourseInterface::class);
        $sessionUser = m::mock(SessionUserInterface::class);

        $course->shouldReceive('isLocked')->andReturn(true);
        $course->shouldReceive('isArchived')->andReturn(false);

        $this->assertFalse($this->permissionChecker->canCreateSession($sessionUser, $course));
    }

    public function testCanNotCreateSessionInArchivedCourse(): void
    {
        $course = m::mock(CourseInterface::class);
        $sessionUser = m::mock(SessionUserInterface::class);

        $course->shouldReceive('isLocked')->andReturn(false);
        $course->shouldReceive('isArchived')->andReturn(true);

        $this->assertFalse($this->permissionChecker->canCreateSession($sessionUser, $course));
    }

    public function testCanUpdateSessionType(): void
    {
        $schoolId = 10;
        $rolesInSchool = ['foo'];
        $sessionUser = m::mock(SessionUserInterface::class);

        $sessionUser->shouldReceive('isRoot')->andReturn(false);
        $sessionUser->shouldReceive('rolesInSchool')->andReturn($rolesInSchool);
        $this->permissionMatrix
            ->shouldReceive('getPermittedRoles')
            ->withArgs([$schoolId, Capabilities::CAN_UPDATE_SESSION_TYPES])
            ->andReturn([]);
        $this->permissionMatrix
            ->shouldReceive('hasPermission')
            ->withArgs([$schoolId, Capabilities::CAN_UPDATE_SESSION_TYPES, $rolesInSchool])
            ->andReturn(true);

        $this->assertTrue($this->permissionChecker->canUpdateSessionType($sessionUser, $schoolId));
    }

    public function testCanNotUpdateSessionType(): void
    {
        $schoolId = 10;
        $rolesInSchool = ['foo'];
        $sessionUser = m::mock(SessionUserInterface::class);

        $sessionUser->shouldReceive('isRoot')->andReturn(false);
        $sessionUser->shouldReceive('rolesInSchool')->andReturn($rolesInSchool);
        $this->permissionMatrix
            ->shouldReceive('getPermittedRoles')
            ->withArgs([$schoolId, Capabilities::CAN_UPDATE_SESSION_TYPES])
            ->andReturn([]);
        $this->permissionMatrix
            ->shouldReceive('hasPermission')
            ->withArgs([$schoolId, Capabilities::CAN_UPDATE_SESSION_TYPES, $rolesInSchool])
            ->andReturn(false);

        $this->assertFalse($this->permissionChecker->canUpdateSessionType($sessionUser, $schoolId));
    }

    public function testCanDeleteSessionType(): void
    {
        $schoolId = 10;
        $rolesInSchool = ['foo'];
        $sessionUser = m::mock(SessionUserInterface::class);

        $sessionUser->shouldReceive('isRoot')->andReturn(false);
        $sessionUser->shouldReceive('rolesInSchool')->andReturn($rolesInSchool);
        $this->permissionMatrix
            ->shouldReceive('getPermittedRoles')
            ->withArgs([$schoolId, Capabilities::CAN_DELETE_SESSION_TYPES])
            ->andReturn([]);
        $this->permissionMatrix
            ->shouldReceive('hasPermission')
            ->withArgs([$schoolId, Capabilities::CAN_DELETE_SESSION_TYPES, $rolesInSchool])
            ->andReturn(true);

        $this->assertTrue($this->permissionChecker->canDeleteSessionType($sessionUser, $schoolId));
    }

    public function testCanNotDeleteSessionType(): void
    {
        $schoolId = 10;
        $rolesInSchool = ['foo'];
        $sessionUser = m::mock(SessionUserInterface::class);

        $sessionUser->shouldReceive('isRoot')->andReturn(false);
        $sessionUser->shouldReceive('rolesInSchool')->andReturn($rolesInSchool);
        $this->permissionMatrix
            ->shouldReceive('getPermittedRoles')
            ->withArgs([$schoolId, Capabilities::CAN_DELETE_SESSION_TYPES])
            ->andReturn([]);
        $this->permissionMatrix
            ->shouldReceive('hasPermission')
            ->withArgs([$schoolId, Capabilities::CAN_DELETE_SESSION_TYPES, $rolesInSchool])
            ->andReturn(false);

        $this->assertFalse($this->permissionChecker->canDeleteSessionType($sessionUser, $schoolId));
    }

    public function testCanCreateSessionType(): void
    {
        $schoolId = 10;
        $rolesInSchool = ['foo'];
        $sessionUser = m::mock(SessionUserInterface::class);

        $sessionUser->shouldReceive('isRoot')->andReturn(false);
        $sessionUser->shouldReceive('rolesInSchool')->andReturn($rolesInSchool);
        $this->permissionMatrix
            ->shouldReceive('getPermittedRoles')
            ->withArgs([$schoolId, Capabilities::CAN_CREATE_SESSION_TYPES])
            ->andReturn([]);
        $this->permissionMatrix
            ->shouldReceive('hasPermission')
            ->withArgs([$schoolId, Capabilities::CAN_CREATE_SESSION_TYPES, $rolesInSchool])
            ->andReturn(true);

        $this->assertTrue($this->permissionChecker->canCreateSessionType($sessionUser, $schoolId));
    }

    public function testCanNotCreateSessionType(): void
    {
        $schoolId = 10;
        $rolesInSchool = ['foo'];
        $sessionUser = m::mock(SessionUserInterface::class);

        $sessionUser->shouldReceive('isRoot')->andReturn(false);
        $sessionUser->shouldReceive('rolesInSchool')->andReturn($rolesInSchool);
        $this->permissionMatrix
            ->shouldReceive('getPermittedRoles')
            ->withArgs([$schoolId, Capabilities::CAN_CREATE_SESSION_TYPES])
            ->andReturn([]);
        $this->permissionMatrix
            ->shouldReceive('hasPermission')
            ->withArgs([$schoolId, Capabilities::CAN_CREATE_SESSION_TYPES, $rolesInSchool])
            ->andReturn(false);

        $this->assertFalse($this->permissionChecker->canCreateSessionType($sessionUser, $schoolId));
    }

    public function testCanUpdateAllPrograms(): void
    {
        $rolesInSchool = ['foo'];
        $schoolId = 10;
        $programId = 20;

        $sessionUser = m::mock(SessionUserInterface::class);

        $sessionUser->shouldReceive('isRoot')->andReturn(false);
        $sessionUser->shouldReceive('rolesInSchool')->andReturn($rolesInSchool);
        $this->permissionMatrix
            ->shouldReceive('getPermittedRoles')
            ->withArgs([$schoolId, Capabilities::CAN_UPDATE_ALL_PROGRAMS])
            ->andReturn([]);
        $this->permissionMatrix
            ->shouldReceive('hasPermission')
            ->withArgs([$schoolId, Capabilities::CAN_UPDATE_ALL_PROGRAMS, $rolesInSchool])
            ->andReturn(true);
        $this->assertTrue($this->permissionChecker->canUpdateProgram($sessionUser, $programId, $schoolId));
    }

    public function testCanUpdateTheirPrograms(): void
    {
        $rolesInSchool  = ['foo'];
        $rolesInProgram = ['bar'];
        $schoolId = 10;
        $programId = 20;

        $sessionUser = m::mock(SessionUserInterface::class);

        $sessionUser->shouldReceive('isRoot')->andReturn(false);
        $sessionUser->shouldReceive('rolesInSchool')->andReturn($rolesInSchool);
        $sessionUser->shouldReceive('rolesInProgram')->andReturn($rolesInProgram);

        $this->permissionMatrix
            ->shouldReceive('getPermittedRoles')
            ->withArgs([$schoolId, Capabilities::CAN_UPDATE_ALL_PROGRAMS])
            ->andReturn([]);
        $this->permissionMatrix
            ->shouldReceive('hasPermission')
            ->withArgs([$schoolId, Capabilities::CAN_UPDATE_ALL_PROGRAMS, $rolesInSchool])
            ->andReturn(false);
        $this->permissionMatrix
            ->shouldReceive('getPermittedRoles')
            ->withArgs([$schoolId, Capabilities::CAN_UPDATE_THEIR_PROGRAMS])
            ->andReturn([]);
        $this->permissionMatrix
            ->shouldReceive('hasPermission')
            ->withArgs([$schoolId, Capabilities::CAN_UPDATE_THEIR_PROGRAMS, $rolesInProgram])
            ->andReturn(true);

        $this->assertTrue($this->permissionChecker->canUpdateProgram($sessionUser, $programId, $schoolId));
    }

    public function testCanNotUpdatePrograms(): void
    {
        $rolesInSchool  = ['foo'];
        $rolesInProgram = ['bar'];
        $schoolId = 10;
        $programId = 20;

        $sessionUser = m::mock(SessionUserInterface::class);

        $sessionUser->shouldReceive('isRoot')->andReturn(false);
        $sessionUser->shouldReceive('rolesInSchool')->andReturn($rolesInSchool);
        $sessionUser->shouldReceive('rolesInProgram')->andReturn($rolesInProgram);

        $this->permissionMatrix
            ->shouldReceive('getPermittedRoles')
            ->withArgs([$schoolId, Capabilities::CAN_UPDATE_ALL_PROGRAMS])
            ->andReturn([]);
        $this->permissionMatrix
            ->shouldReceive('hasPermission')
            ->withArgs([$schoolId, Capabilities::CAN_UPDATE_ALL_PROGRAMS, $rolesInSchool])
            ->andReturn(false);
        $this->permissionMatrix
            ->shouldReceive('getPermittedRoles')
            ->withArgs([$schoolId, Capabilities::CAN_UPDATE_THEIR_PROGRAMS])
            ->andReturn([]);
        $this->permissionMatrix
            ->shouldReceive('hasPermission')
            ->withArgs([$schoolId, Capabilities::CAN_UPDATE_THEIR_PROGRAMS, $rolesInProgram])
            ->andReturn(false);

        $this->assertFalse($this->permissionChecker->canUpdateProgram($sessionUser, $programId, $schoolId));
    }

    public function testCanDeleteAllPrograms(): void
    {
        $rolesInSchool = ['foo'];
        $schoolId = 10;
        $programId = 20;

        $sessionUser = m::mock(SessionUserInterface::class);

        $sessionUser->shouldReceive('isRoot')->andReturn(false);
        $sessionUser->shouldReceive('rolesInSchool')->andReturn($rolesInSchool);
        $this->permissionMatrix
            ->shouldReceive('getPermittedRoles')
            ->withArgs([$schoolId, Capabilities::CAN_DELETE_ALL_PROGRAMS])
            ->andReturn([]);
        $this->permissionMatrix
            ->shouldReceive('hasPermission')
            ->withArgs([$schoolId, Capabilities::CAN_DELETE_ALL_PROGRAMS, $rolesInSchool])
            ->andReturn(true);
        $this->assertTrue($this->permissionChecker->canDeleteProgram($sessionUser, $programId, $schoolId));
    }

    public function testCanDeleteTheirPrograms(): void
    {
        $rolesInSchool  = ['foo'];
        $rolesInProgram = ['bar'];
        $schoolId = 10;
        $programId = 20;

        $sessionUser = m::mock(SessionUserInterface::class);

        $sessionUser->shouldReceive('isRoot')->andReturn(false);
        $sessionUser->shouldReceive('rolesInSchool')->andReturn($rolesInSchool);
        $sessionUser->shouldReceive('rolesInProgram')->andReturn($rolesInProgram);

        $this->permissionMatrix
            ->shouldReceive('getPermittedRoles')
            ->withArgs([$schoolId, Capabilities::CAN_DELETE_ALL_PROGRAMS])
            ->andReturn([]);
        $this->permissionMatrix
            ->shouldReceive('hasPermission')
            ->withArgs([$schoolId, Capabilities::CAN_DELETE_ALL_PROGRAMS, $rolesInSchool])
            ->andReturn(false);
        $this->permissionMatrix
            ->shouldReceive('getPermittedRoles')
            ->withArgs([$schoolId, Capabilities::CAN_DELETE_THEIR_PROGRAMS])
            ->andReturn([]);
        $this->permissionMatrix
            ->shouldReceive('hasPermission')
            ->withArgs([$schoolId, Capabilities::CAN_DELETE_THEIR_PROGRAMS, $rolesInProgram])
            ->andReturn(true);

        $this->assertTrue($this->permissionChecker->canDeleteProgram($sessionUser, $programId, $schoolId));
    }

    public function testCanNotDeletePrograms(): void
    {
        $rolesInSchool  = ['foo'];
        $rolesInProgram = ['bar'];
        $schoolId = 10;
        $programId = 20;

        $sessionUser = m::mock(SessionUserInterface::class);

        $sessionUser->shouldReceive('isRoot')->andReturn(false);
        $sessionUser->shouldReceive('rolesInSchool')->andReturn($rolesInSchool);
        $sessionUser->shouldReceive('rolesInProgram')->andReturn($rolesInProgram);

        $this->permissionMatrix
            ->shouldReceive('getPermittedRoles')
            ->withArgs([$schoolId, Capabilities::CAN_DELETE_ALL_PROGRAMS])
            ->andReturn([]);
        $this->permissionMatrix
            ->shouldReceive('hasPermission')
            ->withArgs([$schoolId, Capabilities::CAN_DELETE_ALL_PROGRAMS, $rolesInSchool])
            ->andReturn(false);
        $this->permissionMatrix
            ->shouldReceive('getPermittedRoles')
            ->withArgs([$schoolId, Capabilities::CAN_DELETE_THEIR_PROGRAMS])
            ->andReturn([]);
        $this->permissionMatrix
            ->shouldReceive('hasPermission')
            ->withArgs([$schoolId, Capabilities::CAN_DELETE_THEIR_PROGRAMS, $rolesInProgram])
            ->andReturn(false);

        $this->assertFalse($this->permissionChecker->canDeleteProgram($sessionUser, $programId, $schoolId));
    }

    public function testCanCreateProgram(): void
    {
        $schoolId = 10;
        $rolesInSchool = ['foo'];
        $sessionUser = m::mock(SessionUserInterface::class);

        $sessionUser->shouldReceive('isRoot')->andReturn(false);
        $sessionUser->shouldReceive('rolesInSchool')->andReturn($rolesInSchool);
        $this->permissionMatrix
            ->shouldReceive('getPermittedRoles')
            ->withArgs([$schoolId, Capabilities::CAN_CREATE_PROGRAMS])
            ->andReturn([]);
        $this->permissionMatrix
            ->shouldReceive('hasPermission')
            ->withArgs([$schoolId, Capabilities::CAN_CREATE_PROGRAMS, $rolesInSchool])
            ->andReturn(true);

        $this->assertTrue($this->permissionChecker->canCreateProgram($sessionUser, $schoolId));
    }

    public function testCanNotCreateProgram(): void
    {
        $schoolId = 10;
        $rolesInSchool = ['foo'];
        $sessionUser = m::mock(SessionUserInterface::class);

        $sessionUser->shouldReceive('isRoot')->andReturn(false);
        $sessionUser->shouldReceive('rolesInSchool')->andReturn($rolesInSchool);
        $this->permissionMatrix
            ->shouldReceive('getPermittedRoles')
            ->withArgs([$schoolId, Capabilities::CAN_CREATE_PROGRAMS])
            ->andReturn([]);
        $this->permissionMatrix
            ->shouldReceive('hasPermission')
            ->withArgs([$schoolId, Capabilities::CAN_CREATE_PROGRAMS, $rolesInSchool])
            ->andReturn(false);

        $this->assertFalse($this->permissionChecker->canCreateProgram($sessionUser, $schoolId));
    }

    public function testCanUpdateAllProgramYears(): void
    {
        $rolesInSchool = ['foo'];
        $programYearId = 10;
        $schoolId = 20;
        $school = m::mock(SchoolInterface::class);
        $programYear = m::mock(ProgramYearInterface::class);
        $sessionUser = m::mock(SessionUserInterface::class);

        $school->shouldReceive('getId')->andReturn($schoolId);
        $programYear->shouldReceive('getSchool')->andReturn($school);
        $programYear->shouldReceive('getId')->andReturn($programYearId);
        $programYear->shouldReceive('isArchived')->andReturn(false);
        $programYear->shouldReceive('isLocked')->andReturn(false);
        $sessionUser->shouldReceive('isRoot')->andReturn(false);
        $sessionUser->shouldReceive('rolesInSchool')->andReturn($rolesInSchool);

        $this->permissionMatrix
            ->shouldReceive('getPermittedRoles')
            ->withArgs([$schoolId, Capabilities::CAN_UPDATE_ALL_PROGRAM_YEARS])
            ->andReturn([]);
        $this->permissionMatrix
            ->shouldReceive('hasPermission')
            ->withArgs([$schoolId, Capabilities::CAN_UPDATE_ALL_PROGRAM_YEARS, $rolesInSchool])
            ->andReturn(true);

        $this->assertTrue($this->permissionChecker->canUpdateProgramYear($sessionUser, $programYear));
    }

    public function testCanUpdateTheirProgramYears(): void
    {
        $rolesInSchool  = ['foo'];
        $rolesInProgramYear = ['bar'];
        $programYearId = 10;
        $schoolId = 20;
        $school = m::mock(SchoolInterface::class);
        $programYear = m::mock(ProgramYearInterface::class);
        $sessionUser = m::mock(SessionUserInterface::class);

        $school->shouldReceive('getId')->andReturn($schoolId);
        $programYear->shouldReceive('getSchool')->andReturn($school);
        $programYear->shouldReceive('getId')->andReturn($programYearId);
        $programYear->shouldReceive('isArchived')->andReturn(false);
        $programYear->shouldReceive('isLocked')->andReturn(false);
        $sessionUser->shouldReceive('isRoot')->andReturn(false);
        $sessionUser->shouldReceive('rolesInSchool')->andReturn($rolesInSchool);
        $sessionUser->shouldReceive('rolesInProgramYear')->andReturn($rolesInProgramYear);

        $this->permissionMatrix
            ->shouldReceive('getPermittedRoles')
            ->withArgs([$schoolId, Capabilities::CAN_UPDATE_ALL_PROGRAM_YEARS])
            ->andReturn([]);
        $this->permissionMatrix
            ->shouldReceive('hasPermission')
            ->withArgs([$schoolId, Capabilities::CAN_UPDATE_ALL_PROGRAM_YEARS, $rolesInSchool])
            ->andReturn(false);
        $this->permissionMatrix
            ->shouldReceive('getPermittedRoles')
            ->withArgs([$schoolId, Capabilities::CAN_UPDATE_THEIR_PROGRAM_YEARS])
            ->andReturn([]);
        $this->permissionMatrix
            ->shouldReceive('hasPermission')
            ->withArgs([$schoolId, Capabilities::CAN_UPDATE_THEIR_PROGRAM_YEARS, $rolesInProgramYear])
            ->andReturn(true);

        $this->assertTrue($this->permissionChecker->canUpdateProgramYear($sessionUser, $programYear));
    }

    public function testCanUpdateProgramYearsIfUserCanUpdateProgram(): void
    {
        $rolesInSchool  = ['foo'];
        $rolesInProgramYear = ['bar'];
        $programYearId = 10;
        $programId = 15;
        $schoolId = 20;
        $school = m::mock(SchoolInterface::class);
        $programYear = m::mock(ProgramYearInterface::class);
        $program = m::mock(ProgramInterface::class);
        $sessionUser = m::mock(SessionUserInterface::class);

        $school->shouldReceive('getId')->andReturn($schoolId);
        $program->shouldReceive('getId')->andReturn($programId);
        $programYear->shouldReceive('getSchool')->andReturn($school);
        $programYear->shouldReceive('getProgram')->andReturn($program);
        $programYear->shouldReceive('getId')->andReturn($programYearId);
        $programYear->shouldReceive('isArchived')->andReturn(false);
        $programYear->shouldReceive('isLocked')->andReturn(false);
        $sessionUser->shouldReceive('isRoot')->andReturn(false);
        $sessionUser->shouldReceive('rolesInSchool')->andReturn($rolesInSchool);
        $sessionUser->shouldReceive('rolesInProgramYear')->andReturn($rolesInProgramYear);

        $this->permissionMatrix
            ->shouldReceive('getPermittedRoles')
            ->withArgs([$schoolId, Capabilities::CAN_UPDATE_ALL_PROGRAM_YEARS])
            ->andReturn([]);
        $this->permissionMatrix
            ->shouldReceive('hasPermission')
            ->withArgs([$schoolId, Capabilities::CAN_UPDATE_ALL_PROGRAM_YEARS, $rolesInSchool])
            ->andReturn(false);
        $this->permissionMatrix
            ->shouldReceive('getPermittedRoles')
            ->withArgs([$schoolId, Capabilities::CAN_UPDATE_THEIR_PROGRAM_YEARS])
            ->andReturn([]);
        $this->permissionMatrix
            ->shouldReceive('hasPermission')
            ->withArgs([$schoolId, Capabilities::CAN_UPDATE_THEIR_PROGRAM_YEARS, $rolesInProgramYear])
            ->andReturn(false);
        $this->permissionMatrix
            ->shouldReceive('getPermittedRoles')
            ->withArgs([$schoolId, Capabilities::CAN_UPDATE_ALL_PROGRAMS])
            ->andReturn([]);
        $this->permissionMatrix
            ->shouldReceive('hasPermission')
            ->withArgs([$schoolId, Capabilities::CAN_UPDATE_ALL_PROGRAMS, $rolesInSchool])
            ->andReturn(true);

        $this->assertTrue($this->permissionChecker->canUpdateProgramYear($sessionUser, $programYear));
    }

    public function testCanNotUpdateProgramYears(): void
    {
        $rolesInSchool  = ['foo'];
        $rolesInProgramYear = ['bar'];
        $rolesInProgram = ['baz'];
        $programYearId = 10;
        $programId = 15;
        $schoolId = 20;
        $school = m::mock(SchoolInterface::class);
        $programYear = m::mock(ProgramYearInterface::class);
        $program = m::mock(ProgramInterface::class);
        $sessionUser = m::mock(SessionUserInterface::class);

        $school->shouldReceive('getId')->andReturn($schoolId);
        $program->shouldReceive('getId')->andReturn($programId);
        $programYear->shouldReceive('getSchool')->andReturn($school);
        $programYear->shouldReceive('getProgram')->andReturn($program);
        $programYear->shouldReceive('getId')->andReturn($programYearId);
        $programYear->shouldReceive('isArchived')->andReturn(false);
        $programYear->shouldReceive('isLocked')->andReturn(false);
        $sessionUser->shouldReceive('isRoot')->andReturn(false);
        $sessionUser->shouldReceive('rolesInSchool')->andReturn($rolesInSchool);
        $sessionUser->shouldReceive('rolesInProgram')->andReturn($rolesInProgram);
        $sessionUser->shouldReceive('rolesInProgramYear')->andReturn($rolesInProgramYear);

        $this->permissionMatrix
            ->shouldReceive('getPermittedRoles')
            ->withArgs([$schoolId, Capabilities::CAN_UPDATE_ALL_PROGRAM_YEARS])
            ->andReturn([]);
        $this->permissionMatrix
            ->shouldReceive('hasPermission')
            ->withArgs([$schoolId, Capabilities::CAN_UPDATE_ALL_PROGRAM_YEARS, $rolesInSchool])
            ->andReturn(false);
        $this->permissionMatrix
            ->shouldReceive('getPermittedRoles')
            ->withArgs([$schoolId, Capabilities::CAN_UPDATE_THEIR_PROGRAM_YEARS])
            ->andReturn([]);
        $this->permissionMatrix
            ->shouldReceive('hasPermission')
            ->withArgs([$schoolId, Capabilities::CAN_UPDATE_THEIR_PROGRAM_YEARS, $rolesInProgramYear])
            ->andReturn(false);
        $this->permissionMatrix
            ->shouldReceive('getPermittedRoles')
            ->withArgs([$schoolId, Capabilities::CAN_UPDATE_ALL_PROGRAMS])
            ->andReturn([]);
        $this->permissionMatrix
            ->shouldReceive('hasPermission')
            ->withArgs([$schoolId, Capabilities::CAN_UPDATE_ALL_PROGRAMS, $rolesInSchool])
            ->andReturn(false);
        $this->permissionMatrix
            ->shouldReceive('getPermittedRoles')
            ->withArgs([$schoolId, Capabilities::CAN_UPDATE_THEIR_PROGRAMS])
            ->andReturn([]);
        $this->permissionMatrix
            ->shouldReceive('hasPermission')
            ->withArgs([$schoolId, Capabilities::CAN_UPDATE_THEIR_PROGRAMS, $rolesInProgram])
            ->andReturn(false);

        $this->assertFalse($this->permissionChecker->canUpdateProgramYear($sessionUser, $programYear));
    }

    public function testCanNotUpdateLockedProgramYears(): void
    {
        $programYear = m::mock(ProgramYearInterface::class);
        $sessionUser = m::mock(SessionUserInterface::class);

        $programYear->shouldReceive('isArchived')->andReturn(false);
        $programYear->shouldReceive('isLocked')->andReturn(true);

        $this->assertFalse($this->permissionChecker->canUpdateProgramYear($sessionUser, $programYear));
    }

    public function testCanNotUpdateArchivedProgramYears(): void
    {
        $programYear = m::mock(ProgramYearInterface::class);
        $sessionUser = m::mock(SessionUserInterface::class);

        $programYear->shouldReceive('isArchived')->andReturn(true);
        $programYear->shouldReceive('isLocked')->andReturn(false);

        $this->assertFalse($this->permissionChecker->canUpdateProgramYear($sessionUser, $programYear));
    }

    public function testCanDeleteAllProgramYears(): void
    {
        $rolesInSchool = ['foo'];
        $programYearId = 20;
        $schoolId = 10;
        $school = m::mock(SchoolInterface::class);
        $programYear = m::mock(ProgramYearInterface::class);
        $sessionUser = m::mock(SessionUserInterface::class);

        $school->shouldReceive('getId')->andReturn($schoolId);
        $programYear->shouldReceive('getSchool')->andReturn($school);
        $programYear->shouldReceive('getId')->andReturn($programYearId);
        $programYear->shouldReceive('isArchived')->andReturn(false);
        $programYear->shouldReceive('isLocked')->andReturn(false);
        $sessionUser->shouldReceive('isRoot')->andReturn(false);
        $sessionUser->shouldReceive('rolesInSchool')->andReturn($rolesInSchool);

        $this->permissionMatrix
            ->shouldReceive('getPermittedRoles')
            ->withArgs([$schoolId, Capabilities::CAN_DELETE_ALL_PROGRAM_YEARS])
            ->andReturn([]);
        $this->permissionMatrix
            ->shouldReceive('hasPermission')
            ->withArgs([$schoolId, Capabilities::CAN_DELETE_ALL_PROGRAM_YEARS, $rolesInSchool])
            ->andReturn(true);

        $this->assertTrue($this->permissionChecker->canDeleteProgramYear($sessionUser, $programYear));
    }

    public function testCanDeleteTheirProgramYears(): void
    {
        $rolesInSchool  = ['foo'];
        $rolesInProgramYear = ['bar'];
        $programYearId = 20;
        $schoolId = 10;
        $school = m::mock(SchoolInterface::class);
        $programYear = m::mock(ProgramYearInterface::class);
        $sessionUser = m::mock(SessionUserInterface::class);

        $school->shouldReceive('getId')->andReturn($schoolId);
        $programYear->shouldReceive('getSchool')->andReturn($school);
        $programYear->shouldReceive('getId')->andReturn($programYearId);
        $programYear->shouldReceive('isArchived')->andReturn(false);
        $programYear->shouldReceive('isLocked')->andReturn(false);
        $sessionUser->shouldReceive('isRoot')->andReturn(false);
        $sessionUser->shouldReceive('rolesInSchool')->andReturn($rolesInSchool);
        $sessionUser->shouldReceive('rolesInProgramYear')->andReturn($rolesInProgramYear);

        $this->permissionMatrix
            ->shouldReceive('getPermittedRoles')
            ->withArgs([$schoolId, Capabilities::CAN_DELETE_ALL_PROGRAM_YEARS])
            ->andReturn([]);
        $this->permissionMatrix
            ->shouldReceive('hasPermission')
            ->withArgs([$schoolId, Capabilities::CAN_DELETE_ALL_PROGRAM_YEARS, $rolesInSchool])
            ->andReturn(false);
        $this->permissionMatrix
            ->shouldReceive('getPermittedRoles')
            ->withArgs([$schoolId, Capabilities::CAN_DELETE_THEIR_PROGRAM_YEARS])
            ->andReturn([]);
        $this->permissionMatrix
            ->shouldReceive('hasPermission')
            ->withArgs([$schoolId, Capabilities::CAN_DELETE_THEIR_PROGRAM_YEARS, $rolesInProgramYear])
            ->andReturn(true);

        $this->assertTrue($this->permissionChecker->canDeleteProgramYear($sessionUser, $programYear));
    }

    public function testCanDeleteProgramYearsIfUserCanUpdateProgram(): void
    {
        $rolesInSchool  = ['foo'];
        $rolesInProgramYear = ['bar'];
        $programYearId = 20;
        $programId = 15;
        $schoolId = 10;
        $school = m::mock(SchoolInterface::class);
        $programYear = m::mock(ProgramYearInterface::class);
        $program = m::mock(ProgramInterface::class);
        $sessionUser = m::mock(SessionUserInterface::class);

        $school->shouldReceive('getId')->andReturn($schoolId);
        $program->shouldReceive('getId')->andReturn($programId);
        $programYear->shouldReceive('getProgram')->andReturn($program);
        $programYear->shouldReceive('getSchool')->andReturn($school);
        $programYear->shouldReceive('getId')->andReturn($programYearId);
        $programYear->shouldReceive('isArchived')->andReturn(false);
        $programYear->shouldReceive('isLocked')->andReturn(false);
        $sessionUser->shouldReceive('isRoot')->andReturn(false);
        $sessionUser->shouldReceive('rolesInSchool')->andReturn($rolesInSchool);
        $sessionUser->shouldReceive('rolesInProgramYear')->andReturn($rolesInProgramYear);

        $this->permissionMatrix
            ->shouldReceive('getPermittedRoles')
            ->withArgs([$schoolId, Capabilities::CAN_DELETE_ALL_PROGRAM_YEARS])
            ->andReturn([]);
        $this->permissionMatrix
            ->shouldReceive('hasPermission')
            ->withArgs([$schoolId, Capabilities::CAN_DELETE_ALL_PROGRAM_YEARS, $rolesInSchool])
            ->andReturn(false);
        $this->permissionMatrix
            ->shouldReceive('getPermittedRoles')
            ->withArgs([$schoolId, Capabilities::CAN_DELETE_THEIR_PROGRAM_YEARS])
            ->andReturn([]);
        $this->permissionMatrix
            ->shouldReceive('hasPermission')
            ->withArgs([$schoolId, Capabilities::CAN_DELETE_THEIR_PROGRAM_YEARS, $rolesInProgramYear])
            ->andReturn(false);
        $this->permissionMatrix
            ->shouldReceive('getPermittedRoles')
            ->withArgs([$schoolId, Capabilities::CAN_UPDATE_ALL_PROGRAMS])
            ->andReturn([]);
        $this->permissionMatrix
            ->shouldReceive('hasPermission')
            ->withArgs([$schoolId, Capabilities::CAN_UPDATE_ALL_PROGRAMS, $rolesInSchool])
            ->andReturn(true);


        $this->assertTrue($this->permissionChecker->canDeleteProgramYear($sessionUser, $programYear));
    }

    public function testCanNotDeleteProgramYears(): void
    {
        $rolesInSchool  = ['foo'];
        $rolesInProgramYear = ['bar'];
        $rolesInProgram = ['baz'];
        $programYearId = 20;
        $programId = 15;
        $schoolId = 10;
        $school = m::mock(SchoolInterface::class);
        $programYear = m::mock(ProgramYearInterface::class);
        $program = m::mock(ProgramInterface::class);
        $sessionUser = m::mock(SessionUserInterface::class);

        $school->shouldReceive('getId')->andReturn($schoolId);
        $program->shouldReceive('getId')->andReturn($programId);
        $programYear->shouldReceive('getProgram')->andReturn($program);
        $programYear->shouldReceive('getSchool')->andReturn($school);
        $programYear->shouldReceive('getId')->andReturn($programYearId);
        $programYear->shouldReceive('isArchived')->andReturn(false);
        $programYear->shouldReceive('isLocked')->andReturn(false);
        $sessionUser->shouldReceive('isRoot')->andReturn(false);
        $sessionUser->shouldReceive('rolesInSchool')->andReturn($rolesInSchool);
        $sessionUser->shouldReceive('rolesInProgramYear')->andReturn($rolesInProgramYear);
        $sessionUser->shouldReceive('rolesInProgram')->andReturn($rolesInProgram);

        $this->permissionMatrix
            ->shouldReceive('getPermittedRoles')
            ->withArgs([$schoolId, Capabilities::CAN_DELETE_ALL_PROGRAM_YEARS])
            ->andReturn([]);
        $this->permissionMatrix
            ->shouldReceive('hasPermission')
            ->withArgs([$schoolId, Capabilities::CAN_DELETE_ALL_PROGRAM_YEARS, $rolesInSchool])
            ->andReturn(false);
        $this->permissionMatrix
            ->shouldReceive('getPermittedRoles')
            ->withArgs([$schoolId, Capabilities::CAN_DELETE_THEIR_PROGRAM_YEARS])
            ->andReturn([]);
        $this->permissionMatrix
            ->shouldReceive('hasPermission')
            ->withArgs([$schoolId, Capabilities::CAN_DELETE_THEIR_PROGRAM_YEARS, $rolesInProgramYear])
            ->andReturn(false);
        $this->permissionMatrix
            ->shouldReceive('getPermittedRoles')
            ->withArgs([$schoolId, Capabilities::CAN_UPDATE_ALL_PROGRAMS])
            ->andReturn([]);
        $this->permissionMatrix
            ->shouldReceive('hasPermission')
            ->withArgs([$schoolId, Capabilities::CAN_UPDATE_ALL_PROGRAMS, $rolesInSchool])
            ->andReturn(false);
        $this->permissionMatrix
            ->shouldReceive('getPermittedRoles')
            ->withArgs([$schoolId, Capabilities::CAN_UPDATE_THEIR_PROGRAMS])
            ->andReturn([]);
        $this->permissionMatrix
            ->shouldReceive('hasPermission')
            ->withArgs([$schoolId, Capabilities::CAN_UPDATE_THEIR_PROGRAMS, $rolesInProgram])
            ->andReturn(false);

        $this->assertFalse($this->permissionChecker->canDeleteProgramYear($sessionUser, $programYear));
    }

    public function testCanNotDeleteLockedProgramYears(): void
    {
        $programYear = m::mock(ProgramYearInterface::class);
        $sessionUser = m::mock(SessionUserInterface::class);

        $programYear->shouldReceive('isArchived')->andReturn(false);
        $programYear->shouldReceive('isLocked')->andReturn(true);

        $this->assertFalse($this->permissionChecker->canDeleteProgramYear($sessionUser, $programYear));
    }

    public function testCanNotDeleteArchivedProgramYears(): void
    {
        $programYear = m::mock(ProgramYearInterface::class);
        $sessionUser = m::mock(SessionUserInterface::class);

        $programYear->shouldReceive('isArchived')->andReturn(true);
        $programYear->shouldReceive('isLocked')->andReturn(false);

        $this->assertFalse($this->permissionChecker->canDeleteProgramYear($sessionUser, $programYear));
    }

    public function testCanCreateProgramYear(): void
    {
        $programId = 20;
        $schoolId = 10;
        $rolesInSchool = ['foo'];
        $program = m::mock(ProgramInterface::class);
        $school = m::mock(SchoolInterface::class);
        $sessionUser = m::mock(SessionUserInterface::class);

        $school->shouldReceive(('getId'))->andReturn($schoolId);
        $program->shouldReceive('getId')->andReturn($programId);
        $program->shouldReceive('getSchool')->andReturn($school);
        $sessionUser->shouldReceive('isRoot')->andReturn(false);
        $sessionUser->shouldReceive('rolesInSchool')->andReturn($rolesInSchool);

        $this->permissionMatrix
            ->shouldReceive('getPermittedRoles')
            ->withArgs([$schoolId, Capabilities::CAN_CREATE_PROGRAM_YEARS])
            ->andReturn([]);
        $this->permissionMatrix
            ->shouldReceive('hasPermission')
            ->withArgs([$schoolId, Capabilities::CAN_CREATE_PROGRAM_YEARS, $rolesInSchool])
            ->andReturn(true);

        $this->assertTrue($this->permissionChecker->canCreateProgramYear($sessionUser, $program));
    }

    public function testCanCreateProgramYearIfUserCanUpdateProgram(): void
    {
        $programId = 20;
        $schoolId = 10;
        $rolesInSchool = ['foo'];
        $program = m::mock(ProgramInterface::class);
        $school = m::mock(SchoolInterface::class);
        $sessionUser = m::mock(SessionUserInterface::class);

        $school->shouldReceive(('getId'))->andReturn($schoolId);
        $program->shouldReceive('getId')->andReturn($programId);
        $program->shouldReceive('getSchool')->andReturn($school);
        $sessionUser->shouldReceive('isRoot')->andReturn(false);
        $sessionUser->shouldReceive('rolesInSchool')->andReturn($rolesInSchool);

        $this->permissionMatrix
            ->shouldReceive('getPermittedRoles')
            ->withArgs([$schoolId, Capabilities::CAN_CREATE_PROGRAM_YEARS])
            ->andReturn([]);
        $this->permissionMatrix
            ->shouldReceive('hasPermission')
            ->withArgs([$schoolId, Capabilities::CAN_CREATE_PROGRAM_YEARS, $rolesInSchool])
            ->andReturn(false);
        $this->permissionMatrix
            ->shouldReceive('getPermittedRoles')
            ->withArgs([$schoolId, Capabilities::CAN_UPDATE_ALL_PROGRAMS])
            ->andReturn([]);
        $this->permissionMatrix
            ->shouldReceive('hasPermission')
            ->withArgs([$schoolId, Capabilities::CAN_UPDATE_ALL_PROGRAMS, $rolesInSchool])
            ->andReturn(true);

        $this->assertTrue($this->permissionChecker->canCreateProgramYear($sessionUser, $program));
    }

    public function testCanNotCreateProgramYear(): void
    {
        $programId = 20;
        $schoolId = 10;
        $rolesInSchool = ['foo'];
        $rolesInProgram = ['bar'];
        $program = m::mock(ProgramInterface::class);
        $school = m::mock(SchoolInterface::class);
        $sessionUser = m::mock(SessionUserInterface::class);

        $school->shouldReceive(('getId'))->andReturn($schoolId);
        $program->shouldReceive('getId')->andReturn($programId);
        $program->shouldReceive('getSchool')->andReturn($school);
        $sessionUser->shouldReceive('isRoot')->andReturn(false);
        $sessionUser->shouldReceive('rolesInSchool')->andReturn($rolesInSchool);
        $sessionUser->shouldReceive('rolesInProgram')->andReturn($rolesInProgram);


        $this->permissionMatrix
            ->shouldReceive('getPermittedRoles')
            ->withArgs([$schoolId, Capabilities::CAN_CREATE_PROGRAM_YEARS])
            ->andReturn([]);
        $this->permissionMatrix
            ->shouldReceive('hasPermission')
            ->withArgs([$schoolId, Capabilities::CAN_CREATE_PROGRAM_YEARS, $rolesInSchool])
            ->andReturn(false);
        $this->permissionMatrix
            ->shouldReceive('getPermittedRoles')
            ->withArgs([$schoolId, Capabilities::CAN_UPDATE_ALL_PROGRAMS])
            ->andReturn([]);
        $this->permissionMatrix
            ->shouldReceive('hasPermission')
            ->withArgs([$schoolId, Capabilities::CAN_UPDATE_ALL_PROGRAMS, $rolesInSchool])
            ->andReturn(false);
        $this->permissionMatrix
            ->shouldReceive('getPermittedRoles')
            ->withArgs([$schoolId, Capabilities::CAN_UPDATE_THEIR_PROGRAMS])
            ->andReturn([]);
        $this->permissionMatrix
            ->shouldReceive('hasPermission')
            ->withArgs([$schoolId, Capabilities::CAN_UPDATE_THEIR_PROGRAMS, $rolesInProgram])
            ->andReturn(false);

        $this->assertFalse($this->permissionChecker->canCreateProgramYear($sessionUser, $program));
    }

    public function testCanLockAllProgramYears(): void
    {
        $rolesInSchool = ['foo'];
        $programYearId = 10;
        $schoolId = 20;
        $school = m::mock(SchoolInterface::class);
        $programYear = m::mock(ProgramYearInterface::class);
        $sessionUser = m::mock(SessionUserInterface::class);

        $school->shouldReceive('getId')->andReturn($schoolId);
        $programYear->shouldReceive('getSchool')->andReturn($school);
        $programYear->shouldReceive('getId')->andReturn($programYearId);
        $programYear->shouldReceive('isArchived')->andReturn(false);
        $sessionUser->shouldReceive('isRoot')->andReturn(false);
        $sessionUser->shouldReceive('rolesInSchool')->andReturn($rolesInSchool);

        $this->permissionMatrix
            ->shouldReceive('getPermittedRoles')
            ->withArgs([$schoolId, Capabilities::CAN_LOCK_ALL_PROGRAM_YEARS])
            ->andReturn([]);
        $this->permissionMatrix
            ->shouldReceive('hasPermission')
            ->withArgs([$schoolId, Capabilities::CAN_LOCK_ALL_PROGRAM_YEARS, $rolesInSchool])
            ->andReturn(true);

        $this->assertTrue($this->permissionChecker->canLockProgramYear($sessionUser, $programYear));
    }

    public function testCanLockTheirProgramYears(): void
    {
        $rolesInSchool  = ['foo'];
        $rolesInProgramYear = ['bar'];
        $programYearId = 10;
        $schoolId = 20;
        $school = m::mock(SchoolInterface::class);
        $programYear = m::mock(ProgramYearInterface::class);
        $sessionUser = m::mock(SessionUserInterface::class);

        $school->shouldReceive('getId')->andReturn($schoolId);
        $programYear->shouldReceive('getSchool')->andReturn($school);
        $programYear->shouldReceive('getId')->andReturn($programYearId);
        $programYear->shouldReceive('isArchived')->andReturn(false);
        $sessionUser->shouldReceive('isRoot')->andReturn(false);
        $sessionUser->shouldReceive('rolesInSchool')->andReturn($rolesInSchool);
        $sessionUser->shouldReceive('rolesInProgramYear')->andReturn($rolesInProgramYear);

        $this->permissionMatrix
            ->shouldReceive('getPermittedRoles')
            ->withArgs([$schoolId, Capabilities::CAN_LOCK_ALL_PROGRAM_YEARS])
            ->andReturn([]);
        $this->permissionMatrix
            ->shouldReceive('hasPermission')
            ->withArgs([$schoolId, Capabilities::CAN_LOCK_ALL_PROGRAM_YEARS, $rolesInSchool])
            ->andReturn(false);
        $this->permissionMatrix
            ->shouldReceive('getPermittedRoles')
            ->withArgs([$schoolId, Capabilities::CAN_LOCK_THEIR_PROGRAM_YEARS])
            ->andReturn([]);
        $this->permissionMatrix
            ->shouldReceive('hasPermission')
            ->withArgs([$schoolId, Capabilities::CAN_LOCK_THEIR_PROGRAM_YEARS, $rolesInProgramYear])
            ->andReturn(true);

        $this->assertTrue($this->permissionChecker->canLockProgramYear($sessionUser, $programYear));
    }

    public function testCanLockProgramYearsIfUserCanUpdateProgram(): void
    {
        $rolesInSchool  = ['foo'];
        $rolesInProgramYear = ['bar'];
        $programYearId = 10;
        $programId = 15;
        $schoolId = 20;
        $school = m::mock(SchoolInterface::class);
        $programYear = m::mock(ProgramYearInterface::class);
        $program = m::mock(ProgramInterface::class);
        $sessionUser = m::mock(SessionUserInterface::class);

        $school->shouldReceive('getId')->andReturn($schoolId);
        $program->shouldReceive('getId')->andReturn($programId);
        $programYear->shouldReceive('getSchool')->andReturn($school);
        $programYear->shouldReceive('getProgram')->andReturn($program);
        $programYear->shouldReceive('getId')->andReturn($programYearId);
        $programYear->shouldReceive('isArchived')->andReturn(false);
        $sessionUser->shouldReceive('isRoot')->andReturn(false);
        $sessionUser->shouldReceive('rolesInSchool')->andReturn($rolesInSchool);
        $sessionUser->shouldReceive('rolesInProgramYear')->andReturn($rolesInProgramYear);

        $this->permissionMatrix
            ->shouldReceive('getPermittedRoles')
            ->withArgs([$schoolId, Capabilities::CAN_LOCK_ALL_PROGRAM_YEARS])
            ->andReturn([]);
        $this->permissionMatrix
            ->shouldReceive('hasPermission')
            ->withArgs([$schoolId, Capabilities::CAN_LOCK_ALL_PROGRAM_YEARS, $rolesInSchool])
            ->andReturn(false);
        $this->permissionMatrix
            ->shouldReceive('getPermittedRoles')
            ->withArgs([$schoolId, Capabilities::CAN_LOCK_THEIR_PROGRAM_YEARS])
            ->andReturn([]);
        $this->permissionMatrix
            ->shouldReceive('hasPermission')
            ->withArgs([$schoolId, Capabilities::CAN_LOCK_THEIR_PROGRAM_YEARS, $rolesInProgramYear])
            ->andReturn(false);
        $this->permissionMatrix
            ->shouldReceive('getPermittedRoles')
            ->withArgs([$schoolId, Capabilities::CAN_UPDATE_ALL_PROGRAMS])
            ->andReturn([]);
        $this->permissionMatrix
            ->shouldReceive('hasPermission')
            ->withArgs([$schoolId, Capabilities::CAN_UPDATE_ALL_PROGRAMS, $rolesInSchool])
            ->andReturn(true);

        $this->assertTrue($this->permissionChecker->canLockProgramYear($sessionUser, $programYear));
    }

    public function testCanNotLockProgramYearIfProgramYearIsArchived(): void
    {
        $programYear = m::mock(ProgramYearInterface::class);
        $programYear->shouldReceive('isArchived')->andReturn(true);
        $sessionUser = m::mock(SessionUserInterface::class);


        $this->assertFalse($this->permissionChecker->canLockProgramYear($sessionUser, $programYear));
    }

    public function testCanUnlockAllProgramYears(): void
    {
        $rolesInSchool = ['foo'];
        $programYearId = 10;
        $schoolId = 20;
        $school = m::mock(SchoolInterface::class);
        $programYear = m::mock(ProgramYearInterface::class);
        $sessionUser = m::mock(SessionUserInterface::class);

        $school->shouldReceive('getId')->andReturn($schoolId);
        $programYear->shouldReceive('getSchool')->andReturn($school);
        $programYear->shouldReceive('getId')->andReturn($programYearId);
        $programYear->shouldReceive('isArchived')->andReturn(false);
        $sessionUser->shouldReceive('isRoot')->andReturn(false);
        $sessionUser->shouldReceive('rolesInSchool')->andReturn($rolesInSchool);

        $this->permissionMatrix
            ->shouldReceive('getPermittedRoles')
            ->withArgs([$schoolId, Capabilities::CAN_UNLOCK_ALL_PROGRAM_YEARS])
            ->andReturn([]);
        $this->permissionMatrix
            ->shouldReceive('hasPermission')
            ->withArgs([$schoolId, Capabilities::CAN_UNLOCK_ALL_PROGRAM_YEARS, $rolesInSchool])
            ->andReturn(true);

        $this->assertTrue($this->permissionChecker->canUnlockProgramYear($sessionUser, $programYear));
    }

    public function testCanUnlockTheirProgramYears(): void
    {
        $rolesInSchool  = ['foo'];
        $rolesInProgramYear = ['bar'];
        $programYearId = 10;
        $schoolId = 20;
        $school = m::mock(SchoolInterface::class);
        $programYear = m::mock(ProgramYearInterface::class);
        $sessionUser = m::mock(SessionUserInterface::class);

        $school->shouldReceive('getId')->andReturn($schoolId);
        $programYear->shouldReceive('getSchool')->andReturn($school);
        $programYear->shouldReceive('getId')->andReturn($programYearId);
        $programYear->shouldReceive('isArchived')->andReturn(false);
        $sessionUser->shouldReceive('isRoot')->andReturn(false);
        $sessionUser->shouldReceive('rolesInSchool')->andReturn($rolesInSchool);
        $sessionUser->shouldReceive('rolesInProgramYear')->andReturn($rolesInProgramYear);

        $this->permissionMatrix
            ->shouldReceive('getPermittedRoles')
            ->withArgs([$schoolId, Capabilities::CAN_UNLOCK_ALL_PROGRAM_YEARS])
            ->andReturn([]);
        $this->permissionMatrix
            ->shouldReceive('hasPermission')
            ->withArgs([$schoolId, Capabilities::CAN_UNLOCK_ALL_PROGRAM_YEARS, $rolesInSchool])
            ->andReturn(false);
        $this->permissionMatrix
            ->shouldReceive('getPermittedRoles')
            ->withArgs([$schoolId, Capabilities::CAN_UNLOCK_THEIR_PROGRAM_YEARS])
            ->andReturn([]);
        $this->permissionMatrix
            ->shouldReceive('hasPermission')
            ->withArgs([$schoolId, Capabilities::CAN_UNLOCK_THEIR_PROGRAM_YEARS, $rolesInProgramYear])
            ->andReturn(true);

        $this->assertTrue($this->permissionChecker->canUnlockProgramYear($sessionUser, $programYear));
    }

    public function testCanUnlockProgramYearsIfUserCanUpdateProgram(): void
    {
        $rolesInSchool  = ['foo'];
        $rolesInProgramYear = ['bar'];
        $programYearId = 10;
        $programId = 15;
        $schoolId = 20;
        $school = m::mock(SchoolInterface::class);
        $programYear = m::mock(ProgramYearInterface::class);
        $program = m::mock(ProgramInterface::class);
        $sessionUser = m::mock(SessionUserInterface::class);

        $school->shouldReceive('getId')->andReturn($schoolId);
        $program->shouldReceive('getId')->andReturn($programId);
        $programYear->shouldReceive('getSchool')->andReturn($school);
        $programYear->shouldReceive('getProgram')->andReturn($program);
        $programYear->shouldReceive('getId')->andReturn($programYearId);
        $programYear->shouldReceive('isArchived')->andReturn(false);
        $sessionUser->shouldReceive('isRoot')->andReturn(false);
        $sessionUser->shouldReceive('rolesInSchool')->andReturn($rolesInSchool);
        $sessionUser->shouldReceive('rolesInProgramYear')->andReturn($rolesInProgramYear);

        $this->permissionMatrix
            ->shouldReceive('getPermittedRoles')
            ->withArgs([$schoolId, Capabilities::CAN_UNLOCK_ALL_PROGRAM_YEARS])
            ->andReturn([]);
        $this->permissionMatrix
            ->shouldReceive('hasPermission')
            ->withArgs([$schoolId, Capabilities::CAN_UNLOCK_ALL_PROGRAM_YEARS, $rolesInSchool])
            ->andReturn(false);
        $this->permissionMatrix
            ->shouldReceive('getPermittedRoles')
            ->withArgs([$schoolId, Capabilities::CAN_UNLOCK_THEIR_PROGRAM_YEARS])
            ->andReturn([]);
        $this->permissionMatrix
            ->shouldReceive('hasPermission')
            ->withArgs([$schoolId, Capabilities::CAN_UNLOCK_THEIR_PROGRAM_YEARS, $rolesInProgramYear])
            ->andReturn(false);
        $this->permissionMatrix
            ->shouldReceive('getPermittedRoles')
            ->withArgs([$schoolId, Capabilities::CAN_UPDATE_ALL_PROGRAMS])
            ->andReturn([]);
        $this->permissionMatrix
            ->shouldReceive('hasPermission')
            ->withArgs([$schoolId, Capabilities::CAN_UPDATE_ALL_PROGRAMS, $rolesInSchool])
            ->andReturn(true);

        $this->assertTrue($this->permissionChecker->canUnlockProgramYear($sessionUser, $programYear));
    }

    public function testCanNotUnlockProgramYearIfProgramYearIsArchived(): void
    {
        $programYear = m::mock(ProgramYearInterface::class);
        $programYear->shouldReceive('isArchived')->andReturn(true);
        $sessionUser = m::mock(SessionUserInterface::class);


        $this->assertFalse($this->permissionChecker->canUnlockProgramYear($sessionUser, $programYear));
    }

    public function testCanArchiveAllProgramYears(): void
    {
        $rolesInSchool = ['foo'];
        $programYearId = 10;
        $schoolId = 20;
        $school = m::mock(SchoolInterface::class);
        $programYear = m::mock(ProgramYearInterface::class);
        $sessionUser = m::mock(SessionUserInterface::class);

        $school->shouldReceive('getId')->andReturn($schoolId);
        $programYear->shouldReceive('getSchool')->andReturn($school);
        $programYear->shouldReceive('getId')->andReturn($programYearId);
        $sessionUser->shouldReceive('isRoot')->andReturn(false);
        $sessionUser->shouldReceive('rolesInSchool')->andReturn($rolesInSchool);

        $this->permissionMatrix
            ->shouldReceive('getPermittedRoles')
            ->withArgs([$schoolId, Capabilities::CAN_ARCHIVE_ALL_PROGRAM_YEARS])
            ->andReturn([]);
        $this->permissionMatrix
            ->shouldReceive('hasPermission')
            ->withArgs([$schoolId, Capabilities::CAN_ARCHIVE_ALL_PROGRAM_YEARS, $rolesInSchool])
            ->andReturn(true);

        $this->assertTrue($this->permissionChecker->canArchiveProgramYear($sessionUser, $programYear));
    }

    public function testCanArchiveTheirProgramYears(): void
    {
        $rolesInSchool  = ['foo'];
        $rolesInProgramYear = ['bar'];
        $programYearId = 10;
        $schoolId = 20;
        $school = m::mock(SchoolInterface::class);
        $programYear = m::mock(ProgramYearInterface::class);
        $sessionUser = m::mock(SessionUserInterface::class);

        $school->shouldReceive('getId')->andReturn($schoolId);
        $programYear->shouldReceive('getSchool')->andReturn($school);
        $programYear->shouldReceive('getId')->andReturn($programYearId);
        $sessionUser->shouldReceive('isRoot')->andReturn(false);
        $sessionUser->shouldReceive('rolesInSchool')->andReturn($rolesInSchool);
        $sessionUser->shouldReceive('rolesInProgramYear')->andReturn($rolesInProgramYear);

        $this->permissionMatrix
            ->shouldReceive('getPermittedRoles')
            ->withArgs([$schoolId, Capabilities::CAN_ARCHIVE_ALL_PROGRAM_YEARS])
            ->andReturn([]);
        $this->permissionMatrix
            ->shouldReceive('hasPermission')
            ->withArgs([$schoolId, Capabilities::CAN_ARCHIVE_ALL_PROGRAM_YEARS, $rolesInSchool])
            ->andReturn(false);
        $this->permissionMatrix
            ->shouldReceive('getPermittedRoles')
            ->withArgs([$schoolId, Capabilities::CAN_ARCHIVE_THEIR_PROGRAM_YEARS])
            ->andReturn([]);
        $this->permissionMatrix
            ->shouldReceive('hasPermission')
            ->withArgs([$schoolId, Capabilities::CAN_ARCHIVE_THEIR_PROGRAM_YEARS, $rolesInProgramYear])
            ->andReturn(true);

        $this->assertTrue($this->permissionChecker->canArchiveProgramYear($sessionUser, $programYear));
    }

    public function testCanArchiveProgramYearsIfUserCanUpdateProgram(): void
    {
        $rolesInSchool  = ['foo'];
        $rolesInProgramYear = ['bar'];
        $programYearId = 10;
        $programId = 15;
        $schoolId = 20;
        $school = m::mock(SchoolInterface::class);
        $programYear = m::mock(ProgramYearInterface::class);
        $program = m::mock(ProgramInterface::class);
        $sessionUser = m::mock(SessionUserInterface::class);

        $school->shouldReceive('getId')->andReturn($schoolId);
        $program->shouldReceive('getId')->andReturn($programId);
        $programYear->shouldReceive('getSchool')->andReturn($school);
        $programYear->shouldReceive('getProgram')->andReturn($program);
        $programYear->shouldReceive('getId')->andReturn($programYearId);
        $sessionUser->shouldReceive('isRoot')->andReturn(false);
        $sessionUser->shouldReceive('rolesInSchool')->andReturn($rolesInSchool);
        $sessionUser->shouldReceive('rolesInProgramYear')->andReturn($rolesInProgramYear);

        $this->permissionMatrix
            ->shouldReceive('getPermittedRoles')
            ->withArgs([$schoolId, Capabilities::CAN_ARCHIVE_ALL_PROGRAM_YEARS])
            ->andReturn([]);
        $this->permissionMatrix
            ->shouldReceive('hasPermission')
            ->withArgs([$schoolId, Capabilities::CAN_ARCHIVE_ALL_PROGRAM_YEARS, $rolesInSchool])
            ->andReturn(false);
        $this->permissionMatrix
            ->shouldReceive('getPermittedRoles')
            ->withArgs([$schoolId, Capabilities::CAN_ARCHIVE_THEIR_PROGRAM_YEARS])
            ->andReturn([]);
        $this->permissionMatrix
            ->shouldReceive('hasPermission')
            ->withArgs([$schoolId, Capabilities::CAN_ARCHIVE_THEIR_PROGRAM_YEARS, $rolesInProgramYear])
            ->andReturn(false);
        $this->permissionMatrix
            ->shouldReceive('getPermittedRoles')
            ->withArgs([$schoolId, Capabilities::CAN_UPDATE_ALL_PROGRAMS])
            ->andReturn([]);
        $this->permissionMatrix
            ->shouldReceive('hasPermission')
            ->withArgs([$schoolId, Capabilities::CAN_UPDATE_ALL_PROGRAMS, $rolesInSchool])
            ->andReturn(true);

        $this->assertTrue($this->permissionChecker->canArchiveProgramYear($sessionUser, $programYear));
    }

    public function testCanUpdateSchoolConfig(): void
    {
        $schoolId = 10;
        $rolesInSchool = ['foo'];
        $sessionUser = m::mock(SessionUserInterface::class);

        $sessionUser->shouldReceive('isRoot')->andReturn(false);
        $sessionUser->shouldReceive('rolesInSchool')->andReturn($rolesInSchool);
        $this->permissionMatrix
            ->shouldReceive('getPermittedRoles')
            ->withArgs([$schoolId, Capabilities::CAN_UPDATE_SCHOOL_CONFIGS])
            ->andReturn([]);
        $this->permissionMatrix
            ->shouldReceive('hasPermission')
            ->withArgs([$schoolId, Capabilities::CAN_UPDATE_SCHOOL_CONFIGS, $rolesInSchool])
            ->andReturn(true);

        $this->assertTrue($this->permissionChecker->canUpdateSchoolConfig($sessionUser, $schoolId));
    }

    public function testCanNotUpdateSchoolConfig(): void
    {
        $schoolId = 10;
        $rolesInSchool = ['foo'];
        $sessionUser = m::mock(SessionUserInterface::class);

        $sessionUser->shouldReceive('isRoot')->andReturn(false);
        $sessionUser->shouldReceive('rolesInSchool')->andReturn($rolesInSchool);
        $this->permissionMatrix
            ->shouldReceive('getPermittedRoles')
            ->withArgs([$schoolId, Capabilities::CAN_UPDATE_SCHOOL_CONFIGS])
            ->andReturn([]);
        $this->permissionMatrix
            ->shouldReceive('hasPermission')
            ->withArgs([$schoolId, Capabilities::CAN_UPDATE_SCHOOL_CONFIGS, $rolesInSchool])
            ->andReturn(false);

        $this->assertFalse($this->permissionChecker->canUpdateSchoolConfig($sessionUser, $schoolId));
    }

    public function testCanDeleteSchoolConfig(): void
    {
        $schoolId = 10;
        $rolesInSchool = ['foo'];
        $sessionUser = m::mock(SessionUserInterface::class);

        $sessionUser->shouldReceive('isRoot')->andReturn(false);
        $sessionUser->shouldReceive('rolesInSchool')->andReturn($rolesInSchool);
        $this->permissionMatrix
            ->shouldReceive('getPermittedRoles')
            ->withArgs([$schoolId, Capabilities::CAN_DELETE_SCHOOL_CONFIGS])
            ->andReturn([]);
        $this->permissionMatrix
            ->shouldReceive('hasPermission')
            ->withArgs([$schoolId, Capabilities::CAN_DELETE_SCHOOL_CONFIGS, $rolesInSchool])
            ->andReturn(true);

        $this->assertTrue($this->permissionChecker->canDeleteSchoolConfig($sessionUser, $schoolId));
    }

    public function testCanNotDeleteSchoolConfig(): void
    {
        $schoolId = 10;
        $rolesInSchool = ['foo'];
        $sessionUser = m::mock(SessionUserInterface::class);

        $sessionUser->shouldReceive('isRoot')->andReturn(false);
        $sessionUser->shouldReceive('rolesInSchool')->andReturn($rolesInSchool);
        $this->permissionMatrix
            ->shouldReceive('getPermittedRoles')
            ->withArgs([$schoolId, Capabilities::CAN_DELETE_SCHOOL_CONFIGS])
            ->andReturn([]);
        $this->permissionMatrix
            ->shouldReceive('hasPermission')
            ->withArgs([$schoolId, Capabilities::CAN_DELETE_SCHOOL_CONFIGS, $rolesInSchool])
            ->andReturn(false);

        $this->assertFalse($this->permissionChecker->canDeleteSchoolConfig($sessionUser, $schoolId));
    }

    public function testCanCreateSchoolConfig(): void
    {
        $schoolId = 10;
        $rolesInSchool = ['foo'];
        $sessionUser = m::mock(SessionUserInterface::class);

        $sessionUser->shouldReceive('isRoot')->andReturn(false);
        $sessionUser->shouldReceive('rolesInSchool')->andReturn($rolesInSchool);
        $this->permissionMatrix
            ->shouldReceive('getPermittedRoles')
            ->withArgs([$schoolId, Capabilities::CAN_CREATE_SCHOOL_CONFIGS])
            ->andReturn([]);
        $this->permissionMatrix
            ->shouldReceive('hasPermission')
            ->withArgs([$schoolId, Capabilities::CAN_CREATE_SCHOOL_CONFIGS, $rolesInSchool])
            ->andReturn(true);

        $this->assertTrue($this->permissionChecker->canCreateSchoolConfig($sessionUser, $schoolId));
    }

    public function testCanNotCreateSchoolConfig(): void
    {
        $schoolId = 10;
        $rolesInSchool = ['foo'];
        $sessionUser = m::mock(SessionUserInterface::class);

        $sessionUser->shouldReceive('isRoot')->andReturn(false);
        $sessionUser->shouldReceive('rolesInSchool')->andReturn($rolesInSchool);
        $this->permissionMatrix
            ->shouldReceive('getPermittedRoles')
            ->withArgs([$schoolId, Capabilities::CAN_CREATE_SCHOOL_CONFIGS])
            ->andReturn([]);
        $this->permissionMatrix
            ->shouldReceive('hasPermission')
            ->withArgs([$schoolId, Capabilities::CAN_CREATE_SCHOOL_CONFIGS, $rolesInSchool])
            ->andReturn(false);

        $this->assertFalse($this->permissionChecker->canCreateSchoolConfig($sessionUser, $schoolId));
    }

    public function testCanUpdateSchool(): void
    {
        $schoolId = 10;
        $rolesInSchool = ['foo'];
        $sessionUser = m::mock(SessionUserInterface::class);

        $sessionUser->shouldReceive('isRoot')->andReturn(false);
        $sessionUser->shouldReceive('rolesInSchool')->andReturn($rolesInSchool);
        $this->permissionMatrix
            ->shouldReceive('getPermittedRoles')
            ->withArgs([$schoolId, Capabilities::CAN_UPDATE_SCHOOLS])
            ->andReturn([]);
        $this->permissionMatrix
            ->shouldReceive('hasPermission')
            ->withArgs([$schoolId, Capabilities::CAN_UPDATE_SCHOOLS, $rolesInSchool])
            ->andReturn(true);

        $this->assertTrue($this->permissionChecker->canUpdateSchool($sessionUser, $schoolId));
    }

    public function testCanNotUpdateSchool(): void
    {
        $schoolId = 10;
        $rolesInSchool = ['foo'];
        $sessionUser = m::mock(SessionUserInterface::class);

        $sessionUser->shouldReceive('isRoot')->andReturn(false);
        $sessionUser->shouldReceive('rolesInSchool')->andReturn($rolesInSchool);
        $this->permissionMatrix
            ->shouldReceive('getPermittedRoles')
            ->withArgs([$schoolId, Capabilities::CAN_UPDATE_SCHOOLS])
            ->andReturn([]);
        $this->permissionMatrix
            ->shouldReceive('hasPermission')
            ->withArgs([$schoolId, Capabilities::CAN_UPDATE_SCHOOLS, $rolesInSchool])
            ->andReturn(false);

        $this->assertFalse($this->permissionChecker->canUpdateSchool($sessionUser, $schoolId));
    }

    public function testCanUpdateCompetency(): void
    {
        $schoolId = 10;
        $rolesInSchool = ['foo'];
        $sessionUser = m::mock(SessionUserInterface::class);

        $sessionUser->shouldReceive('isRoot')->andReturn(false);
        $sessionUser->shouldReceive('rolesInSchool')->andReturn($rolesInSchool);
        $this->permissionMatrix
            ->shouldReceive('getPermittedRoles')
            ->withArgs([$schoolId, Capabilities::CAN_UPDATE_COMPETENCIES])
            ->andReturn([]);
        $this->permissionMatrix
            ->shouldReceive('hasPermission')
            ->withArgs([$schoolId, Capabilities::CAN_UPDATE_COMPETENCIES, $rolesInSchool])
            ->andReturn(true);

        $this->assertTrue($this->permissionChecker->canUpdateCompetency($sessionUser, $schoolId));
    }

    public function testCanNotUpdateCompetency(): void
    {
        $schoolId = 10;
        $rolesInSchool = ['foo'];
        $sessionUser = m::mock(SessionUserInterface::class);

        $sessionUser->shouldReceive('isRoot')->andReturn(false);
        $sessionUser->shouldReceive('rolesInSchool')->andReturn($rolesInSchool);
        $this->permissionMatrix
            ->shouldReceive('getPermittedRoles')
            ->withArgs([$schoolId, Capabilities::CAN_UPDATE_COMPETENCIES])
            ->andReturn([]);
        $this->permissionMatrix
            ->shouldReceive('hasPermission')
            ->withArgs([$schoolId, Capabilities::CAN_UPDATE_COMPETENCIES, $rolesInSchool])
            ->andReturn(false);

        $this->assertFalse($this->permissionChecker->canUpdateCompetency($sessionUser, $schoolId));
    }

    public function testCanDeleteCompetency(): void
    {
        $schoolId = 10;
        $rolesInSchool = ['foo'];
        $sessionUser = m::mock(SessionUserInterface::class);

        $sessionUser->shouldReceive('isRoot')->andReturn(false);
        $sessionUser->shouldReceive('rolesInSchool')->andReturn($rolesInSchool);
        $this->permissionMatrix
            ->shouldReceive('getPermittedRoles')
            ->withArgs([$schoolId, Capabilities::CAN_DELETE_COMPETENCIES])
            ->andReturn([]);
        $this->permissionMatrix
            ->shouldReceive('hasPermission')
            ->withArgs([$schoolId, Capabilities::CAN_DELETE_COMPETENCIES, $rolesInSchool])
            ->andReturn(true);

        $this->assertTrue($this->permissionChecker->canDeleteCompetency($sessionUser, $schoolId));
    }

    public function testCanNotDeleteCompetency(): void
    {
        $schoolId = 10;
        $rolesInSchool = ['foo'];
        $sessionUser = m::mock(SessionUserInterface::class);

        $sessionUser->shouldReceive('isRoot')->andReturn(false);
        $sessionUser->shouldReceive('rolesInSchool')->andReturn($rolesInSchool);
        $this->permissionMatrix
            ->shouldReceive('getPermittedRoles')
            ->withArgs([$schoolId, Capabilities::CAN_DELETE_COMPETENCIES])
            ->andReturn([]);
        $this->permissionMatrix
            ->shouldReceive('hasPermission')
            ->withArgs([$schoolId, Capabilities::CAN_DELETE_COMPETENCIES, $rolesInSchool])
            ->andReturn(false);

        $this->assertFalse($this->permissionChecker->canDeleteCompetency($sessionUser, $schoolId));
    }

    public function testCanCreateCompetency(): void
    {
        $schoolId = 10;
        $rolesInSchool = ['foo'];
        $sessionUser = m::mock(SessionUserInterface::class);

        $sessionUser->shouldReceive('isRoot')->andReturn(false);
        $sessionUser->shouldReceive('rolesInSchool')->andReturn($rolesInSchool);
        $this->permissionMatrix
            ->shouldReceive('getPermittedRoles')
            ->withArgs([$schoolId, Capabilities::CAN_CREATE_COMPETENCIES])
            ->andReturn([]);
        $this->permissionMatrix
            ->shouldReceive('hasPermission')
            ->withArgs([$schoolId, Capabilities::CAN_CREATE_COMPETENCIES, $rolesInSchool])
            ->andReturn(true);

        $this->assertTrue($this->permissionChecker->canCreateCompetency($sessionUser, $schoolId));
    }

    public function testCanNotCreateCompetency(): void
    {
        $schoolId = 10;
        $rolesInSchool = ['foo'];
        $sessionUser = m::mock(SessionUserInterface::class);

        $sessionUser->shouldReceive('isRoot')->andReturn(false);
        $sessionUser->shouldReceive('rolesInSchool')->andReturn($rolesInSchool);
        $this->permissionMatrix
            ->shouldReceive('getPermittedRoles')
            ->withArgs([$schoolId, Capabilities::CAN_CREATE_COMPETENCIES])
            ->andReturn([]);
        $this->permissionMatrix
            ->shouldReceive('hasPermission')
            ->withArgs([$schoolId, Capabilities::CAN_CREATE_COMPETENCIES, $rolesInSchool])
            ->andReturn(false);

        $this->assertFalse($this->permissionChecker->canCreateCompetency($sessionUser, $schoolId));
    }

    public function testCanUpdateVocabulary(): void
    {
        $schoolId = 10;
        $rolesInSchool = ['foo'];
        $sessionUser = m::mock(SessionUserInterface::class);

        $sessionUser->shouldReceive('isRoot')->andReturn(false);
        $sessionUser->shouldReceive('rolesInSchool')->andReturn($rolesInSchool);
        $this->permissionMatrix
            ->shouldReceive('getPermittedRoles')
            ->withArgs([$schoolId, Capabilities::CAN_UPDATE_VOCABULARIES])
            ->andReturn([]);
        $this->permissionMatrix
            ->shouldReceive('hasPermission')
            ->withArgs([$schoolId, Capabilities::CAN_UPDATE_VOCABULARIES, $rolesInSchool])
            ->andReturn(true);

        $this->assertTrue($this->permissionChecker->canUpdateVocabulary($sessionUser, $schoolId));
    }

    public function testCanNotUpdateVocabulary(): void
    {
        $schoolId = 10;
        $rolesInSchool = ['foo'];
        $sessionUser = m::mock(SessionUserInterface::class);

        $sessionUser->shouldReceive('isRoot')->andReturn(false);
        $sessionUser->shouldReceive('rolesInSchool')->andReturn($rolesInSchool);
        $this->permissionMatrix
            ->shouldReceive('getPermittedRoles')
            ->withArgs([$schoolId, Capabilities::CAN_UPDATE_VOCABULARIES])
            ->andReturn([]);
        $this->permissionMatrix
            ->shouldReceive('hasPermission')
            ->withArgs([$schoolId, Capabilities::CAN_UPDATE_VOCABULARIES, $rolesInSchool])
            ->andReturn(false);

        $this->assertFalse($this->permissionChecker->canUpdateVocabulary($sessionUser, $schoolId));
    }

    public function testCanDeleteVocabulary(): void
    {
        $schoolId = 10;
        $rolesInSchool = ['foo'];
        $sessionUser = m::mock(SessionUserInterface::class);

        $sessionUser->shouldReceive('isRoot')->andReturn(false);
        $sessionUser->shouldReceive('rolesInSchool')->andReturn($rolesInSchool);
        $this->permissionMatrix
            ->shouldReceive('getPermittedRoles')
            ->withArgs([$schoolId, Capabilities::CAN_DELETE_VOCABULARIES])
            ->andReturn([]);
        $this->permissionMatrix
            ->shouldReceive('hasPermission')
            ->withArgs([$schoolId, Capabilities::CAN_DELETE_VOCABULARIES, $rolesInSchool])
            ->andReturn(true);

        $this->assertTrue($this->permissionChecker->canDeleteVocabulary($sessionUser, $schoolId));
    }

    public function testCanNotDeleteVocabulary(): void
    {
        $schoolId = 10;
        $rolesInSchool = ['foo'];
        $sessionUser = m::mock(SessionUserInterface::class);

        $sessionUser->shouldReceive('isRoot')->andReturn(false);
        $sessionUser->shouldReceive('rolesInSchool')->andReturn($rolesInSchool);
        $this->permissionMatrix
            ->shouldReceive('getPermittedRoles')
            ->withArgs([$schoolId, Capabilities::CAN_DELETE_VOCABULARIES])
            ->andReturn([]);
        $this->permissionMatrix
            ->shouldReceive('hasPermission')
            ->withArgs([$schoolId, Capabilities::CAN_DELETE_VOCABULARIES, $rolesInSchool])
            ->andReturn(false);

        $this->assertFalse($this->permissionChecker->canDeleteVocabulary($sessionUser, $schoolId));
    }

    public function testCanCreateVocabulary(): void
    {
        $schoolId = 10;
        $rolesInSchool = ['foo'];
        $sessionUser = m::mock(SessionUserInterface::class);

        $sessionUser->shouldReceive('isRoot')->andReturn(false);
        $sessionUser->shouldReceive('rolesInSchool')->andReturn($rolesInSchool);
        $this->permissionMatrix
            ->shouldReceive('getPermittedRoles')
            ->withArgs([$schoolId, Capabilities::CAN_CREATE_VOCABULARIES])
            ->andReturn([]);
        $this->permissionMatrix
            ->shouldReceive('hasPermission')
            ->withArgs([$schoolId, Capabilities::CAN_CREATE_VOCABULARIES, $rolesInSchool])
            ->andReturn(true);

        $this->assertTrue($this->permissionChecker->canCreateVocabulary($sessionUser, $schoolId));
    }

    public function testCanNotCreateVocabulary(): void
    {
        $schoolId = 10;
        $rolesInSchool = ['foo'];
        $sessionUser = m::mock(SessionUserInterface::class);

        $sessionUser->shouldReceive('isRoot')->andReturn(false);
        $sessionUser->shouldReceive('rolesInSchool')->andReturn($rolesInSchool);
        $this->permissionMatrix
            ->shouldReceive('getPermittedRoles')
            ->withArgs([$schoolId, Capabilities::CAN_CREATE_VOCABULARIES])
            ->andReturn([]);
        $this->permissionMatrix
            ->shouldReceive('hasPermission')
            ->withArgs([$schoolId, Capabilities::CAN_CREATE_VOCABULARIES, $rolesInSchool])
            ->andReturn(false);

        $this->assertFalse($this->permissionChecker->canCreateVocabulary($sessionUser, $schoolId));
    }

    public function testCanUpdateTerm(): void
    {
        $schoolId = 10;
        $rolesInSchool = ['foo'];
        $sessionUser = m::mock(SessionUserInterface::class);

        $sessionUser->shouldReceive('isRoot')->andReturn(false);
        $sessionUser->shouldReceive('rolesInSchool')->andReturn($rolesInSchool);
        $this->permissionMatrix
            ->shouldReceive('getPermittedRoles')
            ->withArgs([$schoolId, Capabilities::CAN_UPDATE_TERMS])
            ->andReturn([]);
        $this->permissionMatrix
            ->shouldReceive('hasPermission')
            ->withArgs([$schoolId, Capabilities::CAN_UPDATE_TERMS, $rolesInSchool])
            ->andReturn(true);

        $this->assertTrue($this->permissionChecker->canUpdateTerm($sessionUser, $schoolId));
    }

    public function testCanNotUpdateTerm(): void
    {
        $schoolId = 10;
        $rolesInSchool = ['foo'];
        $sessionUser = m::mock(SessionUserInterface::class);

        $sessionUser->shouldReceive('isRoot')->andReturn(false);
        $sessionUser->shouldReceive('rolesInSchool')->andReturn($rolesInSchool);
        $this->permissionMatrix
            ->shouldReceive('getPermittedRoles')
            ->withArgs([$schoolId, Capabilities::CAN_UPDATE_TERMS])
            ->andReturn([]);
        $this->permissionMatrix
            ->shouldReceive('hasPermission')
            ->withArgs([$schoolId, Capabilities::CAN_UPDATE_TERMS, $rolesInSchool])
            ->andReturn(false);

        $this->assertFalse($this->permissionChecker->canUpdateTerm($sessionUser, $schoolId));
    }

    public function testCanDeleteTerm(): void
    {
        $schoolId = 10;
        $rolesInSchool = ['foo'];
        $sessionUser = m::mock(SessionUserInterface::class);

        $sessionUser->shouldReceive('isRoot')->andReturn(false);
        $sessionUser->shouldReceive('rolesInSchool')->andReturn($rolesInSchool);
        $this->permissionMatrix
            ->shouldReceive('getPermittedRoles')
            ->withArgs([$schoolId, Capabilities::CAN_DELETE_TERMS])
            ->andReturn([]);
        $this->permissionMatrix
            ->shouldReceive('hasPermission')
            ->withArgs([$schoolId, Capabilities::CAN_DELETE_TERMS, $rolesInSchool])
            ->andReturn(true);

        $this->assertTrue($this->permissionChecker->canDeleteTerm($sessionUser, $schoolId));
    }

    public function testCanNotDeleteTerm(): void
    {
        $schoolId = 10;
        $rolesInSchool = ['foo'];
        $sessionUser = m::mock(SessionUserInterface::class);

        $sessionUser->shouldReceive('isRoot')->andReturn(false);
        $sessionUser->shouldReceive('rolesInSchool')->andReturn($rolesInSchool);
        $this->permissionMatrix
            ->shouldReceive('getPermittedRoles')
            ->withArgs([$schoolId, Capabilities::CAN_DELETE_TERMS])
            ->andReturn([]);
        $this->permissionMatrix
            ->shouldReceive('hasPermission')
            ->withArgs([$schoolId, Capabilities::CAN_DELETE_TERMS, $rolesInSchool])
            ->andReturn(false);

        $this->assertFalse($this->permissionChecker->canDeleteTerm($sessionUser, $schoolId));
    }

    public function testCanCreateTerm(): void
    {
        $schoolId = 10;
        $rolesInSchool = ['foo'];
        $sessionUser = m::mock(SessionUserInterface::class);

        $sessionUser->shouldReceive('isRoot')->andReturn(false);
        $sessionUser->shouldReceive('rolesInSchool')->andReturn($rolesInSchool);
        $this->permissionMatrix
            ->shouldReceive('getPermittedRoles')
            ->withArgs([$schoolId, Capabilities::CAN_CREATE_TERMS])
            ->andReturn([]);
        $this->permissionMatrix
            ->shouldReceive('hasPermission')
            ->withArgs([$schoolId, Capabilities::CAN_CREATE_TERMS, $rolesInSchool])
            ->andReturn(true);

        $this->assertTrue($this->permissionChecker->canCreateTerm($sessionUser, $schoolId));
    }

    public function testCanNotCreateTerm(): void
    {
        $schoolId = 10;
        $rolesInSchool = ['foo'];
        $sessionUser = m::mock(SessionUserInterface::class);

        $sessionUser->shouldReceive('isRoot')->andReturn(false);
        $sessionUser->shouldReceive('rolesInSchool')->andReturn($rolesInSchool);
        $this->permissionMatrix
            ->shouldReceive('getPermittedRoles')
            ->withArgs([$schoolId, Capabilities::CAN_CREATE_TERMS])
            ->andReturn([]);
        $this->permissionMatrix
            ->shouldReceive('hasPermission')
            ->withArgs([$schoolId, Capabilities::CAN_CREATE_TERMS, $rolesInSchool])
            ->andReturn(false);

        $this->assertFalse($this->permissionChecker->canCreateTerm($sessionUser, $schoolId));
    }

    public function testCanUpdateInstructorGroup(): void
    {
        $schoolId = 10;
        $rolesInSchool = ['foo'];
        $sessionUser = m::mock(SessionUserInterface::class);

        $sessionUser->shouldReceive('isRoot')->andReturn(false);
        $sessionUser->shouldReceive('rolesInSchool')->andReturn($rolesInSchool);
        $this->permissionMatrix
            ->shouldReceive('getPermittedRoles')
            ->withArgs([$schoolId, Capabilities::CAN_UPDATE_INSTRUCTOR_GROUPS])
            ->andReturn([]);
        $this->permissionMatrix
            ->shouldReceive('hasPermission')
            ->withArgs([$schoolId, Capabilities::CAN_UPDATE_INSTRUCTOR_GROUPS, $rolesInSchool])
            ->andReturn(true);

        $this->assertTrue($this->permissionChecker->canUpdateInstructorGroup($sessionUser, $schoolId));
    }

    public function testCanNotUpdateInstructorGroup(): void
    {
        $schoolId = 10;
        $rolesInSchool = ['foo'];
        $sessionUser = m::mock(SessionUserInterface::class);

        $sessionUser->shouldReceive('isRoot')->andReturn(false);
        $sessionUser->shouldReceive('rolesInSchool')->andReturn($rolesInSchool);
        $this->permissionMatrix
            ->shouldReceive('getPermittedRoles')
            ->withArgs([$schoolId, Capabilities::CAN_UPDATE_INSTRUCTOR_GROUPS])
            ->andReturn([]);
        $this->permissionMatrix
            ->shouldReceive('hasPermission')
            ->withArgs([$schoolId, Capabilities::CAN_UPDATE_INSTRUCTOR_GROUPS, $rolesInSchool])
            ->andReturn(false);

        $this->assertFalse($this->permissionChecker->canUpdateInstructorGroup($sessionUser, $schoolId));
    }

    public function testCanDeleteInstructorGroup(): void
    {
        $schoolId = 10;
        $rolesInSchool = ['foo'];
        $sessionUser = m::mock(SessionUserInterface::class);

        $sessionUser->shouldReceive('isRoot')->andReturn(false);
        $sessionUser->shouldReceive('rolesInSchool')->andReturn($rolesInSchool);
        $this->permissionMatrix
            ->shouldReceive('getPermittedRoles')
            ->withArgs([$schoolId, Capabilities::CAN_DELETE_INSTRUCTOR_GROUPS])
            ->andReturn([]);
        $this->permissionMatrix
            ->shouldReceive('hasPermission')
            ->withArgs([$schoolId, Capabilities::CAN_DELETE_INSTRUCTOR_GROUPS, $rolesInSchool])
            ->andReturn(true);

        $this->assertTrue($this->permissionChecker->canDeleteInstructorGroup($sessionUser, $schoolId));
    }

    public function testCanNotDeleteInstructorGroup(): void
    {
        $schoolId = 10;
        $rolesInSchool = ['foo'];
        $sessionUser = m::mock(SessionUserInterface::class);

        $sessionUser->shouldReceive('isRoot')->andReturn(false);
        $sessionUser->shouldReceive('rolesInSchool')->andReturn($rolesInSchool);
        $this->permissionMatrix
            ->shouldReceive('getPermittedRoles')
            ->withArgs([$schoolId, Capabilities::CAN_DELETE_INSTRUCTOR_GROUPS])
            ->andReturn([]);
        $this->permissionMatrix
            ->shouldReceive('hasPermission')
            ->withArgs([$schoolId, Capabilities::CAN_DELETE_INSTRUCTOR_GROUPS, $rolesInSchool])
            ->andReturn(false);

        $this->assertFalse($this->permissionChecker->canDeleteInstructorGroup($sessionUser, $schoolId));
    }

    public function testCanCreateInstructorGroup(): void
    {
        $schoolId = 10;
        $rolesInSchool = ['foo'];
        $sessionUser = m::mock(SessionUserInterface::class);

        $sessionUser->shouldReceive('isRoot')->andReturn(false);
        $sessionUser->shouldReceive('rolesInSchool')->andReturn($rolesInSchool);
        $this->permissionMatrix
            ->shouldReceive('getPermittedRoles')
            ->withArgs([$schoolId, Capabilities::CAN_CREATE_INSTRUCTOR_GROUPS])
            ->andReturn([]);
        $this->permissionMatrix
            ->shouldReceive('hasPermission')
            ->withArgs([$schoolId, Capabilities::CAN_CREATE_INSTRUCTOR_GROUPS, $rolesInSchool])
            ->andReturn(true);

        $this->assertTrue($this->permissionChecker->canCreateInstructorGroup($sessionUser, $schoolId));
    }

    public function testCanNotCreateInstructorGroup(): void
    {
        $schoolId = 10;
        $rolesInSchool = ['foo'];
        $sessionUser = m::mock(SessionUserInterface::class);

        $sessionUser->shouldReceive('isRoot')->andReturn(false);
        $sessionUser->shouldReceive('rolesInSchool')->andReturn($rolesInSchool);
        $this->permissionMatrix
            ->shouldReceive('getPermittedRoles')
            ->withArgs([$schoolId, Capabilities::CAN_CREATE_INSTRUCTOR_GROUPS])
            ->andReturn([]);
        $this->permissionMatrix
            ->shouldReceive('hasPermission')
            ->withArgs([$schoolId, Capabilities::CAN_CREATE_INSTRUCTOR_GROUPS, $rolesInSchool])
            ->andReturn(false);

        $this->assertFalse($this->permissionChecker->canCreateInstructorGroup($sessionUser, $schoolId));
    }

    public function testCanUpdateAllCurriculumInventoryReports(): void
    {
        $rolesInSchool = ['foo'];
        $schoolId = 10;
        $reportId = 20;
        $sessionUser = m::mock(SessionUserInterface::class);

        $sessionUser->shouldReceive('isRoot')->andReturn(false);
        $sessionUser->shouldReceive('rolesInSchool')->andReturn($rolesInSchool);
        $this->permissionMatrix
            ->shouldReceive('getPermittedRoles')
            ->withArgs([$schoolId, Capabilities::CAN_UPDATE_ALL_CURRICULUM_INVENTORY_REPORTS])
            ->andReturn([]);
        $this->permissionMatrix
            ->shouldReceive('hasPermission')
            ->withArgs([$schoolId, Capabilities::CAN_UPDATE_ALL_CURRICULUM_INVENTORY_REPORTS, $rolesInSchool])
            ->andReturn(true);

        $this->assertTrue(
            $this->permissionChecker->canUpdateCurriculumInventoryReport($sessionUser, $reportId, $schoolId)
        );
    }

    public function testCanUpdateTheirCurriculumInventoryReports(): void
    {
        $rolesInSchool = ['foo'];
        $rolesInReport = ['bar'];
        $schoolId = 10;
        $reportId = 20;
        $sessionUser = m::mock(SessionUserInterface::class);

        $sessionUser->shouldReceive('isRoot')->andReturn(false);
        $sessionUser->shouldReceive('rolesInSchool')->andReturn($rolesInSchool);
        $sessionUser->shouldReceive('rolesInCurriculumInventoryReport')->andReturn($rolesInReport);

        $this->permissionMatrix
            ->shouldReceive('getPermittedRoles')
            ->withArgs([$schoolId, Capabilities::CAN_UPDATE_ALL_CURRICULUM_INVENTORY_REPORTS])
            ->andReturn([]);
        $this->permissionMatrix
            ->shouldReceive('hasPermission')
            ->withArgs([$schoolId, Capabilities::CAN_UPDATE_ALL_CURRICULUM_INVENTORY_REPORTS, $rolesInSchool])
            ->andReturn(false);
        $this->permissionMatrix
            ->shouldReceive('getPermittedRoles')
            ->withArgs([$schoolId, Capabilities::CAN_UPDATE_THEIR_CURRICULUM_INVENTORY_REPORTS])
            ->andReturn([]);
        $this->permissionMatrix
            ->shouldReceive('hasPermission')
            ->withArgs([$schoolId, Capabilities::CAN_UPDATE_THEIR_CURRICULUM_INVENTORY_REPORTS, $rolesInReport])
            ->andReturn(true);

        $this->assertTrue(
            $this->permissionChecker->canUpdateCurriculumInventoryReport($sessionUser, $reportId, $schoolId)
        );
    }

    public function testCanNotUpdateCurriculumInventoryReport(): void
    {
        $rolesInSchool = ['foo'];
        $rolesInReport = ['bar'];
        $schoolId = 10;
        $reportId = 20;
        $sessionUser = m::mock(SessionUserInterface::class);

        $sessionUser->shouldReceive('isRoot')->andReturn(false);
        $sessionUser->shouldReceive('rolesInSchool')->andReturn($rolesInSchool);
        $sessionUser->shouldReceive('rolesInCurriculumInventoryReport')->andReturn($rolesInReport);

        $this->permissionMatrix
            ->shouldReceive('getPermittedRoles')
            ->withArgs([$schoolId, Capabilities::CAN_UPDATE_ALL_CURRICULUM_INVENTORY_REPORTS])
            ->andReturn([]);
        $this->permissionMatrix
            ->shouldReceive('hasPermission')
            ->withArgs([$schoolId, Capabilities::CAN_UPDATE_ALL_CURRICULUM_INVENTORY_REPORTS, $rolesInSchool])
            ->andReturn(false);
        $this->permissionMatrix
            ->shouldReceive('getPermittedRoles')
            ->withArgs([$schoolId, Capabilities::CAN_UPDATE_THEIR_CURRICULUM_INVENTORY_REPORTS])
            ->andReturn([]);
        $this->permissionMatrix
            ->shouldReceive('hasPermission')
            ->withArgs([$schoolId, Capabilities::CAN_UPDATE_THEIR_CURRICULUM_INVENTORY_REPORTS, $rolesInReport])
            ->andReturn(false);

        $this->assertFalse(
            $this->permissionChecker->canUpdateCurriculumInventoryReport($sessionUser, $reportId, $schoolId)
        );
    }

    public function testCanDeleteAllCurriculumInventoryReports(): void
    {
        $rolesInSchool = ['foo'];
        $schoolId = 10;
        $reportId = 20;
        $sessionUser = m::mock(SessionUserInterface::class);

        $sessionUser->shouldReceive('isRoot')->andReturn(false);
        $sessionUser->shouldReceive('rolesInSchool')->andReturn($rolesInSchool);
        $this->permissionMatrix
            ->shouldReceive('getPermittedRoles')
            ->withArgs([$schoolId, Capabilities::CAN_DELETE_ALL_CURRICULUM_INVENTORY_REPORTS])
            ->andReturn([]);
        $this->permissionMatrix
            ->shouldReceive('hasPermission')
            ->withArgs([$schoolId, Capabilities::CAN_DELETE_ALL_CURRICULUM_INVENTORY_REPORTS, $rolesInSchool])
            ->andReturn(true);

        $this->assertTrue(
            $this->permissionChecker->canDeleteCurriculumInventoryReport($sessionUser, $reportId, $schoolId)
        );
    }

    public function testCanDeleteTheirCurriculumInventoryReports(): void
    {
        $rolesInSchool = ['foo'];
        $rolesInReport = ['bar'];
        $schoolId = 10;
        $reportId = 20;
        $sessionUser = m::mock(SessionUserInterface::class);

        $sessionUser->shouldReceive('isRoot')->andReturn(false);
        $sessionUser->shouldReceive('rolesInSchool')->andReturn($rolesInSchool);
        $sessionUser->shouldReceive('rolesInCurriculumInventoryReport')->andReturn($rolesInReport);

        $this->permissionMatrix
            ->shouldReceive('getPermittedRoles')
            ->withArgs([$schoolId, Capabilities::CAN_DELETE_ALL_CURRICULUM_INVENTORY_REPORTS])
            ->andReturn([]);
        $this->permissionMatrix
            ->shouldReceive('hasPermission')
            ->withArgs([$schoolId, Capabilities::CAN_DELETE_ALL_CURRICULUM_INVENTORY_REPORTS, $rolesInSchool])
            ->andReturn(false);
        $this->permissionMatrix
            ->shouldReceive('getPermittedRoles')
            ->withArgs([$schoolId, Capabilities::CAN_DELETE_THEIR_CURRICULUM_INVENTORY_REPORTS])
            ->andReturn([]);
        $this->permissionMatrix
            ->shouldReceive('hasPermission')
            ->withArgs([$schoolId, Capabilities::CAN_DELETE_THEIR_CURRICULUM_INVENTORY_REPORTS, $rolesInReport])
            ->andReturn(true);

        $this->assertTrue(
            $this->permissionChecker->canDeleteCurriculumInventoryReport($sessionUser, $reportId, $schoolId)
        );
    }

    public function testCanNotDeleteCurriculumInventoryReport(): void
    {
        $rolesInSchool = ['foo'];
        $rolesInReport = ['bar'];
        $schoolId = 10;
        $reportId = 20;
        $sessionUser = m::mock(SessionUserInterface::class);

        $sessionUser->shouldReceive('isRoot')->andReturn(false);
        $sessionUser->shouldReceive('rolesInSchool')->andReturn($rolesInSchool);
        $sessionUser->shouldReceive('rolesInCurriculumInventoryReport')->andReturn($rolesInReport);

        $this->permissionMatrix
            ->shouldReceive('getPermittedRoles')
            ->withArgs([$schoolId, Capabilities::CAN_DELETE_ALL_CURRICULUM_INVENTORY_REPORTS])
            ->andReturn([]);
        $this->permissionMatrix
            ->shouldReceive('hasPermission')
            ->withArgs([$schoolId, Capabilities::CAN_DELETE_ALL_CURRICULUM_INVENTORY_REPORTS, $rolesInSchool])
            ->andReturn(false);
        $this->permissionMatrix
            ->shouldReceive('getPermittedRoles')
            ->withArgs([$schoolId, Capabilities::CAN_DELETE_THEIR_CURRICULUM_INVENTORY_REPORTS])
            ->andReturn([]);
        $this->permissionMatrix
            ->shouldReceive('hasPermission')
            ->withArgs([$schoolId, Capabilities::CAN_DELETE_THEIR_CURRICULUM_INVENTORY_REPORTS, $rolesInReport])
            ->andReturn(false);

        $this->assertFalse(
            $this->permissionChecker->canDeleteCurriculumInventoryReport($sessionUser, $reportId, $schoolId)
        );
    }

    public function testCanCreateCurriculumInventoryReport(): void
    {
        $schoolId = 10;
        $rolesInSchool = ['foo'];
        $sessionUser = m::mock(SessionUserInterface::class);

        $sessionUser->shouldReceive('isRoot')->andReturn(false);
        $sessionUser->shouldReceive('rolesInSchool')->andReturn($rolesInSchool);
        $this->permissionMatrix
            ->shouldReceive('getPermittedRoles')
            ->withArgs([$schoolId, Capabilities::CAN_CREATE_CURRICULUM_INVENTORY_REPORTS])
            ->andReturn([]);
        $this->permissionMatrix
            ->shouldReceive('hasPermission')
            ->withArgs([$schoolId, Capabilities::CAN_CREATE_CURRICULUM_INVENTORY_REPORTS, $rolesInSchool])
            ->andReturn(true);

        $this->assertTrue($this->permissionChecker->canCreateCurriculumInventoryReport($sessionUser, $schoolId));
    }

    public function testCanNotCreateCurriculumInventoryReport(): void
    {
        $schoolId = 10;
        $rolesInSchool = ['foo'];
        $sessionUser = m::mock(SessionUserInterface::class);

        $sessionUser->shouldReceive('isRoot')->andReturn(false);
        $sessionUser->shouldReceive('rolesInSchool')->andReturn($rolesInSchool);
        $this->permissionMatrix
            ->shouldReceive('getPermittedRoles')
            ->withArgs([$schoolId, Capabilities::CAN_CREATE_CURRICULUM_INVENTORY_REPORTS])
            ->andReturn([]);
        $this->permissionMatrix
            ->shouldReceive('hasPermission')
            ->withArgs([$schoolId, Capabilities::CAN_CREATE_CURRICULUM_INVENTORY_REPORTS, $rolesInSchool])
            ->andReturn(false);

        $this->assertFalse($this->permissionChecker->canCreateCurriculumInventoryReport($sessionUser, $schoolId));
    }

    public function testCanUpdateCurriculumInventoryInstitution(): void
    {
        $schoolId = 10;
        $rolesInSchool = ['foo'];
        $sessionUser = m::mock(SessionUserInterface::class);

        $sessionUser->shouldReceive('isRoot')->andReturn(false);
        $sessionUser->shouldReceive('rolesInSchool')->andReturn($rolesInSchool);
        $this->permissionMatrix
            ->shouldReceive('getPermittedRoles')
            ->withArgs([$schoolId, Capabilities::CAN_UPDATE_CURRICULUM_INVENTORY_INSTITUTIONS])
            ->andReturn([]);
        $this->permissionMatrix
            ->shouldReceive('hasPermission')
            ->withArgs([$schoolId, Capabilities::CAN_UPDATE_CURRICULUM_INVENTORY_INSTITUTIONS, $rolesInSchool])
            ->andReturn(true);

        $this->assertTrue($this->permissionChecker->canUpdateCurriculumInventoryInstitution($sessionUser, $schoolId));
    }

    public function testCanNotUpdateCurriculumInventoryInstitution(): void
    {
        $schoolId = 10;
        $rolesInSchool = ['foo'];
        $sessionUser = m::mock(SessionUserInterface::class);

        $sessionUser->shouldReceive('isRoot')->andReturn(false);
        $sessionUser->shouldReceive('rolesInSchool')->andReturn($rolesInSchool);
        $this->permissionMatrix
            ->shouldReceive('getPermittedRoles')
            ->withArgs([$schoolId, Capabilities::CAN_UPDATE_CURRICULUM_INVENTORY_INSTITUTIONS])
            ->andReturn([]);
        $this->permissionMatrix
            ->shouldReceive('hasPermission')
            ->withArgs([$schoolId, Capabilities::CAN_UPDATE_CURRICULUM_INVENTORY_INSTITUTIONS, $rolesInSchool])
            ->andReturn(false);

        $this->assertFalse($this->permissionChecker->canUpdateCurriculumInventoryInstitution($sessionUser, $schoolId));
    }

    public function testCanDeleteCurriculumInventoryInstitution(): void
    {
        $schoolId = 10;
        $rolesInSchool = ['foo'];
        $sessionUser = m::mock(SessionUserInterface::class);

        $sessionUser->shouldReceive('isRoot')->andReturn(false);
        $sessionUser->shouldReceive('rolesInSchool')->andReturn($rolesInSchool);
        $this->permissionMatrix
            ->shouldReceive('getPermittedRoles')
            ->withArgs([$schoolId, Capabilities::CAN_DELETE_CURRICULUM_INVENTORY_INSTITUTIONS])
            ->andReturn([]);
        $this->permissionMatrix
            ->shouldReceive('hasPermission')
            ->withArgs([$schoolId, Capabilities::CAN_DELETE_CURRICULUM_INVENTORY_INSTITUTIONS, $rolesInSchool])
            ->andReturn(true);

        $this->assertTrue($this->permissionChecker->canDeleteCurriculumInventoryInstitution($sessionUser, $schoolId));
    }

    public function testCanNotDeleteCurriculumInventoryInstitution(): void
    {
        $schoolId = 10;
        $rolesInSchool = ['foo'];
        $sessionUser = m::mock(SessionUserInterface::class);

        $sessionUser->shouldReceive('isRoot')->andReturn(false);
        $sessionUser->shouldReceive('rolesInSchool')->andReturn($rolesInSchool);
        $this->permissionMatrix
            ->shouldReceive('getPermittedRoles')
            ->withArgs([$schoolId, Capabilities::CAN_DELETE_CURRICULUM_INVENTORY_INSTITUTIONS])
            ->andReturn([]);
        $this->permissionMatrix
            ->shouldReceive('hasPermission')
            ->withArgs([$schoolId, Capabilities::CAN_DELETE_CURRICULUM_INVENTORY_INSTITUTIONS, $rolesInSchool])
            ->andReturn(false);

        $this->assertFalse($this->permissionChecker->canDeleteCurriculumInventoryInstitution($sessionUser, $schoolId));
    }

    public function testCanCreateCurriculumInventoryInstitution(): void
    {
        $schoolId = 10;
        $rolesInSchool = ['foo'];
        $sessionUser = m::mock(SessionUserInterface::class);

        $sessionUser->shouldReceive('isRoot')->andReturn(false);
        $sessionUser->shouldReceive('rolesInSchool')->andReturn($rolesInSchool);
        $this->permissionMatrix
            ->shouldReceive('getPermittedRoles')
            ->withArgs([$schoolId, Capabilities::CAN_CREATE_CURRICULUM_INVENTORY_INSTITUTIONS])
            ->andReturn([]);
        $this->permissionMatrix
            ->shouldReceive('hasPermission')
            ->withArgs([$schoolId, Capabilities::CAN_CREATE_CURRICULUM_INVENTORY_INSTITUTIONS, $rolesInSchool])
            ->andReturn(true);

        $this->assertTrue($this->permissionChecker->canCreateCurriculumInventoryInstitution($sessionUser, $schoolId));
    }

    public function testCanNotCreateCurriculumInventoryInstitution(): void
    {
        $schoolId = 10;
        $rolesInSchool = ['foo'];
        $sessionUser = m::mock(SessionUserInterface::class);

        $sessionUser->shouldReceive('isRoot')->andReturn(false);
        $sessionUser->shouldReceive('rolesInSchool')->andReturn($rolesInSchool);
        $this->permissionMatrix
            ->shouldReceive('getPermittedRoles')
            ->withArgs([$schoolId, Capabilities::CAN_CREATE_CURRICULUM_INVENTORY_INSTITUTIONS])
            ->andReturn([]);
        $this->permissionMatrix
            ->shouldReceive('hasPermission')
            ->withArgs([$schoolId, Capabilities::CAN_CREATE_CURRICULUM_INVENTORY_INSTITUTIONS, $rolesInSchool])
            ->andReturn(false);

        $this->assertFalse($this->permissionChecker->canCreateCurriculumInventoryInstitution($sessionUser, $schoolId));
    }

    public function testCanUpdateLearnerGroup(): void
    {
        $schoolId = 10;
        $rolesInSchool = ['foo'];
        $sessionUser = m::mock(SessionUserInterface::class);

        $sessionUser->shouldReceive('isRoot')->andReturn(false);
        $sessionUser->shouldReceive('rolesInSchool')->andReturn($rolesInSchool);
        $this->permissionMatrix
            ->shouldReceive('getPermittedRoles')
            ->withArgs([$schoolId, Capabilities::CAN_UPDATE_LEARNER_GROUPS])
            ->andReturn([]);
        $this->permissionMatrix
            ->shouldReceive('hasPermission')
            ->withArgs([$schoolId, Capabilities::CAN_UPDATE_LEARNER_GROUPS, $rolesInSchool])
            ->andReturn(true);

        $this->assertTrue($this->permissionChecker->canUpdateLearnerGroup($sessionUser, $schoolId));
    }

    public function testCanNotUpdateLearnerGroup(): void
    {
        $schoolId = 10;
        $rolesInSchool = ['foo'];
        $sessionUser = m::mock(SessionUserInterface::class);

        $sessionUser->shouldReceive('isRoot')->andReturn(false);
        $sessionUser->shouldReceive('rolesInSchool')->andReturn($rolesInSchool);
        $this->permissionMatrix
            ->shouldReceive('getPermittedRoles')
            ->withArgs([$schoolId, Capabilities::CAN_UPDATE_LEARNER_GROUPS])
            ->andReturn([]);
        $this->permissionMatrix
            ->shouldReceive('hasPermission')
            ->withArgs([$schoolId, Capabilities::CAN_UPDATE_LEARNER_GROUPS, $rolesInSchool])
            ->andReturn(false);

        $this->assertFalse($this->permissionChecker->canUpdateLearnerGroup($sessionUser, $schoolId));
    }

    public function testCanDeleteLearnerGroup(): void
    {
        $schoolId = 10;
        $rolesInSchool = ['foo'];
        $sessionUser = m::mock(SessionUserInterface::class);

        $sessionUser->shouldReceive('isRoot')->andReturn(false);
        $sessionUser->shouldReceive('rolesInSchool')->andReturn($rolesInSchool);
        $this->permissionMatrix
            ->shouldReceive('getPermittedRoles')
            ->withArgs([$schoolId, Capabilities::CAN_DELETE_LEARNER_GROUPS])
            ->andReturn([]);
        $this->permissionMatrix
            ->shouldReceive('hasPermission')
            ->withArgs([$schoolId, Capabilities::CAN_DELETE_LEARNER_GROUPS, $rolesInSchool])
            ->andReturn(true);

        $this->assertTrue($this->permissionChecker->canDeleteLearnerGroup($sessionUser, $schoolId));
    }

    public function testCanNotDeleteLearnerGroup(): void
    {
        $schoolId = 10;
        $rolesInSchool = ['foo'];
        $sessionUser = m::mock(SessionUserInterface::class);

        $sessionUser->shouldReceive('isRoot')->andReturn(false);
        $sessionUser->shouldReceive('rolesInSchool')->andReturn($rolesInSchool);
        $this->permissionMatrix
            ->shouldReceive('getPermittedRoles')
            ->withArgs([$schoolId, Capabilities::CAN_DELETE_LEARNER_GROUPS])
            ->andReturn([]);
        $this->permissionMatrix
            ->shouldReceive('hasPermission')
            ->withArgs([$schoolId, Capabilities::CAN_DELETE_LEARNER_GROUPS, $rolesInSchool])
            ->andReturn(false);

        $this->assertFalse($this->permissionChecker->canDeleteLearnerGroup($sessionUser, $schoolId));
    }

    public function testCanCreateLearnerGroup(): void
    {
        $schoolId = 10;
        $rolesInSchool = ['foo'];
        $sessionUser = m::mock(SessionUserInterface::class);

        $sessionUser->shouldReceive('isRoot')->andReturn(false);
        $sessionUser->shouldReceive('rolesInSchool')->andReturn($rolesInSchool);
        $this->permissionMatrix
            ->shouldReceive('getPermittedRoles')
            ->withArgs([$schoolId, Capabilities::CAN_CREATE_LEARNER_GROUPS])
            ->andReturn([]);
        $this->permissionMatrix
            ->shouldReceive('hasPermission')
            ->withArgs([$schoolId, Capabilities::CAN_CREATE_LEARNER_GROUPS, $rolesInSchool])
            ->andReturn(true);

        $this->assertTrue($this->permissionChecker->canCreateLearnerGroup($sessionUser, $schoolId));
    }

    public function testCanNotCreateLearnerGroup(): void
    {
        $schoolId = 10;
        $rolesInSchool = ['foo'];
        $sessionUser = m::mock(SessionUserInterface::class);

        $sessionUser->shouldReceive('isRoot')->andReturn(false);
        $sessionUser->shouldReceive('rolesInSchool')->andReturn($rolesInSchool);
        $this->permissionMatrix
            ->shouldReceive('getPermittedRoles')
            ->withArgs([$schoolId, Capabilities::CAN_CREATE_LEARNER_GROUPS])
            ->andReturn([]);
        $this->permissionMatrix
            ->shouldReceive('hasPermission')
            ->withArgs([$schoolId, Capabilities::CAN_CREATE_LEARNER_GROUPS, $rolesInSchool])
            ->andReturn(false);

        $this->assertFalse($this->permissionChecker->canCreateLearnerGroup($sessionUser, $schoolId));
    }

    public function testCanUpdateUser(): void
    {
        $schoolId = 10;
        $rolesInSchool = ['foo'];
        $sessionUser = m::mock(SessionUserInterface::class);

        $sessionUser->shouldReceive('isRoot')->andReturn(false);
        $sessionUser->shouldReceive('rolesInSchool')->andReturn($rolesInSchool);
        $this->permissionMatrix
            ->shouldReceive('getPermittedRoles')
            ->withArgs([$schoolId, Capabilities::CAN_UPDATE_USERS])
            ->andReturn([]);
        $this->permissionMatrix
            ->shouldReceive('hasPermission')
            ->withArgs([$schoolId, Capabilities::CAN_UPDATE_USERS, $rolesInSchool])
            ->andReturn(true);

        $this->assertTrue($this->permissionChecker->canUpdateUser($sessionUser, $schoolId));
    }

    public function testCanNotUpdateUser(): void
    {
        $schoolId = 10;
        $rolesInSchool = ['foo'];
        $sessionUser = m::mock(SessionUserInterface::class);

        $sessionUser->shouldReceive('isRoot')->andReturn(false);
        $sessionUser->shouldReceive('rolesInSchool')->andReturn($rolesInSchool);
        $this->permissionMatrix
            ->shouldReceive('getPermittedRoles')
            ->withArgs([$schoolId, Capabilities::CAN_UPDATE_USERS])
            ->andReturn([]);
        $this->permissionMatrix
            ->shouldReceive('hasPermission')
            ->withArgs([$schoolId, Capabilities::CAN_UPDATE_USERS, $rolesInSchool])
            ->andReturn(false);

        $this->assertFalse($this->permissionChecker->canUpdateUser($sessionUser, $schoolId));
    }

    public function testCanDeleteUser(): void
    {
        $schoolId = 10;
        $rolesInSchool = ['foo'];
        $sessionUser = m::mock(SessionUserInterface::class);

        $sessionUser->shouldReceive('isRoot')->andReturn(false);
        $sessionUser->shouldReceive('rolesInSchool')->andReturn($rolesInSchool);
        $this->permissionMatrix
            ->shouldReceive('getPermittedRoles')
            ->withArgs([$schoolId, Capabilities::CAN_DELETE_USERS])
            ->andReturn([]);
        $this->permissionMatrix
            ->shouldReceive('hasPermission')
            ->withArgs([$schoolId, Capabilities::CAN_DELETE_USERS, $rolesInSchool])
            ->andReturn(true);

        $this->assertTrue($this->permissionChecker->canDeleteUser($sessionUser, $schoolId));
    }

    public function testCanNotDeleteUser(): void
    {
        $schoolId = 10;
        $rolesInSchool = ['foo'];
        $sessionUser = m::mock(SessionUserInterface::class);

        $sessionUser->shouldReceive('isRoot')->andReturn(false);
        $sessionUser->shouldReceive('rolesInSchool')->andReturn($rolesInSchool);
        $this->permissionMatrix
            ->shouldReceive('getPermittedRoles')
            ->withArgs([$schoolId, Capabilities::CAN_DELETE_USERS])
            ->andReturn([]);
        $this->permissionMatrix
            ->shouldReceive('hasPermission')
            ->withArgs([$schoolId, Capabilities::CAN_DELETE_USERS, $rolesInSchool])
            ->andReturn(false);

        $this->assertFalse($this->permissionChecker->canDeleteUser($sessionUser, $schoolId));
    }

    public function testCanCreateUser(): void
    {
        $schoolId = 10;
        $rolesInSchool = ['foo'];
        $sessionUser = m::mock(SessionUserInterface::class);

        $sessionUser->shouldReceive('isRoot')->andReturn(false);
        $sessionUser->shouldReceive('rolesInSchool')->andReturn($rolesInSchool);
        $this->permissionMatrix
            ->shouldReceive('getPermittedRoles')
            ->withArgs([$schoolId, Capabilities::CAN_CREATE_USERS])
            ->andReturn([]);
        $this->permissionMatrix
            ->shouldReceive('hasPermission')
            ->withArgs([$schoolId, Capabilities::CAN_CREATE_USERS, $rolesInSchool])
            ->andReturn(true);

        $this->assertTrue($this->permissionChecker->canCreateUser($sessionUser, $schoolId));
    }

    public function testCanNotCreateUser(): void
    {
        $schoolId = 10;
        $rolesInSchool = ['foo'];
        $sessionUser = m::mock(SessionUserInterface::class);

        $sessionUser->shouldReceive('isRoot')->andReturn(false);
        $sessionUser->shouldReceive('rolesInSchool')->andReturn($rolesInSchool);
        $this->permissionMatrix
            ->shouldReceive('getPermittedRoles')
            ->withArgs([$schoolId, Capabilities::CAN_CREATE_USERS])
            ->andReturn([]);
        $this->permissionMatrix
            ->shouldReceive('hasPermission')
            ->withArgs([$schoolId, Capabilities::CAN_CREATE_USERS, $rolesInSchool])
            ->andReturn(false);

        $this->assertFalse($this->permissionChecker->canCreateUser($sessionUser, $schoolId));
    }

    public function testCanViewLearnerGroupIfUseIsInLearnerGroup(): void
    {
        $learnerGroupId = 10;
        $sessionUser = m::mock(SessionUserInterface::class);

        $sessionUser->shouldReceive('isRoot')->andReturn(false);
        $sessionUser
            ->shouldReceive('isInLearnerGroup')
            ->withArgs([$learnerGroupId])
            ->andReturn(true);
        $this->assertTrue($this->permissionChecker->canViewLearnerGroup($sessionUser, $learnerGroupId));
    }

    public function testCanViewLearnerGroupIfUserPerformsNonLearnerFunction(): void
    {
        $learnerGroupId = 10;
        $sessionUser = m::mock(SessionUserInterface::class);

        $sessionUser->shouldReceive('isRoot')->andReturn(false);
        $sessionUser
            ->shouldReceive('isInLearnerGroup')
            ->withArgs([$learnerGroupId])
            ->andReturn(false);
        $sessionUser
            ->shouldReceive('performsNonLearnerFunction')
            ->andReturn(true);
        $this->assertTrue($this->permissionChecker->canViewLearnerGroup($sessionUser, $learnerGroupId));
    }

    public function testCanNotViewLearnerGroup(): void
    {
        $learnerGroupId = 10;
        $sessionUser = m::mock(SessionUserInterface::class);

        $sessionUser->shouldReceive('isRoot')->andReturn(false);
        $sessionUser
            ->shouldReceive('isInLearnerGroup')
            ->withArgs([$learnerGroupId])
            ->andReturn(false);
        $sessionUser
            ->shouldReceive('performsNonLearnerFunction')
            ->andReturn(false);
        $this->assertFalse($this->permissionChecker->canViewLearnerGroup($sessionUser, $learnerGroupId));
    }

    public function testCanCreateUsersInAnySchool(): void
    {
        $this->markTestIncomplete('This test has not been implemented yet.');
    }

    public function testCanCreateOrUpdateUsersInAnySchool(): void
    {
        $this->markTestIncomplete('This test has not been implemented yet.');
    }

    public function testCanCreateCurriculumInventoryReportUserInAnySchool(): void
    {
        $this->markTestIncomplete('This test has not been implemented yet.');
    }
}
