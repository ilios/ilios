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
use Ilios\CoreBundle\Entity\SchoolConfigInterface;
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

    /**
     * @covers PermissionChecker::canUpdateCourse()
     */
    public function testCanUpdateTheirCourses()
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

    /**
     * @covers PermissionChecker::canUpdateCourse()
     */
    public function testCanNotUpdateCourses()
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

    /**
     * @covers PermissionChecker::canDeleteCourse()
     */
    public function testCanDeleteTheirCourses()
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

    /**
     * @covers PermissionChecker::canDeleteCourse()
     */
    public function testCanNotDeleteCourses()
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

    /**
     * @covers PermissionChecker::canCreateCourse()
     */
    public function testCanNotCreateCourse()
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

    /**
     * @covers PermissionChecker::canUnlockCourse()
     */
    public function testCanUnlockAllCourses()
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

    /**
     * @covers PermissionChecker::canUnlockCourse()
     */
    public function testCanUnlockTheirCourses()
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

    /**
     * @covers PermissionChecker::canUnlockCourse()
     */
    public function testCanNotUnlockCourses()
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

    /**
     * @covers PermissionChecker::canUnlockCourse()
     */
    public function testCanNotUnlockCourseIfCourseIsArchived()
    {
        $course = m::mock(CourseInterface::class);
        $sessionUser = m::mock(SessionUserInterface::class);

        $course->shouldReceive('isArchived')->andReturn(true);

        $this->assertFalse($this->permissionChecker->canUnlockCourse($sessionUser, $course));
    }

    /**
     * @covers PermissionChecker::canLockCourse()
     */
    public function testCanLockAllCourses()
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

    /**
     * @covers PermissionChecker::canLockCourse()
     */
    public function testCanLockTheirCourses()
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

    /**
     * @covers PermissionChecker::canLockCourse()
     */
    public function testCanNotLockCourses()
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

    /**
     * @covers PermissionChecker::canUnlockCourse()
     */
    public function testCanNotLockCourseIfCourseIsArchived()
    {
        $course = m::mock(CourseInterface::class);
        $sessionUser = m::mock(SessionUserInterface::class);

        $course->shouldReceive('isArchived')->andReturn(true);

        $this->assertFalse($this->permissionChecker->canLockCourse($sessionUser, $course));
    }

    /**
     * @covers PermissionChecker::canArchiveCourse()
     */
    public function testCanArchiveAllCourses()
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

    /**
     * @covers PermissionChecker::canArchiveCourse()
     */
    public function testCanArchiveTheirCourses()
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

    /**
     * @covers PermissionChecker::canArchiveCourse()
     */
    public function testCanNotArchiveCourses()
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

    /**
     * @covers PermissionChecker::canUpdateSession()
     */
    public function testCanUpdateAllSessions()
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

    /**
     * @covers PermissionChecker::canUpdateSession()
     */
    public function testCanUpdateTheirSessions()
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

    /**
     * @covers PermissionChecker::canUpdateSession()
     */
    public function testCanUpdateSessionsIfUserCanUpdateCourse()
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

    /**
     * @covers PermissionChecker::canUpdateSession()
     */
    public function testCanNotUpdateSessions()
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

    /**
     * @covers PermissionChecker::canDeleteSession()
     */
    public function testCanDeleteTheirSessions()
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

    /**
     * @covers PermissionChecker::canDeleteSession()
     */
    public function testCanDeleteSessionsIfUserCanUpdateCourse()
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

    /**
     * @covers PermissionChecker::canDeleteSession()
     */
    public function testCanNotDeleteSessions()
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

    /**
     * @covers PermissionChecker::canCreateSession()
     */
    public function testCanCreateSessionIfUserCanUpdateCourse()
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

    /**
     * @covers PermissionChecker::canCreateSession()
     */
    public function testCanNotCreateSession()
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

    /**
     * @covers PermissionChecker::canUpdateSessionType()
     */
    public function testCanUpdateSessionType()
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

    /**
     * @covers PermissionChecker::canUpdateSessionType()
     */
    public function testCanNotUpdateSessionType()
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

    /**
     * @covers PermissionChecker::canDeleteSessionType()
     */
    public function testCanDeleteSessionType()
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

    /**
     * @covers PermissionChecker::canDeleteSessionType()
     */
    public function testCanNotDeleteSessionType()
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

    /**
     * @covers PermissionChecker::canCreateSessionType()
     */
    public function testCanCreateSessionType()
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

    /**
     * @covers PermissionChecker::canCreateSessionType()
     */
    public function testCanNotCreateSessionType()
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

    /**
     * @covers PermissionChecker::canUpdateDepartment()
     */
    public function testCanUpdateDepartment()
    {
        $schoolId = 10;
        $rolesInSchool = ['foo'];
        $sessionUser = m::mock(SessionUserInterface::class);

        $sessionUser->shouldReceive('isRoot')->andReturn(false);
        $sessionUser->shouldReceive('rolesInSchool')->andReturn($rolesInSchool);
        $this->permissionMatrix
            ->shouldReceive('getPermittedRoles')
            ->withArgs([$schoolId, Capabilities::CAN_UPDATE_DEPARTMENTS])
            ->andReturn([]);
        $this->permissionMatrix
            ->shouldReceive('hasPermission')
            ->withArgs([$schoolId, Capabilities::CAN_UPDATE_DEPARTMENTS, $rolesInSchool])
            ->andReturn(true);

        $this->assertTrue($this->permissionChecker->canUpdateDepartment($sessionUser, $schoolId));
    }

    /**
     * @covers PermissionChecker::canUpdateDepartment()
     */
    public function testCanNotUpdateDepartment()
    {
        $schoolId = 10;
        $rolesInSchool = ['foo'];
        $sessionUser = m::mock(SessionUserInterface::class);

        $sessionUser->shouldReceive('isRoot')->andReturn(false);
        $sessionUser->shouldReceive('rolesInSchool')->andReturn($rolesInSchool);
        $this->permissionMatrix
            ->shouldReceive('getPermittedRoles')
            ->withArgs([$schoolId, Capabilities::CAN_UPDATE_DEPARTMENTS])
            ->andReturn([]);
        $this->permissionMatrix
            ->shouldReceive('hasPermission')
            ->withArgs([$schoolId, Capabilities::CAN_UPDATE_DEPARTMENTS, $rolesInSchool])
            ->andReturn(false);

        $this->assertFalse($this->permissionChecker->canUpdateDepartment($sessionUser, $schoolId));
    }

    /**
     * @covers PermissionChecker::canDeleteDepartment()
     */
    public function testCanDeleteDepartment()
    {
        $schoolId = 10;
        $rolesInSchool = ['foo'];
        $sessionUser = m::mock(SessionUserInterface::class);

        $sessionUser->shouldReceive('isRoot')->andReturn(false);
        $sessionUser->shouldReceive('rolesInSchool')->andReturn($rolesInSchool);
        $this->permissionMatrix
            ->shouldReceive('getPermittedRoles')
            ->withArgs([$schoolId, Capabilities::CAN_DELETE_DEPARTMENTS])
            ->andReturn([]);
        $this->permissionMatrix
            ->shouldReceive('hasPermission')
            ->withArgs([$schoolId, Capabilities::CAN_DELETE_DEPARTMENTS, $rolesInSchool])
            ->andReturn(true);

        $this->assertTrue($this->permissionChecker->canDeleteDepartment($sessionUser, $schoolId));
    }

    /**
     * @covers PermissionChecker::canDeleteDepartment()
     */
    public function testCanNotDeleteDepartment()
    {
        $schoolId = 10;
        $rolesInSchool = ['foo'];
        $sessionUser = m::mock(SessionUserInterface::class);

        $sessionUser->shouldReceive('isRoot')->andReturn(false);
        $sessionUser->shouldReceive('rolesInSchool')->andReturn($rolesInSchool);
        $this->permissionMatrix
            ->shouldReceive('getPermittedRoles')
            ->withArgs([$schoolId, Capabilities::CAN_DELETE_DEPARTMENTS])
            ->andReturn([]);
        $this->permissionMatrix
            ->shouldReceive('hasPermission')
            ->withArgs([$schoolId, Capabilities::CAN_DELETE_DEPARTMENTS, $rolesInSchool])
            ->andReturn(false);

        $this->assertFalse($this->permissionChecker->canDeleteDepartment($sessionUser, $schoolId));
    }

    /**
     * @covers PermissionChecker::canCreateDepartment()
     */
    public function testCanCreateDepartment()
    {
        $schoolId = 10;
        $rolesInSchool = ['foo'];
        $sessionUser = m::mock(SessionUserInterface::class);

        $sessionUser->shouldReceive('isRoot')->andReturn(false);
        $sessionUser->shouldReceive('rolesInSchool')->andReturn($rolesInSchool);
        $this->permissionMatrix
            ->shouldReceive('getPermittedRoles')
            ->withArgs([$schoolId, Capabilities::CAN_CREATE_DEPARTMENTS])
            ->andReturn([]);
        $this->permissionMatrix
            ->shouldReceive('hasPermission')
            ->withArgs([$schoolId, Capabilities::CAN_CREATE_DEPARTMENTS, $rolesInSchool])
            ->andReturn(true);

        $this->assertTrue($this->permissionChecker->canCreateDepartment($sessionUser, $schoolId));
    }

    /**
     * @covers PermissionChecker::canCreateDepartment()
     */
    public function testCanNotCreateDepartment()
    {
        $schoolId = 10;
        $rolesInSchool = ['foo'];
        $sessionUser = m::mock(SessionUserInterface::class);

        $sessionUser->shouldReceive('isRoot')->andReturn(false);
        $sessionUser->shouldReceive('rolesInSchool')->andReturn($rolesInSchool);
        $this->permissionMatrix
            ->shouldReceive('getPermittedRoles')
            ->withArgs([$schoolId, Capabilities::CAN_CREATE_DEPARTMENTS])
            ->andReturn([]);
        $this->permissionMatrix
            ->shouldReceive('hasPermission')
            ->withArgs([$schoolId, Capabilities::CAN_CREATE_DEPARTMENTS, $rolesInSchool])
            ->andReturn(false);

        $this->assertFalse($this->permissionChecker->canCreateDepartment($sessionUser, $schoolId));
    }

    /**
     * @covers PermissionChecker::canUpdateProgram()
     */
    public function testCanUpdateAllPrograms()
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

    /**
     * @covers PermissionChecker::canUpdateProgram()
     */
    public function testCanUpdateTheirPrograms()
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

    /**
     * @covers PermissionChecker::canUpdateProgram()
     */
    public function testCanNotUpdatePrograms()
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

    /**
     * @covers PermissionChecker::canDeleteProgram()
     */
    public function testCanDeleteAllPrograms()
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

    /**
     * @covers PermissionChecker::canDeleteProgram()
     */
    public function testCanDeleteTheirPrograms()
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

    /**
     * @covers PermissionChecker::canDeleteProgram()
     */
    public function testCanNotDeletePrograms()
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

    /**
     * @covers PermissionChecker::canCreateProgram()
     */
    public function testCanCreateProgram()
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

    /**
     * @covers PermissionChecker::canCreateProgram()
     */
    public function testCanNotCreateProgram()
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

    /**
     * @covers PermissionChecker::canUpdateProgramYear()
     */
    public function testCanUpdateAllProgramYears()
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

    /**
     * @covers PermissionChecker::canUpdateProgramYear()
     */
    public function testCanUpdateTheirProgramYears()
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

    /**
     * @covers PermissionChecker::canUpdateProgramYear()
     */
    public function testCanUpdateProgramYearsIfUserCanUpdateProgram()
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

    /**
     * @covers PermissionChecker::canUpdateProgramYear()
     */
    public function testCanNotUpdateProgramYears()
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

    /**
     * @covers PermissionChecker::canDeleteProgramYear()
     */
    public function testCanDeleteTheirProgramYears()
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

    /**
     * @covers PermissionChecker::canDeleteProgramYear()
     */
    public function testCanDeleteProgramYearsIfUserCanUpdateProgram()
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

    /**
     * @covers PermissionChecker::canDeleteProgramYear()
     */
    public function testCanNotDeleteProgramYears()
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

    /**
     * @covers PermissionChecker::canCreateProgramYear()
     */
    public function testCanCreateProgramYearIfUserCanUpdateProgram()
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

    /**
     * @covers PermissionChecker::canCreateProgramYear()
     */
    public function testCanNotCreateProgramYear()
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

    /**
     * @covers PermissionChecker::canLockProgramYear()
     */
    public function testCanLockAllProgramYears()
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

    /**
     * @covers PermissionChecker::canLockProgramYear()
     */
    public function testCanLockTheirProgramYears()
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

    /**
     * @covers PermissionChecker::canLockProgramYear()
     */
    public function testCanLockProgramYearsIfUserCanUpdateProgram()
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

    /**
     * @covers PermissionChecker::canLockProgramYear()
     */
    public function testCanNotLockProgramYearIfProgramYearIsArchived()
    {
        $programYear = m::mock(ProgramYearInterface::class);
        $programYear->shouldReceive('isArchived')->andReturn(true);
        $sessionUser = m::mock(SessionUserInterface::class);


        $this->assertFalse($this->permissionChecker->canLockProgramYear($sessionUser, $programYear));
    }

    /**
     * @covers PermissionChecker::canUnlockProgramYear()
     */
    public function testCanUnlockAllProgramYears()
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

    /**
     * @covers PermissionChecker::canUnlockProgramYear()
     */
    public function testCanUnlockTheirProgramYears()
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

    /**
     * @covers PermissionChecker::canUnlockProgramYear()
     */
    public function testCanUnlockProgramYearsIfUserCanUpdateProgram()
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

    /**
     * @covers PermissionChecker::canUnlockProgramYear()
     */
    public function testCanNotUnlockProgramYearIfProgramYearIsArchived()
    {
        $programYear = m::mock(ProgramYearInterface::class);
        $programYear->shouldReceive('isArchived')->andReturn(true);
        $sessionUser = m::mock(SessionUserInterface::class);


        $this->assertFalse($this->permissionChecker->canUnlockProgramYear($sessionUser, $programYear));
    }

    /**
     * @covers PermissionChecker::canArchiveProgramYear()
     */
    public function testCanArchiveAllProgramYears()
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

    /**
     * @covers PermissionChecker::canArchiveProgramYear()
     */
    public function testCanArchiveTheirProgramYears()
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

    /**
     * @covers PermissionChecker::canArchiveProgramYear()
     */
    public function testCanArchiveProgramYearsIfUserCanUpdateProgram()
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

    /**
     * @covers PermissionChecker::canUpdateSchoolConfig()
     */
    public function testCanUpdateSchoolConfig()
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

    /**
     * @covers PermissionChecker::canUpdateSchoolConfig()
     */
    public function testCanNotUpdateSchoolConfig()
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

    /**
     * @covers PermissionChecker::canDeleteSchoolConfig()
     */
    public function testCanDeleteSchoolConfig()
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

    /**
     * @covers PermissionChecker::canDeleteSchoolConfig()
     */
    public function testCanNotDeleteSchoolConfig()
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

    /**
     * @covers PermissionChecker::canCreateSchoolConfig()
     */
    public function testCanCreateSchoolConfig()
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

    /**
     * @covers PermissionChecker::canCreateSchoolConfig()
     */
    public function testCanNotCreateSchoolConfig()
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

    /**
     * @covers PermissionChecker::canUpdateSchool()
     */
    public function testCanUpdateSchool()
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

    /**
     * @covers PermissionChecker::canUpdateSchool()
     */
    public function testCanNotUpdateSchool()
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

    /**
     * @covers PermissionChecker::canUpdateCompetency()
     */
    public function testCanUpdateCompetency()
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

    /**
     * @covers PermissionChecker::canUpdateCompetency()
     */
    public function testCanNotUpdateCompetency()
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

    /**
     * @covers PermissionChecker::canDeleteCompetency()
     */
    public function testCanDeleteCompetency()
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

    /**
     * @covers PermissionChecker::canDeleteCompetency()
     */
    public function testCanNotDeleteCompetency()
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

    /**
     * @covers PermissionChecker::canCreateCompetency()
     */
    public function testCanCreateCompetency()
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

    /**
     * @covers PermissionChecker::canCreateCompetency()
     */
    public function testCanNotCreateCompetency()
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

    /**
     * @covers PermissionChecker::canUpdateVocabulary()
     */
    public function testCanUpdateVocabulary()
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

    /**
     * @covers PermissionChecker::canUpdateVocabulary()
     */
    public function testCanNotUpdateVocabulary()
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

    /**
     * @covers PermissionChecker::canDeleteVocabulary()
     */
    public function testCanDeleteVocabulary()
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

    /**
     * @covers PermissionChecker::canDeleteVocabulary()
     */
    public function testCanNotDeleteVocabulary()
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

    /**
     * @covers PermissionChecker::canCreateVocabulary()
     */
    public function testCanCreateVocabulary()
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

    /**
     * @covers PermissionChecker::canCreateVocabulary()
     */
    public function testCanNotCreateVocabulary()
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

    /**
     * @covers PermissionChecker::canUpdateTerm()
     */
    public function testCanUpdateTerm()
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

    /**
     * @covers PermissionChecker::canUpdateTerm()
     */
    public function testCanNotUpdateTerm()
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

    /**
     * @covers PermissionChecker::canDeleteTerm()
     */
    public function testCanDeleteTerm()
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

    /**
     * @covers PermissionChecker::canDeleteTerm()
     */
    public function testCanNotDeleteTerm()
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

    /**
     * @covers PermissionChecker::canCreateTerm()
     */
    public function testCanCreateTerm()
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

    /**
     * @covers PermissionChecker::canCreateTerm()
     */
    public function testCanNotCreateTerm()
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

    /**
     * @covers PermissionChecker::canUpdateInstructorGroup()
     */
    public function testCanUpdateInstructorGroup()
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

    /**
     * @covers PermissionChecker::canUpdateInstructorGroup()
     */
    public function testCanNotUpdateInstructorGroup()
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

    /**
     * @covers PermissionChecker::canDeleteInstructorGroup()
     */
    public function testCanDeleteInstructorGroup()
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

    /**
     * @covers PermissionChecker::canDeleteInstructorGroup()
     */
    public function testCanNotDeleteInstructorGroup()
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

    /**
     * @covers PermissionChecker::canCreateInstructorGroup()
     */
    public function testCanCreateInstructorGroup()
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

    /**
     * @covers PermissionChecker::canCreateInstructorGroup()
     */
    public function testCanNotCreateInstructorGroup()
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

    /**
     * @covers PermissionChecker::canUpdateCurriculumInventoryReport()
     */
    public function testCanUpdateAllCurriculumInventoryReports()
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

    /**
     * @covers PermissionChecker::canUpdateCurriculumInventoryReport()
     */
    public function testCanUpdateTheirCurriculumInventoryReports()
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

    /**
     * @covers PermissionChecker::canUpdateCurriculumInventoryReport()
     */
    public function testCanNotUpdateCurriculumInventoryReport()
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

    /**
     * @covers PermissionChecker::canDeleteCurriculumInventoryReport()
     */
    public function testCanDeleteAllCurriculumInventoryReports()
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

    /**
     * @covers PermissionChecker::canDeleteCurriculumInventoryReport()
     */
    public function testCanDeleteTheirCurriculumInventoryReports()
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

    /**
     * @covers PermissionChecker::canDeleteCurriculumInventoryReport()
     */
    public function testCanNotDeleteCurriculumInventoryReport()
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

    /**
     * @covers PermissionChecker::canCreateCurriculumInventoryReport()
     */
    public function testCanCreateCurriculumInventoryReport()
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

    /**
     * @covers PermissionChecker::canCreateCurriculumInventoryReport()
     */
    public function testCanNotCreateCurriculumInventoryReport()
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

    /**
     * @covers PermissionChecker::canUpdateCurriculumInventoryInstitution()
     */
    public function testCanUpdateCurriculumInventoryInstitution()
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

    /**
     * @covers PermissionChecker::canUpdateCurriculumInventoryInstitution()
     */
    public function testCanNotUpdateCurriculumInventoryInstitution()
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

    /**
     * @covers PermissionChecker::canDeleteCurriculumInventoryInstitution()
     */
    public function testCanDeleteCurriculumInventoryInstitution()
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

    /**
     * @covers PermissionChecker::canDeleteCurriculumInventoryInstitution()
     */
    public function testCanNotDeleteCurriculumInventoryInstitution()
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

    /**
     * @covers PermissionChecker::canCreateCurriculumInventoryInstitution()
     */
    public function testCanCreateCurriculumInventoryInstitution()
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

    /**
     * @covers PermissionChecker::canCreateCurriculumInventoryInstitution()
     */
    public function testCanNotCreateCurriculumInventoryInstitution()
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

    /**
     * @covers PermissionChecker::canUpdateLearnerGroup()
     */
    public function testCanUpdateLearnerGroup()
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

    /**
     * @covers PermissionChecker::canUpdateLearnerGroup()
     */
    public function testCanNotUpdateLearnerGroup()
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

    /**
     * @covers PermissionChecker::canDeleteLearnerGroup()
     */
    public function testCanDeleteLearnerGroup()
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

    /**
     * @covers PermissionChecker::canDeleteLearnerGroup()
     */
    public function testCanNotDeleteLearnerGroup()
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

    /**
     * @covers PermissionChecker::canCreateLearnerGroup()
     */
    public function testCanCreateLearnerGroup()
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

    /**
     * @covers PermissionChecker::canCreateLearnerGroup()
     */
    public function testCanNotCreateLearnerGroup()
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

    /**
     * @covers PermissionChecker::canUpdateUser()
     */
    public function testCanUpdateUser()
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

    /**
     * @covers PermissionChecker::canUpdateUser()
     */
    public function testCanNotUpdateUser()
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

    /**
     * @covers PermissionChecker::canDeleteUser()
     */
    public function testCanDeleteUser()
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

    /**
     * @covers PermissionChecker::canDeleteUser()
     */
    public function testCanNotDeleteUser()
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

    /**
     * @covers PermissionChecker::canCreateUser()
     */
    public function testCanCreateUser()
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

    /**
     * @covers PermissionChecker::canCreateUser()
     */
    public function testCanNotCreateUser()
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


    /**
     * @covers PermissionChecker::canViewLearnerGroup()
     */
    public function testCanViewLearnerGroupIfUseIsInLearnerGroup()
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


    /**
     * @covers PermissionChecker::canViewLearnerGroup()
     */
    public function testCanViewLearnerGroupIfUserPerformsNonLearnerFunction()
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

    /**
     * @covers PermissionChecker::canViewLearnerGroup()
     */
    public function testCanNotViewLearnerGroup()
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

    /**
     * @covers PermissionChecker::canCreateUsersInAnySchool()
     */
    public function testCanCreateUsersInAnySchool()
    {
        $this->markTestIncomplete('This test has not been implemented yet.');
    }

    /**
     * @covers PermissionChecker::canCreateOrUpdateUsersInAnySchool()
     */
    public function testCanCreateOrUpdateUsersInAnySchool()
    {
        $this->markTestIncomplete('This test has not been implemented yet.');
    }

    /**
     * @covers PermissionChecker::canCreateCurriculumInventoryReportInAnySchool()
     */
    public function testCanCreateCurriculumInventoryReportUserInAnySchool()
    {
        $this->markTestIncomplete('This test has not been implemented yet.');
    }

}
