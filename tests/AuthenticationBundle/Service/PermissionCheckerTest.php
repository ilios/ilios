<?php

namespace Tests\AuthenticationBundle\Service;

use Ilios\AuthenticationBundle\Classes\Capabilities;
use Ilios\AuthenticationBundle\Classes\PermissionMatrixInterface;
use Ilios\AuthenticationBundle\Classes\SessionUserInterface;
use Ilios\AuthenticationBundle\Service\PermissionChecker;
use Ilios\CoreBundle\Entity\CohortInterface;
use Ilios\CoreBundle\Entity\CourseInterface;
use Ilios\CoreBundle\Entity\ProgramInterface;
use Ilios\CoreBundle\Entity\ProgramYearInterface;
use Ilios\CoreBundle\Entity\SchoolInterface;
use Ilios\CoreBundle\Entity\SessionInterface;
use Mockery as m;
use Symfony\Bundle\FrameworkBundle\Tests\TestCase;

/**
 * Class PermissionCheckerTest
 * @package Tests\AuthenticationBundle\Service
 */
class PermissionCheckerTest extends TestCase
{
    use m\Adapter\Phpunit\MockeryPHPUnitIntegration;

    /**
     * @var PermissionChecker
     */
    protected $permissionChecker;

    /**
     * @var m\MockInterface
     */
    protected $permissionMatrix;

    /**
     * @inheritdoc
     */
    public function setUp()
    {
        $this->permissionMatrix = m::mock(PermissionMatrixInterface::class);
        $this->permissionChecker = new PermissionChecker($this->permissionMatrix);
    }

    /**
     * @inheritdoc
     */
    public function tearDown()
    {
        unset($this->permissionChecker);
        unset($this->permissionMatrix);
    }

    /**
     * @covers PermissionChecker::canUpdateCourse()
     */
    public function testCanUpdateAllCourses()
    {
        $roles = ['foo'];
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
        $sessionUser->shouldReceive('rolesInSchool')->andReturn($roles);
        $this->permissionMatrix
            ->shouldReceive('hasPermission')
            ->withArgs([$schoolId, Capabilities::CAN_UPDATE_ALL_COURSES, $roles])
            ->andReturn(true);

        $this->assertTrue($this->permissionChecker->canUpdateCourse($sessionUser, $course));
    }

    /**
     * @covers PermissionChecker::canUpdateCourse()
     */
    public function testCanUpdateTheirCourses()
    {
        $schoolRoles = ['foo'];
        $courseRoles = ['bar'];
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
        $sessionUser->shouldReceive('rolesInSchool')->andReturn($schoolRoles);
        $sessionUser->shouldReceive('rolesInCourse')->andReturn($courseRoles);

        $this->permissionMatrix
            ->shouldReceive('hasPermission')
            ->withArgs([$schoolId, Capabilities::CAN_UPDATE_ALL_COURSES, $schoolRoles])
            ->andReturn(false);

        $this->permissionMatrix
            ->shouldReceive('hasPermission')
            ->withArgs([$schoolId, Capabilities::CAN_UPDATE_THEIR_COURSES, $courseRoles])
            ->andReturn(true);

        $this->assertTrue($this->permissionChecker->canUpdateCourse($sessionUser, $course));
    }

    /**
     * @covers PermissionChecker::canUpdateCourse()
     */
    public function testCanNotUpdateCourses()
    {
        $schoolRoles = ['foo'];
        $courseRoles = ['bar'];
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
        $sessionUser->shouldReceive('rolesInSchool')->andReturn($schoolRoles);
        $sessionUser->shouldReceive('rolesInCourse')->andReturn($courseRoles);

        $this->permissionMatrix
            ->shouldReceive('hasPermission')
            ->withArgs([$schoolId, Capabilities::CAN_UPDATE_ALL_COURSES, $schoolRoles])
            ->andReturn(false);

        $this->permissionMatrix
            ->shouldReceive('hasPermission')
            ->withArgs([$schoolId, Capabilities::CAN_UPDATE_THEIR_COURSES, $courseRoles])
            ->andReturn(false);

        $this->assertFalse($this->permissionChecker->canUpdateCourse($sessionUser, $course));
    }

    /**
     * @covers PermissionChecker::canUpdateCourse()
     */
    public function testCanNotUpdateLockedCourses()
    {
        $course = m::mock(CourseInterface::class);
        $sessionUser = m::mock(SessionUserInterface::class);

        $course->shouldReceive('isArchived')->andReturn(false);
        $course->shouldReceive('isLocked')->andReturn(true);

        $this->assertFalse($this->permissionChecker->canUpdateCourse($sessionUser, $course));
    }

    /**
     * @covers PermissionChecker::canUpdateCourse()
     */
    public function testCanNotUpdateArchivedCourses()
    {
        $course = m::mock(CourseInterface::class);
        $sessionUser = m::mock(SessionUserInterface::class);

        $course->shouldReceive('isArchived')->andReturn(true);
        $course->shouldReceive('isLocked')->andReturn(false);

        $this->assertFalse($this->permissionChecker->canUpdateCourse($sessionUser, $course));
    }

    /**
     * @covers PermissionChecker::canDeleteCourse()
     */
    public function testCanDeleteAllCourses()
    {
        $roles = ['foo'];
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
        $sessionUser->shouldReceive('rolesInSchool')->andReturn($roles);
        $this->permissionMatrix
            ->shouldReceive('hasPermission')
            ->withArgs([$schoolId, Capabilities::CAN_DELETE_ALL_COURSES, $roles])
            ->andReturn(true);

        $this->assertTrue($this->permissionChecker->canDeleteCourse($sessionUser, $course));
    }

    /**
     * @covers PermissionChecker::canDeleteCourse()
     */
    public function testCanDeleteTheirCourses()
    {
        $schoolRoles = ['foo'];
        $courseRoles = ['bar'];
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
        $sessionUser->shouldReceive('rolesInSchool')->andReturn($schoolRoles);
        $sessionUser->shouldReceive('rolesInCourse')->andReturn($courseRoles);

        $this->permissionMatrix
            ->shouldReceive('hasPermission')
            ->withArgs([$schoolId, Capabilities::CAN_DELETE_ALL_COURSES, $schoolRoles])
            ->andReturn(false);

        $this->permissionMatrix
            ->shouldReceive('hasPermission')
            ->withArgs([$schoolId, Capabilities::CAN_DELETE_THEIR_COURSES, $courseRoles])
            ->andReturn(true);

        $this->assertTrue($this->permissionChecker->canDeleteCourse($sessionUser, $course));
    }

    /**
     * @covers PermissionChecker::canDeleteCourse()
     */
    public function testCanNotDeleteCourses()
    {
        $schoolRoles = ['foo'];
        $courseRoles = ['bar'];
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
        $sessionUser->shouldReceive('rolesInSchool')->andReturn($schoolRoles);
        $sessionUser->shouldReceive('rolesInCourse')->andReturn($courseRoles);

        $this->permissionMatrix
            ->shouldReceive('hasPermission')
            ->withArgs([$schoolId, Capabilities::CAN_DELETE_ALL_COURSES, $schoolRoles])
            ->andReturn(false);

        $this->permissionMatrix
            ->shouldReceive('hasPermission')
            ->withArgs([$schoolId, Capabilities::CAN_DELETE_THEIR_COURSES, $courseRoles])
            ->andReturn(false);

        $this->assertFalse($this->permissionChecker->canDeleteCourse($sessionUser, $course));
    }

    /**
     * @covers PermissionChecker::canDeleteCourse()
     */
    public function testCanNotDeleteLockedCourses()
    {
        $course = m::mock(CourseInterface::class);
        $sessionUser = m::mock(SessionUserInterface::class);

        $course->shouldReceive('isArchived')->andReturn(false);
        $course->shouldReceive('isLocked')->andReturn(true);

        $this->assertFalse($this->permissionChecker->canDeleteCourse($sessionUser, $course));
    }

    /**
     * @covers PermissionChecker::canDeleteCourse()
     */
    public function testCanNotDeleteArchivedCourses()
    {
        $course = m::mock(CourseInterface::class);
        $sessionUser = m::mock(SessionUserInterface::class);

        $course->shouldReceive('isArchived')->andReturn(true);
        $course->shouldReceive('isLocked')->andReturn(false);

        $this->assertFalse($this->permissionChecker->canDeleteCourse($sessionUser, $course));
    }

    /**
     * @covers PermissionChecker::canCreateCourse()
     */
    public function testCanCreateCourse()
    {
        $schoolId = 10;
        $roles = ['foo'];
        $school = m::mock(SchoolInterface::class);
        $sessionUser = m::mock(SessionUserInterface::class);

        $school->shouldReceive('getId')->andReturn($schoolId);
        $sessionUser->shouldReceive('rolesInSchool')->andReturn($roles);
        $this->permissionMatrix
            ->shouldReceive('hasPermission')
            ->withArgs([$schoolId, Capabilities::CAN_CREATE_COURSES, $roles])
            ->andReturn(true);

        $this->assertTrue($this->permissionChecker->canCreateCourse($sessionUser, $school));
    }

    /**
     * @covers PermissionChecker::canCreateCourse()
     */
    public function testCanNotCreateCourse()
    {
        $schoolId = 10;
        $roles = ['foo'];
        $school = m::mock(SchoolInterface::class);
        $sessionUser = m::mock(SessionUserInterface::class);

        $school->shouldReceive('getId')->andReturn($schoolId);
        $sessionUser->shouldReceive('rolesInSchool')->andReturn($roles);
        $this->permissionMatrix
            ->shouldReceive('hasPermission')
            ->withArgs([$schoolId, Capabilities::CAN_CREATE_COURSES, $roles])
            ->andReturn(false);

        $this->assertFalse($this->permissionChecker->canCreateCourse($sessionUser, $school));
    }

    /**
     * @covers PermissionChecker::canUnlockCourse()
     */
    public function testCanUnlockAllCourses()
    {
        $roles = ['foo'];
        $courseId = 20;
        $schoolId = 10;
        $school = m::mock(SchoolInterface::class);
        $course = m::mock(CourseInterface::class);
        $sessionUser = m::mock(SessionUserInterface::class);

        $school->shouldReceive('getId')->andReturn($schoolId);
        $course->shouldReceive('getSchool')->andReturn($school);
        $course->shouldReceive('getId')->andReturn($courseId);
        $sessionUser->shouldReceive('rolesInSchool')->andReturn($roles);
        $this->permissionMatrix
            ->shouldReceive('hasPermission')
            ->withArgs([$schoolId, Capabilities::CAN_UNLOCK_ALL_COURSES, $roles])
            ->andReturn(true);

        $this->assertTrue($this->permissionChecker->canUnlockCourse($sessionUser, $course));
    }

    /**
     * @covers PermissionChecker::canUnlockCourse()
     */
    public function testCanUnlockTheirCourses()
    {
        $schoolRoles = ['foo'];
        $courseRoles = ['bar'];
        $courseId = 20;
        $schoolId = 10;
        $school = m::mock(SchoolInterface::class);
        $course = m::mock(CourseInterface::class);
        $sessionUser = m::mock(SessionUserInterface::class);

        $school->shouldReceive('getId')->andReturn($schoolId);
        $course->shouldReceive('getSchool')->andReturn($school);
        $course->shouldReceive('getId')->andReturn($courseId);
        $sessionUser->shouldReceive('rolesInSchool')->andReturn($schoolRoles);
        $sessionUser->shouldReceive('rolesInCourse')->andReturn($courseRoles);

        $this->permissionMatrix
            ->shouldReceive('hasPermission')
            ->withArgs([$schoolId, Capabilities::CAN_UNLOCK_ALL_COURSES, $schoolRoles])
            ->andReturn(false);

        $this->permissionMatrix
            ->shouldReceive('hasPermission')
            ->withArgs([$schoolId, Capabilities::CAN_UNLOCK_THEIR_COURSES, $courseRoles])
            ->andReturn(true);

        $this->assertTrue($this->permissionChecker->canUnlockCourse($sessionUser, $course));
    }

    /**
     * @covers PermissionChecker::canUnlockCourse()
     */
    public function testCanNotUnlockCourses()
    {
        $schoolRoles = ['foo'];
        $courseRoles = ['bar'];
        $courseId = 20;
        $schoolId = 10;
        $school = m::mock(SchoolInterface::class);
        $course = m::mock(CourseInterface::class);
        $sessionUser = m::mock(SessionUserInterface::class);

        $school->shouldReceive('getId')->andReturn($schoolId);
        $course->shouldReceive('getSchool')->andReturn($school);
        $course->shouldReceive('getId')->andReturn($courseId);
        $sessionUser->shouldReceive('rolesInSchool')->andReturn($schoolRoles);
        $sessionUser->shouldReceive('rolesInCourse')->andReturn($courseRoles);

        $this->permissionMatrix
            ->shouldReceive('hasPermission')
            ->withArgs([$schoolId, Capabilities::CAN_UNLOCK_ALL_COURSES, $schoolRoles])
            ->andReturn(false);
        $this->permissionMatrix
            ->shouldReceive('hasPermission')
            ->withArgs([$schoolId, Capabilities::CAN_UNLOCK_THEIR_COURSES, $courseRoles])
            ->andReturn(false);

        $this->assertFalse($this->permissionChecker->canUnlockCourse($sessionUser, $course));
    }

    /**
     * @covers PermissionChecker::canUnarchiveCourse()
     */
    public function testCanUnarchiveAllCourses()
    {
        $roles = ['foo'];
        $courseId = 20;
        $schoolId = 10;
        $school = m::mock(SchoolInterface::class);
        $course = m::mock(CourseInterface::class);
        $sessionUser = m::mock(SessionUserInterface::class);

        $school->shouldReceive('getId')->andReturn($schoolId);
        $course->shouldReceive('getSchool')->andReturn($school);
        $course->shouldReceive('getId')->andReturn($courseId);
        $sessionUser->shouldReceive('rolesInSchool')->andReturn($roles);

        $this->permissionMatrix
            ->shouldReceive('hasPermission')
            ->withArgs([$schoolId, Capabilities::CAN_UNARCHIVE_ALL_COURSES, $roles])
            ->andReturn(true);

        $this->assertTrue($this->permissionChecker->canUnarchiveCourse($sessionUser, $course));
    }

    /**
     * @covers PermissionChecker::canUnarchiveCourse()
     */
    public function testCanUnarchiveTheirCourses()
    {
        $schoolRoles = ['foo'];
        $courseRoles = ['bar'];
        $courseId = 20;
        $schoolId = 10;
        $school = m::mock(SchoolInterface::class);
        $course = m::mock(CourseInterface::class);
        $sessionUser = m::mock(SessionUserInterface::class);

        $school->shouldReceive('getId')->andReturn($schoolId);
        $course->shouldReceive('getSchool')->andReturn($school);
        $course->shouldReceive('getId')->andReturn($courseId);
        $sessionUser->shouldReceive('rolesInSchool')->andReturn($schoolRoles);
        $sessionUser->shouldReceive('rolesInCourse')->andReturn($courseRoles);

        $this->permissionMatrix
            ->shouldReceive('hasPermission')
            ->withArgs([$schoolId, Capabilities::CAN_UNARCHIVE_ALL_COURSES, $schoolRoles])
            ->andReturn(false);
        $this->permissionMatrix
            ->shouldReceive('hasPermission')
            ->withArgs([$schoolId, Capabilities::CAN_UNARCHIVE_THEIR_COURSES, $courseRoles])
            ->andReturn(true);

        $this->assertTrue($this->permissionChecker->canUnarchiveCourse($sessionUser, $course));
    }

    /**
     * @covers PermissionChecker::canUnarchiveCourse()
     */
    public function testCanNotUnarchiveCourses()
    {
        $schoolRoles = ['foo'];
        $courseRoles = ['bar'];
        $courseId = 20;
        $schoolId = 10;
        $school = m::mock(SchoolInterface::class);
        $course = m::mock(CourseInterface::class);
        $sessionUser = m::mock(SessionUserInterface::class);

        $school->shouldReceive('getId')->andReturn($schoolId);
        $course->shouldReceive('getSchool')->andReturn($school);
        $course->shouldReceive('getId')->andReturn($courseId);
        $sessionUser->shouldReceive('rolesInSchool')->andReturn($schoolRoles);
        $sessionUser->shouldReceive('rolesInCourse')->andReturn($courseRoles);

        $this->permissionMatrix
            ->shouldReceive('hasPermission')
            ->withArgs([$schoolId, Capabilities::CAN_UNARCHIVE_ALL_COURSES, $schoolRoles])
            ->andReturn(false);
        $this->permissionMatrix
            ->shouldReceive('hasPermission')
            ->withArgs([$schoolId, Capabilities::CAN_UNARCHIVE_THEIR_COURSES, $courseRoles])
            ->andReturn(false);

        $this->assertFalse($this->permissionChecker->canUnarchiveCourse($sessionUser, $course));
    }

    /**
     * @covers PermissionChecker::canUpdateSession()
     */
    public function testCanUpdateAllSessions()
    {
        $roles = ['foo'];
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
        $sessionUser->shouldReceive('rolesInSchool')->andReturn($roles);

        $this->permissionMatrix
            ->shouldReceive('hasPermission')
            ->withArgs([$schoolId, Capabilities::CAN_UPDATE_ALL_SESSIONS, $roles])
            ->andReturn(true);

        $this->assertTrue($this->permissionChecker->canUpdateSession($sessionUser, $session));
    }

    /**
     * @covers PermissionChecker::canUpdateSession()
     */
    public function testCanUpdateTheirSessions()
    {
        $schoolRoles = ['foo'];
        $sessionRoles = ['bar'];
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
        $sessionUser->shouldReceive('rolesInSchool')->andReturn($schoolRoles);
        $sessionUser->shouldReceive('rolesInSession')->andReturn($sessionRoles);

        $this->permissionMatrix
            ->shouldReceive('hasPermission')
            ->withArgs([$schoolId, Capabilities::CAN_UPDATE_ALL_SESSIONS, $schoolRoles])
            ->andReturn(false);
        $this->permissionMatrix
            ->shouldReceive('hasPermission')
            ->withArgs([$schoolId, Capabilities::CAN_UPDATE_THEIR_SESSIONS, $sessionRoles])
            ->andReturn(true);

        $this->assertTrue($this->permissionChecker->canUpdateSession($sessionUser, $session));
    }

    /**
     * @covers PermissionChecker::canUpdateSession()
     */
    public function testCanUpdateSessionsIfUserCanUpdateCourse()
    {
        $schoolRoles = ['foo'];
        $sessionRoles = ['bar'];
        $courseRoles = ['baz'];
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
        $sessionUser->shouldReceive('rolesInSchool')->andReturn($schoolRoles);
        $sessionUser->shouldReceive('rolesInSession')->andReturn($sessionRoles);
        $sessionUser->shouldReceive('rolesInCourse')->andReturn($courseRoles);

        $this->permissionMatrix
            ->shouldReceive('hasPermission')
            ->withArgs([$schoolId, Capabilities::CAN_UPDATE_ALL_SESSIONS, $schoolRoles])
            ->andReturn(false);
        $this->permissionMatrix
            ->shouldReceive('hasPermission')
            ->withArgs([$schoolId, Capabilities::CAN_UPDATE_THEIR_SESSIONS, $sessionRoles])
            ->andReturn(false);
        $this->permissionMatrix
            ->shouldReceive('hasPermission')
            ->withArgs([$schoolId, Capabilities::CAN_UPDATE_ALL_COURSES, $schoolRoles])
            ->andReturn(true);

        $this->assertTrue($this->permissionChecker->canUpdateSession($sessionUser, $session));
    }

    /**
     * @covers PermissionChecker::canUpdateSession()
     */
    public function testCanNotUpdateSessions()
    {
        $schoolRoles = ['foo'];
        $sessionRoles = ['bar'];
        $courseRoles = ['baz'];
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
        $sessionUser->shouldReceive('rolesInSchool')->andReturn($schoolRoles);
        $sessionUser->shouldReceive('rolesInSession')->andReturn($sessionRoles);
        $sessionUser->shouldReceive('rolesInCourse')->andReturn($courseRoles);

        $this->permissionMatrix
            ->shouldReceive('hasPermission')
            ->withArgs([$schoolId, Capabilities::CAN_UPDATE_ALL_SESSIONS, $schoolRoles])
            ->andReturn(false);
        $this->permissionMatrix
            ->shouldReceive('hasPermission')
            ->withArgs([$schoolId, Capabilities::CAN_UPDATE_THEIR_SESSIONS, $sessionRoles])
            ->andReturn(false);
        $this->permissionMatrix
            ->shouldReceive('hasPermission')
            ->withArgs([$schoolId, Capabilities::CAN_UPDATE_ALL_COURSES, $schoolRoles])
            ->andReturn(false);
        $this->permissionMatrix
            ->shouldReceive('hasPermission')
            ->withArgs([$schoolId, Capabilities::CAN_UPDATE_THEIR_COURSES, $courseRoles])
            ->andReturn(false);

        $this->assertFalse($this->permissionChecker->canUpdateSession($sessionUser, $session));
    }

    /**
     * @covers PermissionChecker::canUpdateSession()
     */
    public function testCanNotUpdateSessionsInLockedCourse()
    {
        $session = m::mock(SessionInterface::class);
        $course = m::mock(CourseInterface::class);
        $sessionUser = m::mock(SessionUserInterface::class);

        $session->shouldReceive('getCourse')->andReturn($course);
        $course->shouldReceive('isLocked')->andReturn(true);
        $course->shouldReceive('isArchived')->andReturn(true);

        $this->assertFalse($this->permissionChecker->canUpdateSession($sessionUser, $session));
    }

    /**
     * @covers PermissionChecker::canUpdateSession()
     */
    public function testCanNotUpdateSessionsInArchivedCourse()
    {
        $session = m::mock(SessionInterface::class);
        $course = m::mock(CourseInterface::class);
        $sessionUser = m::mock(SessionUserInterface::class);

        $session->shouldReceive('getCourse')->andReturn($course);
        $course->shouldReceive('isLocked')->andReturn(false);
        $course->shouldReceive('isArchived')->andReturn(true);

        $this->assertFalse($this->permissionChecker->canUpdateSession($sessionUser, $session));
    }

    /**
     * @covers PermissionChecker::canDeleteSession()
     */
    public function testCanDeleteAllSessions()
    {
        $roles = ['foo'];
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
        $sessionUser->shouldReceive('rolesInSchool')->andReturn($roles);

        $this->permissionMatrix
            ->shouldReceive('hasPermission')
            ->withArgs([$schoolId, Capabilities::CAN_DELETE_ALL_SESSIONS, $roles])
            ->andReturn(true);

        $this->assertTrue($this->permissionChecker->canDeleteSession($sessionUser, $session));
    }

    /**
     * @covers PermissionChecker::canDeleteSession()
     */
    public function testCanDeleteTheirSessions()
    {
        $schoolRoles = ['foo'];
        $sessionRoles = ['bar'];
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
        $sessionUser->shouldReceive('rolesInSchool')->andReturn($schoolRoles);
        $sessionUser->shouldReceive('rolesInSession')->andReturn($sessionRoles);

        $this->permissionMatrix
            ->shouldReceive('hasPermission')
            ->withArgs([$schoolId, Capabilities::CAN_DELETE_ALL_SESSIONS, $schoolRoles])
            ->andReturn(false);
        $this->permissionMatrix
            ->shouldReceive('hasPermission')
            ->withArgs([$schoolId, Capabilities::CAN_DELETE_THEIR_SESSIONS, $sessionRoles])
            ->andReturn(true);

        $this->assertTrue($this->permissionChecker->canDeleteSession($sessionUser, $session));
    }

    /**
     * @covers PermissionChecker::canDeleteSession()
     */
    public function testCanDeleteSessionsIfUserCanUpdateCourse()
    {
        $schoolRoles = ['foo'];
        $sessionRoles = ['bar'];
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
        $sessionUser->shouldReceive('rolesInSchool')->andReturn($schoolRoles);
        $sessionUser->shouldReceive('rolesInSession')->andReturn($sessionRoles);

        $this->permissionMatrix
            ->shouldReceive('hasPermission')
            ->withArgs([$schoolId, Capabilities::CAN_DELETE_ALL_SESSIONS, $schoolRoles])
            ->andReturn(false);
        $this->permissionMatrix
            ->shouldReceive('hasPermission')
            ->withArgs([$schoolId, Capabilities::CAN_DELETE_THEIR_SESSIONS, $sessionRoles])
            ->andReturn(false);
        $this->permissionMatrix
            ->shouldReceive('hasPermission')
            ->withArgs([$schoolId, Capabilities::CAN_UPDATE_ALL_COURSES, $schoolRoles])
            ->andReturn(true);

        $this->assertTrue($this->permissionChecker->canDeleteSession($sessionUser, $session));
    }

    /**
     * @covers PermissionChecker::canDeleteSession()
     */
    public function testCanNotDeleteSessions()
    {
        $schoolRoles = ['foo'];
        $sessionRoles = ['bar'];
        $courseRoles = ['baz'];
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
        $sessionUser->shouldReceive('rolesInSchool')->andReturn($schoolRoles);
        $sessionUser->shouldReceive('rolesInSession')->andReturn($sessionRoles);
        $sessionUser->shouldReceive('rolesInCourse')->andReturn($courseRoles);

        $this->permissionMatrix
            ->shouldReceive('hasPermission')
            ->withArgs([$schoolId, Capabilities::CAN_DELETE_ALL_SESSIONS, $schoolRoles])
            ->andReturn(false);
        $this->permissionMatrix
            ->shouldReceive('hasPermission')
            ->withArgs([$schoolId, Capabilities::CAN_DELETE_THEIR_SESSIONS, $sessionRoles])
            ->andReturn(false);
        $this->permissionMatrix
            ->shouldReceive('hasPermission')
            ->withArgs([$schoolId, Capabilities::CAN_UPDATE_ALL_COURSES, $schoolRoles])
            ->andReturn(false);
        $this->permissionMatrix
            ->shouldReceive('hasPermission')
            ->withArgs([$schoolId, Capabilities::CAN_UPDATE_THEIR_COURSES, $courseRoles])
            ->andReturn(false);

        $this->assertFalse($this->permissionChecker->canDeleteSession($sessionUser, $session));
    }

    /**
     * @covers PermissionChecker::canDeleteSession()
     */
    public function testCanNotDeleteSessionsInLockedCourse()
    {
        $session = m::mock(SessionInterface::class);
        $course = m::mock(CourseInterface::class);
        $sessionUser = m::mock(SessionUserInterface::class);

        $session->shouldReceive('getCourse')->andReturn($course);
        $course->shouldReceive('isLocked')->andReturn(true);
        $course->shouldReceive('isArchived')->andReturn(false);

        $this->assertFalse($this->permissionChecker->canDeleteSession($sessionUser, $session));
    }

    /**
     * @covers PermissionChecker::canDeleteSession()
     */
    public function testCanNotDeleteSessionsInArchivedCourse()
    {
        $session = m::mock(SessionInterface::class);
        $course = m::mock(CourseInterface::class);
        $sessionUser = m::mock(SessionUserInterface::class);

        $session->shouldReceive('getCourse')->andReturn($course);
        $course->shouldReceive('isLocked')->andReturn(false);
        $course->shouldReceive('isArchived')->andReturn(true);

        $this->assertFalse($this->permissionChecker->canDeleteSession($sessionUser, $session));
    }

    /**
     * @covers PermissionChecker::canCreateSession()
     */
    public function testCanCreateSession()
    {
        $roles = ['foo'];
        $schoolId = 10;
        $course = m::mock(CourseInterface::class);
        $school = m::mock(SchoolInterface::class);
        $sessionUser = m::mock(SessionUserInterface::class);

        $course->shouldReceive('getSchool')->andReturn($school);
        $course->shouldReceive('isLocked')->andReturn(false);
        $course->shouldReceive('isArchived')->andReturn(false);
        $school->shouldReceive('getId')->andReturn($schoolId);
        $sessionUser->shouldReceive('rolesInSchool')->andReturn($roles);

        $this->permissionMatrix
            ->shouldReceive('hasPermission')
            ->withArgs([$schoolId, Capabilities::CAN_CREATE_SESSIONS, $roles])
            ->andReturn(true);

        $this->assertTrue($this->permissionChecker->canCreateSession($sessionUser, $course));
    }

    /**
     * @covers PermissionChecker::canCreateSession()
     */
    public function testCanCreateSessionIfUserCanUpdateCourse()
    {
        $roles = ['foo'];
        $schoolId = 10;
        $course = m::mock(CourseInterface::class);
        $school = m::mock(SchoolInterface::class);
        $sessionUser = m::mock(SessionUserInterface::class);

        $course->shouldReceive('getSchool')->andReturn($school);
        $course->shouldReceive('isLocked')->andReturn(false);
        $course->shouldReceive('isArchived')->andReturn(false);
        $school->shouldReceive('getId')->andReturn($schoolId);
        $sessionUser->shouldReceive('rolesInSchool')->andReturn($roles);

        $this->permissionMatrix
            ->shouldReceive('hasPermission')
            ->withArgs([$schoolId, Capabilities::CAN_CREATE_SESSIONS, $roles])
            ->andReturn(false);
        $this->permissionMatrix
            ->shouldReceive('hasPermission')
            ->withArgs([$schoolId, Capabilities::CAN_UPDATE_ALL_COURSES, $roles])
            ->andReturn(true);

        $this->assertTrue($this->permissionChecker->canCreateSession($sessionUser, $course));
    }

    /**
     * @covers PermissionChecker::canCreateSession()
     */
    public function testCanNotCreateSession()
    {
        $schoolRoles = ['foo'];
        $courseRoles = ['bar'];
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
        $sessionUser->shouldReceive('rolesInSchool')->andReturn($schoolRoles);
        $sessionUser->shouldReceive('rolesInCourse')->andReturn($courseRoles);

        $this->permissionMatrix
            ->shouldReceive('hasPermission')
            ->withArgs([$schoolId, Capabilities::CAN_CREATE_SESSIONS, $schoolRoles])
            ->andReturn(false);
        $this->permissionMatrix
            ->shouldReceive('hasPermission')
            ->withArgs([$schoolId, Capabilities::CAN_UPDATE_ALL_COURSES, $schoolRoles])
            ->andReturn(false);
        $this->permissionMatrix
            ->shouldReceive('hasPermission')
            ->withArgs([$schoolId, Capabilities::CAN_UPDATE_THEIR_COURSES, $courseRoles])
            ->andReturn(false);

        $this->assertFalse($this->permissionChecker->canCreateSession($sessionUser, $course));
    }

    /**
     * @covers PermissionChecker::canCreateSession()
     */
    public function testCanNotCreateSessionInLockedCourse()
    {
        $course = m::mock(CourseInterface::class);
        $sessionUser = m::mock(SessionUserInterface::class);

        $course->shouldReceive('isLocked')->andReturn(true);
        $course->shouldReceive('isArchived')->andReturn(false);

        $this->assertFalse($this->permissionChecker->canCreateSession($sessionUser, $course));
    }

    /**
     * @covers PermissionChecker::canCreateSession()
     */
    public function testCanNotCreateSessionInArchivedCourse()
    {
        $course = m::mock(CourseInterface::class);
        $sessionUser = m::mock(SessionUserInterface::class);

        $course->shouldReceive('isLocked')->andReturn(false);
        $course->shouldReceive('isArchived')->andReturn(true);

        $this->assertFalse($this->permissionChecker->canCreateSession($sessionUser, $course));
    }

    public function testCanUpdateSessionType()
    {
        $this->markTestIncomplete('This test has not been implemented yet.');
    }

    public function testCanDeleteSessionType()
    {
        $this->markTestIncomplete('This test has not been implemented yet.');
    }

    public function testCanCreateSessionType()
    {
        $this->markTestIncomplete('This test has not been implemented yet.');
    }

    public function testCanUpdateDepartment()
    {
        $this->markTestIncomplete('This test has not been implemented yet.');
    }

    public function testCanDeleteDepartment()
    {
        $this->markTestIncomplete('This test has not been implemented yet.');
    }

    public function testCanCreateDepartment()
    {
        $this->markTestIncomplete('This test has not been implemented yet.');
    }

    public function testCanUpdateProgram()
    {
        $this->markTestIncomplete('This test has not been implemented yet.');
    }

    public function testCanDeleteProgram()
    {
        $this->markTestIncomplete('This test has not been implemented yet.');
    }

    public function testCanCreateProgram()
    {
        $this->markTestIncomplete('This test has not been implemented yet.');
    }

    /**
    * @covers PermissionChecker::canUpdateProgramYear()
    */
    public function testCanUpdateAllProgramYears()
    {
        $roles = ['foo'];
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
        $sessionUser->shouldReceive('rolesInSchool')->andReturn($roles);

        $this->permissionMatrix
            ->shouldReceive('hasPermission')
            ->withArgs([$schoolId, Capabilities::CAN_UPDATE_ALL_PROGRAM_YEARS, $roles])
            ->andReturn(true);

        $this->assertTrue($this->permissionChecker->canUpdateProgramYear($sessionUser, $programYear));
    }

    /**
     * @covers PermissionChecker::canUpdateProgramYear()
     */
    public function testCanUpdateTheirProgramYears()
    {
        $schoolRoles = ['foo'];
        $programYearRoles = ['bar'];
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
        $sessionUser->shouldReceive('rolesInSchool')->andReturn($schoolRoles);
        $sessionUser->shouldReceive('rolesInProgramYear')->andReturn($programYearRoles);

        $this->permissionMatrix
            ->shouldReceive('hasPermission')
            ->withArgs([$schoolId, Capabilities::CAN_UPDATE_ALL_PROGRAM_YEARS, $schoolRoles])
            ->andReturn(false);
        $this->permissionMatrix
            ->shouldReceive('hasPermission')
            ->withArgs([$schoolId, Capabilities::CAN_UPDATE_THEIR_PROGRAM_YEARS, $programYearRoles])
            ->andReturn(true);

        $this->assertTrue($this->permissionChecker->canUpdateProgramYear($sessionUser, $programYear));
    }

    /**
     * @covers PermissionChecker::canUpdateProgramYear()
     */
    public function testCanUpdateProgramYearsIfUserCanUpdateProgram()
    {
        $schoolRoles = ['foo'];
        $programYearRoles = ['bar'];
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
        $sessionUser->shouldReceive('rolesInSchool')->andReturn($schoolRoles);
        $sessionUser->shouldReceive('rolesInProgramYear')->andReturn($programYearRoles);

        $this->permissionMatrix
            ->shouldReceive('hasPermission')
            ->withArgs([$schoolId, Capabilities::CAN_UPDATE_ALL_PROGRAM_YEARS, $schoolRoles])
            ->andReturn(false);
        $this->permissionMatrix
            ->shouldReceive('hasPermission')
            ->withArgs([$schoolId, Capabilities::CAN_UPDATE_THEIR_PROGRAM_YEARS, $programYearRoles])
            ->andReturn(false);
        $this->permissionMatrix
            ->shouldReceive('hasPermission')
            ->withArgs([$schoolId, Capabilities::CAN_UPDATE_ALL_PROGRAMS, $schoolRoles])
            ->andReturn(true);

        $this->assertTrue($this->permissionChecker->canUpdateProgramYear($sessionUser, $programYear));
    }

    /**
     * @covers PermissionChecker::canUpdateProgramYear()
     */
    public function testCanNotUpdateProgramYears()
    {
        $schoolRoles = ['foo'];
        $programYearRoles = ['bar'];
        $programRoles = ['baz'];
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
        $sessionUser->shouldReceive('rolesInSchool')->andReturn($schoolRoles);
        $sessionUser->shouldReceive('rolesInProgram')->andReturn($programRoles);
        $sessionUser->shouldReceive('rolesInProgramYear')->andReturn($programYearRoles);

        $this->permissionMatrix
            ->shouldReceive('hasPermission')
            ->withArgs([$schoolId, Capabilities::CAN_UPDATE_ALL_PROGRAM_YEARS, $schoolRoles])
            ->andReturn(false);
        $this->permissionMatrix
            ->shouldReceive('hasPermission')
            ->withArgs([$schoolId, Capabilities::CAN_UPDATE_THEIR_PROGRAM_YEARS, $programYearRoles])
            ->andReturn(false);
        $this->permissionMatrix
            ->shouldReceive('hasPermission')
            ->withArgs([$schoolId, Capabilities::CAN_UPDATE_ALL_PROGRAMS, $schoolRoles])
            ->andReturn(false);
        $this->permissionMatrix
            ->shouldReceive('hasPermission')
            ->withArgs([$schoolId, Capabilities::CAN_UPDATE_THEIR_PROGRAMS, $programRoles])
            ->andReturn(false);

        $this->assertFalse($this->permissionChecker->canUpdateProgramYear($sessionUser, $programYear));
    }

    /**
     * @covers PermissionChecker::canUpdateProgramYear()
     */
    public function testCanNotUpdateLockedProgramYears()
    {
        $programYear = m::mock(ProgramYearInterface::class);
        $sessionUser = m::mock(SessionUserInterface::class);

        $programYear->shouldReceive('isArchived')->andReturn(false);
        $programYear->shouldReceive('isLocked')->andReturn(true);

        $this->assertFalse($this->permissionChecker->canUpdateProgramYear($sessionUser, $programYear));
    }

    /**
     * @covers PermissionChecker::canUpdateProgramYear()
     */
    public function testCanNotUpdateArchivedProgramYears()
    {
        $programYear = m::mock(ProgramYearInterface::class);
        $sessionUser = m::mock(SessionUserInterface::class);

        $programYear->shouldReceive('isArchived')->andReturn(true);
        $programYear->shouldReceive('isLocked')->andReturn(false);

        $this->assertFalse($this->permissionChecker->canUpdateProgramYear($sessionUser, $programYear));
    }

    /**
     * @covers PermissionChecker::canDeleteProgramYear()
     */
    public function testCanDeleteAllProgramYears()
    {
        $roles = ['foo'];
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
        $sessionUser->shouldReceive('rolesInSchool')->andReturn($roles);

        $this->permissionMatrix
            ->shouldReceive('hasPermission')
            ->withArgs([$schoolId, Capabilities::CAN_DELETE_ALL_PROGRAM_YEARS, $roles])
            ->andReturn(true);

        $this->assertTrue($this->permissionChecker->canDeleteProgramYear($sessionUser, $programYear));
    }

    /**
     * @covers PermissionChecker::canDeleteProgramYear()
     */
    public function testCanDeleteTheirProgramYears()
    {
        $schoolRoles = ['foo'];
        $programYearRoles = ['bar'];
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
        $sessionUser->shouldReceive('rolesInSchool')->andReturn($schoolRoles);
        $sessionUser->shouldReceive('rolesInProgramYear')->andReturn($programYearRoles);

        $this->permissionMatrix
            ->shouldReceive('hasPermission')
            ->withArgs([$schoolId, Capabilities::CAN_DELETE_ALL_PROGRAM_YEARS, $schoolRoles])
            ->andReturn(false);
        $this->permissionMatrix
            ->shouldReceive('hasPermission')
            ->withArgs([$schoolId, Capabilities::CAN_DELETE_THEIR_PROGRAM_YEARS, $programYearRoles])
            ->andReturn(true);

        $this->assertTrue($this->permissionChecker->canDeleteProgramYear($sessionUser, $programYear));
    }

    /**
     * @covers PermissionChecker::canDeleteProgramYear()
     */
    public function testCanDeleteProgramYearsIfUserCanUpdateProgram()
    {
        $schoolRoles = ['foo'];
        $programYearRoles = ['bar'];
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
        $sessionUser->shouldReceive('rolesInSchool')->andReturn($schoolRoles);
        $sessionUser->shouldReceive('rolesInProgramYear')->andReturn($programYearRoles);

        $this->permissionMatrix
            ->shouldReceive('hasPermission')
            ->withArgs([$schoolId, Capabilities::CAN_DELETE_ALL_PROGRAM_YEARS, $schoolRoles])
            ->andReturn(false);
        $this->permissionMatrix
            ->shouldReceive('hasPermission')
            ->withArgs([$schoolId, Capabilities::CAN_DELETE_THEIR_PROGRAM_YEARS, $programYearRoles])
            ->andReturn(false);
        $this->permissionMatrix
            ->shouldReceive('hasPermission')
            ->withArgs([$schoolId, Capabilities::CAN_UPDATE_ALL_PROGRAMS, $schoolRoles])
            ->andReturn(true);


        $this->assertTrue($this->permissionChecker->canDeleteProgramYear($sessionUser, $programYear));
    }

    /**
     * @covers PermissionChecker::canDeleteProgramYear()
     */
    public function testCanNotDeleteProgramYears()
    {
        $schoolRoles = ['foo'];
        $programYearRoles = ['bar'];
        $programRoles = ['baz'];
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
        $sessionUser->shouldReceive('rolesInSchool')->andReturn($schoolRoles);
        $sessionUser->shouldReceive('rolesInProgramYear')->andReturn($programYearRoles);
        $sessionUser->shouldReceive('rolesInProgram')->andReturn($programRoles);

        $this->permissionMatrix
            ->shouldReceive('hasPermission')
            ->withArgs([$schoolId, Capabilities::CAN_DELETE_ALL_PROGRAM_YEARS, $schoolRoles])
            ->andReturn(false);
        $this->permissionMatrix
            ->shouldReceive('hasPermission')
            ->withArgs([$schoolId, Capabilities::CAN_DELETE_THEIR_PROGRAM_YEARS, $programYearRoles])
            ->andReturn(false);
        $this->permissionMatrix
            ->shouldReceive('hasPermission')
            ->withArgs([$schoolId, Capabilities::CAN_UPDATE_ALL_PROGRAMS, $schoolRoles])
            ->andReturn(false);
        $this->permissionMatrix
            ->shouldReceive('hasPermission')
            ->withArgs([$schoolId, Capabilities::CAN_UPDATE_THEIR_PROGRAMS, $programRoles])
            ->andReturn(false);

        $this->assertFalse($this->permissionChecker->canDeleteProgramYear($sessionUser, $programYear));
    }

    /**
     * @covers PermissionChecker::canDeleteProgramYear()
     */
    public function testCanNotDeleteLockedProgramYears()
    {
        $programYear = m::mock(ProgramYearInterface::class);
        $sessionUser = m::mock(SessionUserInterface::class);

        $programYear->shouldReceive('isArchived')->andReturn(false);
        $programYear->shouldReceive('isLocked')->andReturn(true);

        $this->assertFalse($this->permissionChecker->canDeleteProgramYear($sessionUser, $programYear));
    }

    /**
     * @covers PermissionChecker::canDeleteProgramYear()
     */
    public function testCanNotDeleteArchivedProgramYears()
    {
        $programYear = m::mock(ProgramYearInterface::class);
        $sessionUser = m::mock(SessionUserInterface::class);

        $programYear->shouldReceive('isArchived')->andReturn(true);
        $programYear->shouldReceive('isLocked')->andReturn(false);

        $this->assertFalse($this->permissionChecker->canDeleteProgramYear($sessionUser, $programYear));
    }

    /**
     * @covers PermissionChecker::canCreateProgramYear()
     */
    public function testCanCreateProgramYear()
    {
        $programId = 20;
        $schoolId = 10;
        $roles = ['foo'];
        $program = m::mock(ProgramInterface::class);
        $school = m::mock(SchoolInterface::class);
        $sessionUser = m::mock(SessionUserInterface::class);

        $school->shouldReceive(('getId'))->andReturn($schoolId);
        $program->shouldReceive('getId')->andReturn($programId);
        $program->shouldReceive('getSchool')->andReturn($school);
        $sessionUser->shouldReceive('rolesInSchool')->andReturn($roles);

        $this->permissionMatrix
            ->shouldReceive('hasPermission')
            ->withArgs([$schoolId, Capabilities::CAN_CREATE_PROGRAM_YEARS, $roles])
            ->andReturn(true);

        $this->assertTrue($this->permissionChecker->canCreateProgramYear($sessionUser, $program));
    }

    /**
     * @covers PermissionChecker::canCreateProgramYear()
     */
    public function testCanCreateProgramYearIfUserCanUpdateProgram()
    {
        $programId = 20;
        $schoolId = 10;
        $roles = ['foo'];
        $program = m::mock(ProgramInterface::class);
        $school = m::mock(SchoolInterface::class);
        $sessionUser = m::mock(SessionUserInterface::class);

        $school->shouldReceive(('getId'))->andReturn($schoolId);
        $program->shouldReceive('getId')->andReturn($programId);
        $program->shouldReceive('getSchool')->andReturn($school);
        $sessionUser->shouldReceive('rolesInSchool')->andReturn($roles);

        $this->permissionMatrix
            ->shouldReceive('hasPermission')
            ->withArgs([$schoolId, Capabilities::CAN_CREATE_PROGRAM_YEARS, $roles])
            ->andReturn(false);
        $this->permissionMatrix
            ->shouldReceive('hasPermission')
            ->withArgs([$schoolId, Capabilities::CAN_UPDATE_ALL_PROGRAMS, $roles])
            ->andReturn(true);

        $this->assertTrue($this->permissionChecker->canCreateProgramYear($sessionUser, $program));
    }

    /**
     * @covers PermissionChecker::canCreateProgramYear()
     */
    public function testCanNotCreateProgramYear()
    {
        $programId = 20;
        $schoolId = 10;
        $roles = ['foo'];
        $program = m::mock(ProgramInterface::class);
        $school = m::mock(SchoolInterface::class);
        $sessionUser = m::mock(SessionUserInterface::class);

        $school->shouldReceive(('getId'))->andReturn($schoolId);
        $program->shouldReceive('getId')->andReturn($programId);
        $program->shouldReceive('getSchool')->andReturn($school);
        $sessionUser->shouldReceive('rolesInSchool')->andReturn($roles);

        $this->permissionMatrix
            ->shouldReceive('hasPermission')
            ->withArgs([$schoolId, Capabilities::CAN_CREATE_PROGRAM_YEARS, $roles])
            ->andReturn(true);
        $this->permissionMatrix
            ->shouldReceive('hasPermission')
            ->withArgs([$schoolId, Capabilities::CAN_UPDATE_ALL_PROGRAMS, $roles])
            ->andReturn(false);
        $this->permissionMatrix
            ->shouldReceive('hasPermission')
            ->withArgs([$schoolId, Capabilities::CAN_UPDATE_THEIR_PROGRAMS, $roles])
            ->andReturn(false);

        $this->assertTrue($this->permissionChecker->canCreateProgramYear($sessionUser, $program));
    }

    /**
     * @covers PermissionChecker::canUpdateCohort()
     */
    public function testCanUpdateAllCohorts()
    {
        $roles = ['foo'];
        $cohortId = 10;
        $schoolId = 20;
        $school = m::mock(SchoolInterface::class);
        $cohort = m::mock(CohortInterface::class);
        $programYear = m::mock(ProgramYearInterface::class);
        $sessionUser = m::mock(SessionUserInterface::class);

        $school->shouldReceive('getId')->andReturn($schoolId);
        $cohort->shouldReceive('getProgramYear')->andReturn($programYear);
        $cohort->shouldReceive('getSchool')->andReturn($school);
        $cohort->shouldReceive('getId')->andReturn($cohortId);
        $programYear->shouldReceive('isArchived')->andReturn(false);
        $programYear->shouldReceive('isLocked')->andReturn(false);
        $sessionUser->shouldReceive('rolesInSchool')->andReturn($roles);

        $this->permissionMatrix
            ->shouldReceive('hasPermission')
            ->withArgs([$schoolId, Capabilities::CAN_UPDATE_ALL_COHORTS, $roles])
            ->andReturn(true);

        $this->assertTrue($this->permissionChecker->canUpdateCohort($sessionUser, $cohort));
    }

    /**
     * @covers PermissionChecker::canUpdateCohort()
     */
    public function testCanUpdateTheirCohorts()
    {
        $schoolRoles = ['foo'];
        $cohortRoles = ['bar'];
        $cohortId = 10;
        $schoolId = 20;
        $school = m::mock(SchoolInterface::class);
        $cohort = m::mock(CohortInterface::class);
        $programYear = m::mock(ProgramYearInterface::class);
        $sessionUser = m::mock(SessionUserInterface::class);

        $school->shouldReceive('getId')->andReturn($schoolId);
        $cohort->shouldReceive('getProgramYear')->andReturn($programYear);
        $cohort->shouldReceive('getSchool')->andReturn($school);
        $cohort->shouldReceive('getId')->andReturn($cohortId);
        $programYear->shouldReceive('isArchived')->andReturn(false);
        $programYear->shouldReceive('isLocked')->andReturn(false);
        $sessionUser->shouldReceive('rolesInSchool')->andReturn($schoolRoles);
        $sessionUser->shouldReceive('rolesInCohort')->andReturn($cohortRoles);

        $this->permissionMatrix
            ->shouldReceive('hasPermission')
            ->withArgs([$schoolId, Capabilities::CAN_UPDATE_ALL_COHORTS, $schoolRoles])
            ->andReturn(false);
        $this->permissionMatrix
            ->shouldReceive('hasPermission')
            ->withArgs([$schoolId, Capabilities::CAN_UPDATE_THEIR_COHORTS, $cohortRoles])
            ->andReturn(true);

        $this->assertTrue($this->permissionChecker->canUpdateCohort($sessionUser, $cohort));
    }

    /**
     * @covers PermissionChecker::canUpdateCohort()
     */
    public function testCanNotUpdateCohorts()
    {
        $schoolRoles = ['foo'];
        $cohortRoles = ['bar'];
        $programRoles = ['baz'];
        $cohortId = 10;
        $programId = 15;
        $schoolId = 20;
        $school = m::mock(SchoolInterface::class);
        $cohort = m::mock(CohortInterface::class);
        $programYear = m::mock(ProgramYearInterface::class);
        $program = m::mock(ProgramInterface::class);
        $sessionUser = m::mock(SessionUserInterface::class);

        $school->shouldReceive('getId')->andReturn($schoolId);
        $cohort->shouldReceive('getProgramYear')->andReturn($programYear);
        $cohort->shouldReceive('getProgram')->andReturn($program);
        $cohort->shouldReceive('getSchool')->andReturn($school);
        $cohort->shouldReceive('getId')->andReturn($cohortId);
        $programYear->shouldReceive('isArchived')->andReturn(false);
        $programYear->shouldReceive('isLocked')->andReturn(false);
        $program->shouldReceive('getId')->andReturn($programId);
        $sessionUser->shouldReceive('rolesInSchool')->andReturn($schoolRoles);
        $sessionUser->shouldReceive('rolesInCohort')->andReturn($cohortRoles);
        $sessionUser->shouldReceive('rolesInProgram')->andReturn($programRoles);

        $this->permissionMatrix
            ->shouldReceive('hasPermission')
            ->withArgs([$schoolId, Capabilities::CAN_UPDATE_ALL_COHORTS, $schoolRoles])
            ->andReturn(false);
        $this->permissionMatrix
            ->shouldReceive('hasPermission')
            ->withArgs([$schoolId, Capabilities::CAN_UPDATE_THEIR_COHORTS, $cohortRoles])
            ->andReturn(false);
        $this->permissionMatrix
            ->shouldReceive('hasPermission')
            ->withArgs([$schoolId, Capabilities::CAN_UPDATE_ALL_PROGRAMS, $schoolRoles])
            ->andReturn(false);
        $this->permissionMatrix
            ->shouldReceive('hasPermission')
            ->withArgs([$schoolId, Capabilities::CAN_UPDATE_THEIR_PROGRAMS, $programRoles])
            ->andReturn(false);

        $this->assertFalse($this->permissionChecker->canUpdateCohort($sessionUser, $cohort));
    }

    /**
     * @covers PermissionChecker::canUpdateCohort()
     */
    public function testCanNotUpdateCohortsInLockedProgramYear()
    {
        $cohort = m::mock(CohortInterface::class);
        $programYear = m::mock(ProgramYearInterface::class);
        $sessionUser = m::mock(SessionUserInterface::class);

        $cohort->shouldReceive('getProgramYear')->andReturn($programYear);
        $programYear->shouldReceive('isArchived')->andReturn(false);
        $programYear->shouldReceive('isLocked')->andReturn(true);

        $this->assertFalse($this->permissionChecker->canUpdateCohort($sessionUser, $cohort));
    }

    /**
     * @covers PermissionChecker::canUpdateCohort()
     */
    public function testCanNotUpdateCohortsIfUserCanUpdateProgram()
    {
        $schoolRoles = ['foo'];
        $cohortRoles = ['bar'];
        $cohortId = 10;
        $programId = 15;
        $schoolId = 20;
        $school = m::mock(SchoolInterface::class);
        $cohort = m::mock(CohortInterface::class);
        $programYear = m::mock(ProgramYearInterface::class);
        $program = m::mock(ProgramInterface::class);
        $sessionUser = m::mock(SessionUserInterface::class);

        $school->shouldReceive('getId')->andReturn($schoolId);
        $cohort->shouldReceive('getProgramYear')->andReturn($programYear);
        $cohort->shouldReceive('getProgram')->andReturn($program);
        $cohort->shouldReceive('getSchool')->andReturn($school);
        $cohort->shouldReceive('getId')->andReturn($cohortId);
        $programYear->shouldReceive('isArchived')->andReturn(false);
        $programYear->shouldReceive('isLocked')->andReturn(false);
        $program->shouldReceive('getId')->andReturn($programId);
        $sessionUser->shouldReceive('rolesInSchool')->andReturn($schoolRoles);
        $sessionUser->shouldReceive('rolesInCohort')->andReturn($cohortRoles);

        $this->permissionMatrix
            ->shouldReceive('hasPermission')
            ->withArgs([$schoolId, Capabilities::CAN_UPDATE_ALL_COHORTS, $schoolRoles])
            ->andReturn(false);
        $this->permissionMatrix
            ->shouldReceive('hasPermission')
            ->withArgs([$schoolId, Capabilities::CAN_UPDATE_THEIR_COHORTS, $cohortRoles])
            ->andReturn(false);
        $this->permissionMatrix
            ->shouldReceive('hasPermission')
            ->withArgs([$schoolId, Capabilities::CAN_UPDATE_ALL_PROGRAMS, $schoolRoles])
            ->andReturn(true);

        $this->assertTrue($this->permissionChecker->canUpdateCohort($sessionUser, $cohort));
    }

    /**
     * @covers PermissionChecker::canUpdateCohort()
     */
    public function testCanNotUpdateCohortsInArchivedProgramYear()
    {
        $cohort = m::mock(CohortInterface::class);
        $programYear = m::mock(ProgramYearInterface::class);
        $sessionUser = m::mock(SessionUserInterface::class);

        $cohort->shouldReceive('getProgramYear')->andReturn($programYear);
        $programYear->shouldReceive('isArchived')->andReturn(true);
        $programYear->shouldReceive('isLocked')->andReturn(false);

        $this->assertFalse($this->permissionChecker->canUpdateCohort($sessionUser, $cohort));
    }

    /**
     * @covers PermissionChecker::canDeleteCohort()
     */
    public function testCanDeleteAllCohorts()
    {
        $roles = ['foo'];
        $cohortId = 10;
        $schoolId = 20;
        $school = m::mock(SchoolInterface::class);
        $cohort = m::mock(CohortInterface::class);
        $programYear = m::mock(ProgramYearInterface::class);
        $sessionUser = m::mock(SessionUserInterface::class);

        $school->shouldReceive('getId')->andReturn($schoolId);
        $cohort->shouldReceive('getProgramYear')->andReturn($programYear);
        $cohort->shouldReceive('getSchool')->andReturn($school);
        $cohort->shouldReceive('getId')->andReturn($cohortId);
        $programYear->shouldReceive('isArchived')->andReturn(false);
        $programYear->shouldReceive('isLocked')->andReturn(false);
        $sessionUser->shouldReceive('rolesInSchool')->andReturn($roles);

        $this->permissionMatrix
            ->shouldReceive('hasPermission')
            ->withArgs([$schoolId, Capabilities::CAN_DELETE_ALL_COHORTS, $roles])
            ->andReturn(true);

        $this->assertTrue($this->permissionChecker->canDeleteCohort($sessionUser, $cohort));
    }

    /**
     * @covers PermissionChecker::canDeleteCohort()
     */
    public function testCanDeleteTheirCohorts()
    {
        $schoolRoles = ['foo'];
        $cohortRoles = ['bar'];
        $cohortId = 10;
        $schoolId = 20;
        $school = m::mock(SchoolInterface::class);
        $cohort = m::mock(CohortInterface::class);
        $programYear = m::mock(ProgramYearInterface::class);
        $sessionUser = m::mock(SessionUserInterface::class);

        $school->shouldReceive('getId')->andReturn($schoolId);
        $cohort->shouldReceive('getProgramYear')->andReturn($programYear);
        $cohort->shouldReceive('getSchool')->andReturn($school);
        $cohort->shouldReceive('getId')->andReturn($cohortId);
        $programYear->shouldReceive('isArchived')->andReturn(false);
        $programYear->shouldReceive('isLocked')->andReturn(false);
        $sessionUser->shouldReceive('rolesInSchool')->andReturn($schoolRoles);
        $sessionUser->shouldReceive('rolesInCohort')->andReturn($cohortRoles);

        $this->permissionMatrix
            ->shouldReceive('hasPermission')
            ->withArgs([$schoolId, Capabilities::CAN_DELETE_ALL_COHORTS, $schoolRoles])
            ->andReturn(false);
        $this->permissionMatrix
            ->shouldReceive('hasPermission')
            ->withArgs([$schoolId, Capabilities::CAN_DELETE_THEIR_COHORTS, $cohortRoles])
            ->andReturn(true);

        $this->assertTrue($this->permissionChecker->canDeleteCohort($sessionUser, $cohort));
    }

    /**
     * @covers PermissionChecker::canDeleteCohort()
     */
    public function testCanDeleteCohortsIfUserCanUpdateProgram()
    {
        $schoolRoles = ['foo'];
        $cohortRoles = ['bar'];
        $cohortId = 10;
        $programId = 15;
        $schoolId = 20;
        $school = m::mock(SchoolInterface::class);
        $cohort = m::mock(CohortInterface::class);
        $programYear = m::mock(ProgramYearInterface::class);
        $program = m::mock(ProgramInterface::class);
        $sessionUser = m::mock(SessionUserInterface::class);

        $school->shouldReceive('getId')->andReturn($schoolId);
        $cohort->shouldReceive('getProgramYear')->andReturn($programYear);
        $cohort->shouldReceive('getProgram')->andReturn($program);
        $cohort->shouldReceive('getSchool')->andReturn($school);
        $cohort->shouldReceive('getId')->andReturn($cohortId);
        $programYear->shouldReceive('isArchived')->andReturn(false);
        $programYear->shouldReceive('isLocked')->andReturn(false);
        $program->shouldReceive('getId')->andReturn($programId);
        $sessionUser->shouldReceive('rolesInSchool')->andReturn($schoolRoles);
        $sessionUser->shouldReceive('rolesInCohort')->andReturn($cohortRoles);

        $this->permissionMatrix
            ->shouldReceive('hasPermission')
            ->withArgs([$schoolId, Capabilities::CAN_DELETE_ALL_COHORTS, $schoolRoles])
            ->andReturn(false);
        $this->permissionMatrix
            ->shouldReceive('hasPermission')
            ->withArgs([$schoolId, Capabilities::CAN_DELETE_THEIR_COHORTS, $cohortRoles])
            ->andReturn(false);
        $this->permissionMatrix
            ->shouldReceive('hasPermission')
            ->withArgs([$schoolId, Capabilities::CAN_UPDATE_ALL_PROGRAMS, $schoolRoles])
            ->andReturn(true);

        $this->assertTrue($this->permissionChecker->canDeleteCohort($sessionUser, $cohort));
    }

    /**
     * @covers PermissionChecker::canDeleteCohort()
     */
    public function testCanNotDeleteCohorts()
    {
        $schoolRoles = ['foo'];
        $cohortRoles = ['bar'];
        $programRoles = ['baz'];
        $cohortId = 10;
        $programId = 15;
        $schoolId = 20;
        $school = m::mock(SchoolInterface::class);
        $cohort = m::mock(CohortInterface::class);
        $programYear = m::mock(ProgramYearInterface::class);
        $program = m::mock(ProgramInterface::class);
        $sessionUser = m::mock(SessionUserInterface::class);

        $school->shouldReceive('getId')->andReturn($schoolId);
        $cohort->shouldReceive('getProgramYear')->andReturn($programYear);
        $cohort->shouldReceive('getProgram')->andReturn($program);
        $cohort->shouldReceive('getSchool')->andReturn($school);
        $cohort->shouldReceive('getId')->andReturn($cohortId);
        $programYear->shouldReceive('isArchived')->andReturn(false);
        $programYear->shouldReceive('isLocked')->andReturn(false);
        $program->shouldReceive('getId')->andReturn($programId);
        $sessionUser->shouldReceive('rolesInSchool')->andReturn($schoolRoles);
        $sessionUser->shouldReceive('rolesInCohort')->andReturn($cohortRoles);
        $sessionUser->shouldReceive('rolesInProgram')->andReturn($programRoles);

        $this->permissionMatrix
            ->shouldReceive('hasPermission')
            ->withArgs([$schoolId, Capabilities::CAN_DELETE_ALL_COHORTS, $schoolRoles])
            ->andReturn(false);
        $this->permissionMatrix
            ->shouldReceive('hasPermission')
            ->withArgs([$schoolId, Capabilities::CAN_DELETE_THEIR_COHORTS, $cohortRoles])
            ->andReturn(false);
        $this->permissionMatrix
            ->shouldReceive('hasPermission')
            ->withArgs([$schoolId, Capabilities::CAN_UPDATE_ALL_PROGRAMS, $schoolRoles])
            ->andReturn(false);
        $this->permissionMatrix
            ->shouldReceive('hasPermission')
            ->withArgs([$schoolId, Capabilities::CAN_UPDATE_THEIR_PROGRAMS, $programRoles])
            ->andReturn(false);

        $this->assertFalse($this->permissionChecker->canDeleteCohort($sessionUser, $cohort));
    }

    /**
     * @covers PermissionChecker::canDeleteCohort()
     */
    public function testCanNotDeleteCohortsInLockedProgramYear()
    {
        $cohort = m::mock(CohortInterface::class);
        $programYear = m::mock(ProgramYearInterface::class);
        $sessionUser = m::mock(SessionUserInterface::class);

        $cohort->shouldReceive('getProgramYear')->andReturn($programYear);
        $programYear->shouldReceive('isArchived')->andReturn(false);
        $programYear->shouldReceive('isLocked')->andReturn(true);

        $this->assertFalse($this->permissionChecker->canDeleteCohort($sessionUser, $cohort));
    }

    /**
     * @covers PermissionChecker::canDeleteCohort()
     */
    public function testCanNotDeleteCohortsInArchivedProgramYear()
    {
        $cohort = m::mock(CohortInterface::class);
        $programYear = m::mock(ProgramYearInterface::class);
        $sessionUser = m::mock(SessionUserInterface::class);

        $cohort->shouldReceive('getProgramYear')->andReturn($programYear);
        $programYear->shouldReceive('isArchived')->andReturn(true);
        $programYear->shouldReceive('isLocked')->andReturn(false);

        $this->assertFalse($this->permissionChecker->canDeleteCohort($sessionUser, $cohort));
    }

    /**
     * @covers PermissionChecker::canCreateCohort()
     */
    public function testCanCreateCohort()
    {
        $schoolId = 10;
        $roles = ['foo'];
        $programYear = m::mock(ProgramYearInterface::class);
        $school = m::mock(SchoolInterface::class);
        $sessionUser = m::mock(SessionUserInterface::class);

        $school->shouldReceive(('getId'))->andReturn($schoolId);
        $programYear->shouldReceive('isLocked')->andReturn(false);
        $programYear->shouldReceive('isArchived')->andReturn(false);
        $programYear->shouldReceive('getSchool')->andReturn($school);
        $sessionUser->shouldReceive('rolesInSchool')->andReturn($roles);

        $this->permissionMatrix
            ->shouldReceive('hasPermission')
            ->withArgs([$schoolId, Capabilities::CAN_CREATE_COHORTS, $roles])
            ->andReturn(true);

        $this->assertTrue($this->permissionChecker->canCreateCohort($sessionUser, $programYear));
    }

    /**
     * @covers PermissionChecker::canCreateCohort()
     */
    public function testCanCreateCohortIfUserCanUpdateProgram()
    {
        $programId = 20;
        $schoolId = 10;
        $roles = ['foo'];
        $programYear = m::mock(ProgramYearInterface::class);
        $program = m::mock(ProgramInterface::class);
        $school = m::mock(SchoolInterface::class);
        $sessionUser = m::mock(SessionUserInterface::class);

        $school->shouldReceive(('getId'))->andReturn($schoolId);
        $program->shouldReceive('getId')->andReturn($programId);
        $programYear->shouldReceive('isLocked')->andReturn(false);
        $programYear->shouldReceive('isArchived')->andReturn(false);
        $programYear->shouldReceive('getSchool')->andReturn($school);
        $programYear->shouldReceive('getProgram')->andReturn($program);
        $sessionUser->shouldReceive('rolesInSchool')->andReturn($roles);

        $this->permissionMatrix
            ->shouldReceive('hasPermission')
            ->withArgs([$schoolId, Capabilities::CAN_CREATE_COHORTS, $roles])
            ->andReturn(false);
        $this->permissionMatrix
            ->shouldReceive('hasPermission')
            ->withArgs([$schoolId, Capabilities::CAN_UPDATE_ALL_PROGRAMS, $roles])
            ->andReturn(true);

        $this->assertTrue($this->permissionChecker->canCreateCohort($sessionUser, $programYear));
    }

    /**
     * @covers PermissionChecker::canCreateCohort()
     */
    public function testCanNotCreateCohort()
    {
        $programId = 20;
        $schoolId = 10;
        $schoolRoles = ['foo'];
        $programRoles = ['bar'];
        $programYear = m::mock(ProgramYearInterface::class);
        $program = m::mock(ProgramInterface::class);
        $school = m::mock(SchoolInterface::class);
        $sessionUser = m::mock(SessionUserInterface::class);

        $school->shouldReceive(('getId'))->andReturn($schoolId);
        $program->shouldReceive('getId')->andReturn($programId);
        $programYear->shouldReceive('isLocked')->andReturn(false);
        $programYear->shouldReceive('isArchived')->andReturn(false);
        $programYear->shouldReceive('getSchool')->andReturn($school);
        $programYear->shouldReceive('getProgram')->andReturn($program);
        $sessionUser->shouldReceive('rolesInSchool')->andReturn($schoolRoles);
        $sessionUser->shouldReceive('rolesInProgram')->andReturn($programRoles);

        $this->permissionMatrix
            ->shouldReceive('hasPermission')
            ->withArgs([$schoolId, Capabilities::CAN_CREATE_COHORTS, $schoolRoles])
            ->andReturn(false);
        $this->permissionMatrix
            ->shouldReceive('hasPermission')
            ->withArgs([$schoolId, Capabilities::CAN_UPDATE_ALL_PROGRAMS, $schoolRoles])
            ->andReturn(false);
        $this->permissionMatrix
            ->shouldReceive('hasPermission')
            ->withArgs([$schoolId, Capabilities::CAN_UPDATE_THEIR_PROGRAMS, $programRoles])
            ->andReturn(false);

        $this->assertFalse($this->permissionChecker->canCreateCohort($sessionUser, $programYear));
    }

    /**
     * @covers PermissionChecker::canCreateCohort()
     */
    public function testCanNotCreateCohortInLockedProgramYear()
    {
        $programYear = m::mock(ProgramYearInterface::class);
        $sessionUser = m::mock(SessionUserInterface::class);

        $programYear->shouldReceive('isLocked')->andReturn(true);
        $programYear->shouldReceive('isArchived')->andReturn(false);

        $this->assertFalse($this->permissionChecker->canCreateCohort($sessionUser, $programYear));
    }

    /**
     * @covers PermissionChecker::canCreateCohort()
     */
    public function testCanNotCreateCohortInArchivedProgramYear()
    {
        $programYear = m::mock(ProgramYearInterface::class);
        $sessionUser = m::mock(SessionUserInterface::class);

        $programYear->shouldReceive('isLocked')->andReturn(true);
        $programYear->shouldReceive('isArchived')->andReturn(false);

        $this->assertFalse($this->permissionChecker->canCreateCohort($sessionUser, $programYear));
    }

    public function testCanUpdateSchoolConfig()
    {
        $this->markTestIncomplete('This test has not been implemented yet.');
    }

    public function testCanDeleteSchoolConfig()
    {
        $this->markTestIncomplete('This test has not been implemented yet.');
    }

    public function testCanCreateSchoolConfig()
    {
        $this->markTestIncomplete('This test has not been implemented yet.');
    }

    public function testCanUpdateSchool()
    {
        $this->markTestIncomplete('This test has not been implemented yet.');
    }

    public function testCanDeleteSchool()
    {
        $this->markTestIncomplete('This test has not been implemented yet.');
    }

    public function testCanUpdateCompetency()
    {
        $this->markTestIncomplete('This test has not been implemented yet.');
    }

    public function testCanDeleteCompetency()
    {
        $this->markTestIncomplete('This test has not been implemented yet.');
    }

    public function testCanCreateCompetency()
    {
        $this->markTestIncomplete('This test has not been implemented yet.');
    }

    public function testCanUpdateVocabulary()
    {
        $this->markTestIncomplete('This test has not been implemented yet.');
    }

    public function testCanDeleteVocabulary()
    {
        $this->markTestIncomplete('This test has not been implemented yet.');
    }

    public function testCanCreateVocabulary()
    {
        $this->markTestIncomplete('This test has not been implemented yet.');
    }

    public function testCanUpdateTerm()
    {
        $this->markTestIncomplete('This test has not been implemented yet.');
    }

    public function testCanDeleteTerm()
    {
        $this->markTestIncomplete('This test has not been implemented yet.');
    }

    public function testCanCreateTerm()
    {
        $this->markTestIncomplete('This test has not been implemented yet.');
    }

    public function testCanUpdateInstructorGroup()
    {
        $this->markTestIncomplete('This test has not been implemented yet.');
    }

    public function testCanDeleteInstructorGroup()
    {
        $this->markTestIncomplete('This test has not been implemented yet.');
    }

    public function testCanCreateInstructorGroup()
    {
        $this->markTestIncomplete('This test has not been implemented yet.');
    }

    public function testCanUpdateCurriculumInventoryReport()
    {
        $this->markTestIncomplete('This test has not been implemented yet.');
    }

    public function testCanDeleteCurriculumInventoryReport()
    {
        $this->markTestIncomplete('This test has not been implemented yet.');
    }

    public function testCanCreateCurriculumInventoryReport()
    {
        $this->markTestIncomplete('This test has not been implemented yet.');
    }

    public function testCanUpdateCurriculumInventoryInstitution()
    {
        $this->markTestIncomplete('This test has not been implemented yet.');
    }

    public function testCanDeleteCurriculumInventoryInstitution()
    {
        $this->markTestIncomplete('This test has not been implemented yet.');
    }

    public function testCanCreateCurriculumInventoryInstitution()
    {
        $this->markTestIncomplete('This test has not been implemented yet.');
    }

    public function testCanUpdateLearnerGroup()
    {
        $this->markTestIncomplete('This test has not been implemented yet.');
    }

    public function testCanDeleteLearnerGroup()
    {
        $this->markTestIncomplete('This test has not been implemented yet.');
    }

    public function testCanCreateLearnerGroup()
    {
        $this->markTestIncomplete('This test has not been implemented yet.');
    }

    public function testCanUpdateUser()
    {
        $this->markTestIncomplete('This test has not been implemented yet.');
    }

    public function testCanDeleteUser()
    {
        $this->markTestIncomplete('This test has not been implemented yet.');
    }

    public function testCanCreateUser()
    {
        $this->markTestIncomplete('This test has not been implemented yet.');
    }
}
