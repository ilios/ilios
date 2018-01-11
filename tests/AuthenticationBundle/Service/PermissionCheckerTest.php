<?php

namespace Tests\AuthenticationBundle\Service;

use Ilios\AuthenticationBundle\Classes\Capabilities;
use Ilios\AuthenticationBundle\Classes\PermissionMatrixInterface;
use Ilios\AuthenticationBundle\Classes\SessionUserInterface;
use Ilios\AuthenticationBundle\Service\PermissionChecker;
use Ilios\CoreBundle\Entity\CourseInterface;
use Ilios\CoreBundle\Entity\SchoolInterface;
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

    public function testCanUpdateSession()
    {
        $this->markTestIncomplete('This test has not been implemented yet.');
    }

    public function testCanDeleteSession()
    {
        $this->markTestIncomplete('This test has not been implemented yet.');
    }

    public function testCanCreateSession()
    {
        $this->markTestIncomplete('This test has not been implemented yet.');
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

    public function testCanUpdateProgramYear()
    {
        $this->markTestIncomplete('This test has not been implemented yet.');
    }

    public function testCanDeleteProgramYear()
    {
        $this->markTestIncomplete('This test has not been implemented yet.');
    }

    public function testCanCreateProgramYear()
    {
        $this->markTestIncomplete('This test has not been implemented yet.');
    }

    public function testCanUnlockProgramYear()
    {
        $this->markTestIncomplete('This test has not been implemented yet.');
    }

    public function testCanUnarchiveProgramYear()
    {
        $this->markTestIncomplete('This test has not been implemented yet.');
    }

    public function testCanUpdateCohort()
    {
        $this->markTestIncomplete('This test has not been implemented yet.');
    }

    public function testCanDeleteCohort()
    {
        $this->markTestIncomplete('This test has not been implemented yet.');
    }

    public function testCanCreateCohort()
    {
        $this->markTestIncomplete('This test has not been implemented yet.');
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
