<?php

namespace App\Tests\Classes;

use App\Classes\SessionUser;
use App\Classes\UserRoles;
use App\Entity\Manager\UserManager;
use App\Entity\School;
use App\Entity\SchoolInterface;
use App\Entity\UserInterface;
use App\Service\AuthenticationInterface;
use App\Tests\TestCase;
use Mockery as m;
use function Sentry\withScope;

/**
 * Class SessionUserTest
 * @package App\Tests\Classes
 */
class SessionUserTest extends TestCase
{
    /**
     * @var m\MockInterface
     */
    protected $iliosUser;

    /**
     * @var m\MockInterface
     */
    protected $userManager;

    /**
     * @var SessionUser
     */
    protected $sessionUser;

    /**
     * @var int
     */
    protected $userId;

    /**
     * @var SchoolInterface
     */
    protected $school;

    /**
     * @inheritdoc
     */
    public function setUp()
    {
        $this->userId = 1;
        $this->userManager = m::mock(UserManager::class);

        $this->school = m::mock(School::class);
        $this->school->shouldReceive('getId')->andReturn(1);

        $this->iliosUser = $this->createMockUser($this->userId, $this->school);

        $this->sessionUser = new SessionUser($this->iliosUser, $this->userManager);
    }

    /**
     * @inheritdoc
     */
    public function tearDown() : void
    {
        unset($this->sessionUser);
        unset($this->iliosUser);
        unset($this->userManager);
        unset($this->school);
        unset($this->userId);
    }

    /**
     * @covers SessionUser::isDirectingCourse
     */
    public function testIsDirectingCourse()
    {
        $directedCourseAndSchoolIds = ['courseIds' => [1, 2, 3]];
        $this->userManager
            ->shouldReceive('getDirectedCourseAndSchoolIds')
            ->andReturn($directedCourseAndSchoolIds);
        $this->assertTrue($this->sessionUser->isDirectingCourse(1));
    }

    /**
     * @covers SessionUser::isDirectingCourse
     */
    public function testIsNotDirectingCourse()
    {
        $directedCourseAndSchoolIds = ['courseIds' => [2, 3]];
        $this->userManager
            ->shouldReceive('getDirectedCourseAndSchoolIds')
            ->andReturn($directedCourseAndSchoolIds);
        $this->assertFalse($this->sessionUser->isDirectingCourse(1));
    }

    /**
     * @covers SessionUser::isAdministeringCourse
     */
    public function testIsAdministeringCourse()
    {
        $administeredCourseAndIds = ['courseIds' => [1, 2, 3]];
        $this->userManager
            ->shouldReceive('getAdministeredCourseAndSchoolIds')
            ->andReturn($administeredCourseAndIds);
        $this->assertTrue($this->sessionUser->isAdministeringCourse(1));
    }

    /**
     * @covers SessionUser::isAdministeringCourse
     */
    public function testIsNotAdministeringCourse()
    {
        $administeredCourseAndIds = ['courseIds' => [2, 3]];
        $this->userManager
            ->shouldReceive('getAdministeredCourseAndSchoolIds')
            ->andReturn($administeredCourseAndIds);
        $this->assertFalse($this->sessionUser->isAdministeringCourse(1));
    }

    /**
     * @covers SessionUser::isDirectingSchool
     */
    public function testIsDirectingSchool()
    {
        $directedSchoolIds = [1, 2, 3];
        $this->userManager->shouldReceive('getDirectedSchoolIds')->andReturn($directedSchoolIds);
        $this->assertTrue($this->sessionUser->isDirectingSchool(1));
    }

    /**
     * @covers SessionUser::isDirectingSchool
     */
    public function testIsNotDirectingSchool()
    {
        $directedSchoolIds = [2, 3];
        $this->userManager->shouldReceive('getDirectedSchoolIds')->andReturn($directedSchoolIds);
        $this->assertFalse($this->sessionUser->isDirectingSchool(1));
    }

    /**
     * @covers SessionUser::isAdministeringSchool
     */
    public function testIsAdministeringSchool()
    {
        $administeredSchoolIds = [1, 2, 3];
        $this->userManager->shouldReceive('getAdministeredSchoolIds')->andReturn($administeredSchoolIds);
        $this->assertTrue($this->sessionUser->isAdministeringSchool(1));
    }

    /**
     * @covers SessionUser::isAdministeringSchool
     */
    public function testIsNotAdministeringSchool()
    {
        $administeredSchoolIds = [2, 3];
        $this->userManager->shouldReceive('getAdministeredSchoolIds')->andReturn($administeredSchoolIds);
        $this->assertFalse($this->sessionUser->isAdministeringSchool(1));
    }

    /**
     * @covers SessionUser::isDirectingCourseInSchool
     */
    public function testIsDirectingCourseInSchool()
    {
        $directedCourseAndSchoolIds = ['schoolIds' => [1, 2, 3]];
        $this->userManager
            ->shouldReceive('getDirectedCourseAndSchoolIds')
            ->andReturn($directedCourseAndSchoolIds);
        $this->assertTrue($this->sessionUser->isDirectingCourseInSchool(1));
    }

    /**
     * @covers SessionUser::isDirectingCourseInSchool
     */
    public function testIsNotDirectingCourseInSchool()
    {
        $directedCourseAndSchoolIds = ['schoolIds' => [2, 3]];
        $this->userManager
            ->shouldReceive('getDirectedCourseAndSchoolIds')
            ->andReturn($directedCourseAndSchoolIds);
        $this->assertFalse($this->sessionUser->isDirectingCourseInSchool(1));
    }

    /**
     * @covers SessionUser::isAdministeringCourseInSchool
     */
    public function testIsAdministeringCourseInSchool()
    {
        $administeredCourseAndSchoolIds = ['schoolIds' => [1, 2, 3]];
        $this->userManager
            ->shouldReceive('getAdministeredCourseAndSchoolIds')
            ->andReturn($administeredCourseAndSchoolIds);
        $this->assertTrue($this->sessionUser->isAdministeringCourseInSchool(1));
    }

    /**
     * @covers SessionUser::isAdministeringCourseInSchool
     */
    public function testIsNotAdministeringCourseInSchool()
    {
        $administeredCourseAndSchoolIds = ['schoolIds' => [2, 3]];
        $this->userManager
            ->shouldReceive('getAdministeredCourseAndSchoolIds')
            ->andReturn($administeredCourseAndSchoolIds);
        $this->assertFalse($this->sessionUser->isAdministeringCourseInSchool(1));
    }

    /**
     * @covers SessionUser::isAdministeringSessionInSchool
     */
    public function testIsAdministeringSessionInSchool()
    {
        $administeredSessionCourseAndSchoolIds = ['schoolIds' => [1, 2, 3]];
        $this->userManager
            ->shouldReceive('getAdministeredSessionCourseAndSchoolIds')
            ->andReturn($administeredSessionCourseAndSchoolIds);
        $this->assertTrue($this->sessionUser->isAdministeringSessionInSchool(1));
    }

    /**
     * @covers SessionUser::isAdministeringSessionInSchool
     */
    public function testIsNotAdministeringSessionInSchool()
    {
        $administeredSessionCourseAndSchoolIds = ['schoolIds' => [2, 3]];
        $this->userManager
            ->shouldReceive('getAdministeredSessionCourseAndSchoolIds')
            ->andReturn($administeredSessionCourseAndSchoolIds);
        $this->assertFalse($this->sessionUser->isAdministeringSessionInSchool(1));
    }

    /**
     * @covers SessionUser::isTeachingCourseInSchool
     */
    public function testIsTeachingCourseInSchool()
    {
        $taughtSchoolIds = ['schoolIds' => [1, 2, 3]];
        $this->userManager
            ->shouldReceive('getInstructedOfferingIlmSessionCourseAndSchoolIds')
            ->andReturn($taughtSchoolIds);
        $this->assertTrue($this->sessionUser->isTeachingCourseInSchool(1));
    }

    /**
     * @covers SessionUser::isTeachingCourseInSchool
     */
    public function testIsNotTeachingCourseInSchool()
    {
        $taughtSchoolIds = ['schoolIds' => [2, 3]];
        $this->userManager
            ->shouldReceive('getInstructedOfferingIlmSessionCourseAndSchoolIds')
            ->andReturn($taughtSchoolIds);
        $this->assertFalse($this->sessionUser->isTeachingCourseInSchool(1));
    }

    /**
     * @covers SessionUser::isTeachingCourse
     */
    public function testIsTeachingCourse()
    {
        $taughtCourseIds = ['courseIds' => [1, 2, 3]];
        $this->userManager
            ->shouldReceive('getInstructedOfferingIlmSessionCourseAndSchoolIds')
            ->andReturn($taughtCourseIds);
        $this->assertTrue($this->sessionUser->isTeachingCourse(1));
    }

    /**
     * @covers SessionUser::isTeachingCourse
     */
    public function testIsNotTeachingCourse()
    {
        $taughtCourseIds = ['courseIds' => [2, 3]];
        $this->userManager
            ->shouldReceive('getInstructedOfferingIlmSessionCourseAndSchoolIds')
            ->andReturn($taughtCourseIds);
        $this->assertFalse($this->sessionUser->isTeachingCourse(1));
    }

    /**
     * @covers SessionUser::isAdministeringSessionInCourse
     */
    public function testIsAdministeringSessionInCourse()
    {
        $administeredCourseIds = ['courseIds' => [1, 2, 3]];
        $this->userManager
            ->shouldReceive('getAdministeredSessionCourseAndSchoolIds')
            ->andReturn($administeredCourseIds);
        $this->assertTrue($this->sessionUser->isAdministeringSessionInCourse(1));
    }

    /**
     * @covers SessionUser::isAdministeringSessionInCourse
     */
    public function testIsNotAdministeringSessionInCourse()
    {
        $administeredCourseIds = ['courseIds' => [2, 3]];
        $this->userManager
            ->shouldReceive('getAdministeredSessionCourseAndSchoolIds')
            ->andReturn($administeredCourseIds);
        $this->assertFalse($this->sessionUser->isAdministeringSessionInCourse(1));
    }

    /**
     * @covers SessionUser::isAdministeringSession
     */
    public function testIsAdministeringSession()
    {
        $administeredSessionIds = ['sessionIds' => [1, 2, 3]];
        $this->userManager
            ->shouldReceive('getAdministeredSessionCourseAndSchoolIds')
            ->andReturn($administeredSessionIds);
        $this->assertTrue($this->sessionUser->isAdministeringSession(1));
    }

    /**
     * @covers SessionUser::isAdministeringSession
     */
    public function testIsNotAdministeringSession()
    {
        $administeredSessionIds = ['sessionIds' => [2, 3]];
        $this->userManager
            ->shouldReceive('getAdministeredSessionCourseAndSchoolIds')
            ->andReturn($administeredSessionIds);
        $this->assertFalse($this->sessionUser->isAdministeringSession(1));
    }

    /**
     * @covers SessionUser::isTeachingSession
     */
    public function testIsTeachingSession()
    {
        $taughtSessionIds = ['sessionIds' => [1, 2, 3]];
        $this->userManager
            ->shouldReceive('getInstructedOfferingIlmSessionCourseAndSchoolIds')
            ->andReturn($taughtSessionIds);
        $this->assertTrue($this->sessionUser->isTeachingSession(1));
    }

    /**
     * @covers SessionUser::isTeachingSession
     */
    public function testIsNotTeachingSession()
    {
        $taughtSessionIds = ['sessionIds' => [2, 3]];
        $this->userManager
            ->shouldReceive('getInstructedOfferingIlmSessionCourseAndSchoolIds')
            ->andReturn($taughtSessionIds);
        $this->assertFalse($this->sessionUser->isTeachingSession(1));
    }

    /**
     * @covers SessionUser::isInstructingOffering
     */
    public function testIsInstructingOffering()
    {
        $taughtSessionIds = ['offeringIds' => [1, 2, 3]];
        $this->userManager
            ->shouldReceive('getInstructedOfferingIlmSessionCourseAndSchoolIds')
            ->andReturn($taughtSessionIds);
        $this->assertTrue($this->sessionUser->isInstructingOffering(1));
    }

    /**
     * @covers SessionUser::isInstructingOffering
     */
    public function testIsNotInstructingOffering()
    {
        $taughtSessionIds = ['offeringIds' => [2, 3]];
        $this->userManager
            ->shouldReceive('getInstructedOfferingIlmSessionCourseAndSchoolIds')
            ->andReturn($taughtSessionIds);
        $this->assertFalse($this->sessionUser->isInstructingOffering(1));
    }

    /**
     * @covers SessionUser::isInstructingIlm
     */
    public function testIsInstructingIlm()
    {
        $taughtSessionIds = ['ilmIds' => [1, 2, 3]];
        $this->userManager
            ->shouldReceive('getInstructedOfferingIlmSessionCourseAndSchoolIds')
            ->andReturn($taughtSessionIds);
        $this->assertTrue($this->sessionUser->isInstructingIlm(1));
    }

    /**
     * @covers SessionUser::isInstructingIlm
     */
    public function testIsNotInstructingIlm()
    {
        $taughtSessionIds = ['ilmIds' => [2, 3]];
        $this->userManager
            ->shouldReceive('getInstructedOfferingIlmSessionCourseAndSchoolIds')
            ->andReturn($taughtSessionIds);
        $this->assertFalse($this->sessionUser->isInstructingIlm(1));
    }

    /**
     * @covers SessionUser::isDirectingProgram
     */
    public function testIsDirectingProgram()
    {
        $directedProgramIds = ['programIds' => [1, 2, 3]];
        $this->userManager
            ->shouldReceive('getDirectedProgramAndSchoolIds')
            ->andReturn($directedProgramIds);
        $this->assertTrue($this->sessionUser->isDirectingProgram(1));
    }

    /**
     * @covers SessionUser::isDirectingProgram
     */
    public function testIsNotDirectingProgram()
    {
        $directedProgramIds = ['programIds' => [2, 3]];
        $this->userManager
            ->shouldReceive('getDirectedProgramAndSchoolIds')
            ->andReturn($directedProgramIds);
        $this->assertFalse($this->sessionUser->isDirectingProgram(1));
    }

    /**
     * @covers SessionUser::isDirectingProgramYear
     */
    public function testIsDirectingProgramYear()
    {
        $directedProgramYearIds = ['programYearIds' => [1, 2, 3]];
        $this->userManager
            ->shouldReceive('getDirectedProgramYearProgramAndSchoolIds')
            ->andReturn($directedProgramYearIds);
        $this->assertTrue($this->sessionUser->isDirectingProgramYear(1));
    }

    /**
     * @covers SessionUser::isDirectingProgramYear
     */
    public function testIsNotDirectingProgramYear()
    {
        $directedProgramYearIds = ['programYearIds' => [2, 3]];
        $this->userManager
            ->shouldReceive('getDirectedProgramYearProgramAndSchoolIds')
            ->andReturn($directedProgramYearIds);
        $this->assertFalse($this->sessionUser->isDirectingProgramYear(1));
    }

    /**
     * @covers SessionUser::isDirectingProgramYearInProgram
     */
    public function testIsDirectingProgramYearInProgram()
    {
        $directedProgramIds = ['programIds' => [1, 2, 3]];
        $this->userManager
            ->shouldReceive('getDirectedProgramYearProgramAndSchoolIds')
            ->andReturn($directedProgramIds);
        $this->assertTrue($this->sessionUser->isDirectingProgramYearInProgram(1));
    }

    /**
     * @covers SessionUser::isDirectingProgramYearInProgram
     */
    public function testIsNotDirectingProgramYearInProgram()
    {
        $directedProgramIds = ['programIds' => [2, 3]];
        $this->userManager
            ->shouldReceive('getDirectedProgramYearProgramAndSchoolIds')
            ->andReturn($directedProgramIds);
        $this->assertFalse($this->sessionUser->isDirectingProgramYearInProgram(1));
    }

    /**
     * @covers SessionUser::isAdministeringCurriculumInventoryReport
     */
    public function testIsAdministeringCurriculumInventoryReport()
    {
        $administeredReportIds = ['reportIds' => [1, 2, 3]];
        $this->userManager
            ->shouldReceive('getAdministeredCurriculumInventoryReportAndSchoolIds')
            ->andReturn($administeredReportIds);
        $this->assertTrue($this->sessionUser->isAdministeringCurriculumInventoryReport(1));
    }

    /**
     * @covers SessionUser::isAdministeringCurriculumInventoryReport
     */
    public function testIsNotAdministeringCurriculumInventoryReport()
    {
        $administeredReportIds = ['reportIds' => [2, 3]];
        $this->userManager
            ->shouldReceive('getAdministeredCurriculumInventoryReportAndSchoolIds')
            ->andReturn($administeredReportIds);
        $this->assertFalse($this->sessionUser->isAdministeringCurriculumInventoryReport(1));
    }

    /**
     * @covers SessionUser::isAdministeringCurriculumInventoryReportInSchool
     */
    public function testIsAdministeringCurriculumInventoryReportInSchool()
    {
        $administeredSchoolIds = ['schoolIds' => [1, 2, 3]];
        $this->userManager
            ->shouldReceive('getAdministeredCurriculumInventoryReportAndSchoolIds')
            ->andReturn($administeredSchoolIds);
        $this->assertTrue($this->sessionUser->isAdministeringCurriculumInventoryReportInSchool(1));
    }

    /**
     * @covers SessionUser::isAdministeringCurriculumInventoryReportInSchool
     */
    public function testIsNotAdministeringCurriculumInventoryReportInSchool()
    {
        $administeredSchoolIds = ['schoolIds' => [2, 3]];
        $this->userManager
            ->shouldReceive('getAdministeredCurriculumInventoryReportAndSchoolIds')
            ->andReturn($administeredSchoolIds);
        $this->assertFalse($this->sessionUser->isAdministeringCurriculumInventoryReportInSchool(1));
    }

    /**
     * @covers SessionUser::isInLearnerGroup()
     */
    public function testIsInLearnerGroup()
    {
        $learnerGroupIds = [1, 2, 3];
        $this->userManager
            ->shouldReceive('getLearnerGroupIds')
            ->andReturn($learnerGroupIds);
        $this->assertTrue($this->sessionUser->isInLearnerGroup(1));
    }

    /**
     * @covers SessionUser::isInLearnerGroup()
     */
    public function testIsNotInLearnerGroup()
    {
        $learnerGroupIds = [2, 3];
        $this->userManager
            ->shouldReceive('getLearnerGroupIds')
            ->andReturn($learnerGroupIds);
        $this->assertFalse($this->sessionUser->isInLearnerGroup(1));
    }


    /**
     * @covers SessionUser::rolesInSchool
     */
    public function testRolesInSchool()
    {
        $schoolId = 2;
        $roles = [
            UserRoles::SCHOOL_DIRECTOR,
            UserRoles::SCHOOL_ADMINISTRATOR,
            UserRoles::COURSE_DIRECTOR,
            UserRoles::COURSE_ADMINISTRATOR,
            UserRoles::SESSION_ADMINISTRATOR,
            UserRoles::COURSE_INSTRUCTOR,
            UserRoles::CURRICULUM_INVENTORY_REPORT_ADMINISTRATOR,
            UserRoles::PROGRAM_DIRECTOR,
        ];
        $this->userManager
            ->shouldReceive('getDirectedSchoolIds')
            ->with($this->userId)
            ->andReturn([$schoolId]);
        $this->userManager
            ->shouldReceive('getAdministeredSchoolIds')
            ->with($this->userId)
            ->andReturn([$schoolId]);
        $this->userManager
            ->shouldReceive('getDirectedCourseAndSchoolIds')
            ->with($this->userId)
            ->andReturn(['schoolIds' => [$schoolId]]);
        $this->userManager
            ->shouldReceive('getAdministeredCourseAndSchoolIds')
            ->with($this->userId)
            ->andReturn(['schoolIds' => [$schoolId]]);
        $this->userManager
            ->shouldReceive('getAdministeredSessionCourseAndSchoolIds')
            ->with($this->userId)
            ->andReturn(['schoolIds' => [$schoolId]]);
        $this->userManager
            ->shouldReceive('getInstructedOfferingIlmSessionCourseAndSchoolIds')
            ->with($this->userId)
            ->andReturn(['schoolIds' => [$schoolId]]);
        $this->userManager
            ->shouldReceive('getAdministeredCurriculumInventoryReportAndSchoolIds')
            ->with($this->userId)
            ->andReturn(['schoolIds' => [$schoolId]]);
        $this->userManager
            ->shouldReceive('getDirectedProgramAndSchoolIds')
            ->with($this->userId)
            ->andReturn(['schoolIds' => [$schoolId]]);
        $this->assertEquals($roles, $this->sessionUser->rolesInSchool($schoolId));
    }

    /**
     * @covers SessionUser::rolesInCourse
     */
    public function testRolesInCourse()
    {
        $courseId = 2;
        $roles = [UserRoles::COURSE_DIRECTOR,
            UserRoles::COURSE_ADMINISTRATOR,
            UserRoles::SESSION_ADMINISTRATOR,
            UserRoles::COURSE_INSTRUCTOR
        ];
        $this->userManager
            ->shouldReceive('getDirectedCourseAndSchoolIds')
            ->with($this->userId)
            ->andReturn(['courseIds' => [$courseId]]);
        $this->userManager
            ->shouldReceive('getAdministeredCourseAndSchoolIds')
            ->with($this->userId)
            ->andReturn(['courseIds' => [$courseId]]);
        $this->userManager
            ->shouldReceive('getAdministeredSessionCourseAndSchoolIds')
            ->with($this->userId)
            ->andReturn(['courseIds' => [$courseId]]);
        $this->userManager
            ->shouldReceive('getInstructedOfferingIlmSessionCourseAndSchoolIds')
            ->with($this->userId)
            ->andReturn(['courseIds' => [$courseId]]);
        $this->assertEquals($roles, $this->sessionUser->rolesInCourse($courseId));
    }

    /**
     * @covers SessionUser::rolesInSession
     */
    public function testRolesInSession()
    {
        $sessionId = 2;
        $roles = [UserRoles::SESSION_ADMINISTRATOR, UserRoles::SESSION_INSTRUCTOR];
        $this->userManager
            ->shouldReceive('getAdministeredSessionCourseAndSchoolIds')
            ->with($this->userId)
            ->andReturn(['sessionIds' => [$sessionId]]);
        $this->userManager
            ->shouldReceive('getInstructedOfferingIlmSessionCourseAndSchoolIds')
            ->with($this->userId)
            ->andReturn(['sessionIds' => [$sessionId]]);
        $this->assertEquals($roles, $this->sessionUser->rolesInSession($sessionId));
    }

    /**
     * @covers SessionUser::rolesInProgram
     */
    public function testRolesInProgram()
    {
        $programId = 2;
        $roles = [UserRoles::PROGRAM_DIRECTOR, UserRoles::PROGRAM_YEAR_DIRECTOR];
        $this->userManager
            ->shouldReceive('getDirectedProgramAndSchoolIds')
            ->with($this->userId)
            ->andReturn(['programIds' => [$programId]]);
        $this->userManager
            ->shouldReceive('getDirectedProgramYearProgramAndSchoolIds')
            ->with($this->userId)
            ->andReturn(['programIds' => [$programId]]);
        $this->assertEquals($roles, $this->sessionUser->rolesInProgram($programId));
    }

    /**
     * @covers SessionUser::rolesInProgramYear
     */
    public function testRolesInProgramYear()
    {
        $programYearId = 2;
        $roles = [UserRoles::PROGRAM_YEAR_DIRECTOR];
        $this->userManager
            ->shouldReceive('getDirectedProgramYearProgramAndSchoolIds')
            ->with($this->userId)
            ->andReturn(['programYearIds' => [$programYearId]]);
        $this->assertEquals($roles, $this->sessionUser->rolesInProgramYear($programYearId));
    }

    /**
     * @covers SessionUser::rolesInCurriculumInventoryReport
     */
    public function testRolesInCurriculumInventoryReport()
    {
        $reportId = 2;
        $roles = [UserRoles::CURRICULUM_INVENTORY_REPORT_ADMINISTRATOR];
        $this->userManager
            ->shouldReceive('getAdministeredCurriculumInventoryReportAndSchoolIds')
            ->with($this->userId)
            ->andReturn(['reportIds' => [$reportId]]);
        $this->assertEquals($roles, $this->sessionUser->rolesInCurriculumInventoryReport($reportId));
    }

    /**
     * @covers SessionUser::performsNonLearnerFunction()
     */
    public function testPerformsNonLearnerFunctionIfUserIsRoot()
    {
        $this->iliosUser = $this->createMockUser($this->userId, $this->school, true);
        $this->sessionUser = new SessionUser($this->iliosUser, $this->userManager);
        $this->assertTrue($this->sessionUser->performsNonLearnerFunction());
    }

    /**
     * @covers SessionUser::performsNonLearnerFunction()
     */
    public function testPerformsNonLearnerFunctionIfUserIsCourseDirector()
    {
        $courseIds = [2, 3];
        $this->userManager
            ->shouldReceive('getDirectedCourseAndSchoolIds')
            ->with($this->userId)
            ->andReturn(['courseIds' => $courseIds]);
        $this->assertTrue($this->sessionUser->performsNonLearnerFunction());
    }

    /**
     * @covers SessionUser::performsNonLearnerFunction()
     */
    public function testPerformsNonLearnerFunctionIfUserIsCourseAdministrator()
    {
        $courseIds = [2, 3];
        $this->userManager
            ->shouldReceive('getDirectedCourseAndSchoolIds')
            ->with($this->userId)
            ->andReturn(['courseIds' => []]);
        $this->userManager
            ->shouldReceive('getAdministeredCourseAndSchoolIds')
            ->with($this->userId)
            ->andReturn(['courseIds' => $courseIds]);
        $this->assertTrue($this->sessionUser->performsNonLearnerFunction());
    }

    /**
     * @covers SessionUser::performsNonLearnerFunction()
     */
    public function testPerformsNonLearnerFunctionIfUserIsSchoolDirector()
    {
        $schoolIds = [2, 3];
        $this->userManager
            ->shouldReceive('getDirectedCourseAndSchoolIds')
            ->with($this->userId)
            ->andReturn(['courseIds' => []]);
        $this->userManager
            ->shouldReceive('getAdministeredCourseAndSchoolIds')
            ->with($this->userId)
            ->andReturn(['courseIds' => []]);
        $this->userManager
            ->shouldReceive('getDirectedSchoolIds')
            ->with($this->userId)
            ->andReturn($schoolIds);
        $this->assertTrue($this->sessionUser->performsNonLearnerFunction());
    }

    /**
     * @covers SessionUser::performsNonLearnerFunction()
     */
    public function testPerformsNonLearnerFunctionIfUserIsSchoolAdministrator()
    {
        $schoolIds = [2, 3];
        $this->userManager
            ->shouldReceive('getDirectedCourseAndSchoolIds')
            ->with($this->userId)
            ->andReturn(['courseIds' => []]);
        $this->userManager
            ->shouldReceive('getAdministeredCourseAndSchoolIds')
            ->with($this->userId)
            ->andReturn(['courseIds' => []]);
        $this->userManager
            ->shouldReceive('getDirectedSchoolIds')
            ->with($this->userId)
            ->andReturn([]);
        $this->userManager
            ->shouldReceive('getAdministeredSchoolIds')
            ->with($this->userId)
            ->andReturn($schoolIds);
        $this->assertTrue($this->sessionUser->performsNonLearnerFunction());
    }

    /**
     * @covers SessionUser::performsNonLearnerFunction()
     */
    public function testPerformsNonLearnerFunctionIfUserIsInInstructorGroups()
    {
        $instructorGroupIds = [2, 3];
        $this->userManager
            ->shouldReceive('getDirectedCourseAndSchoolIds')
            ->with($this->userId)
            ->andReturn(['courseIds' => []]);
        $this->userManager
            ->shouldReceive('getAdministeredCourseAndSchoolIds')
            ->with($this->userId)
            ->andReturn(['courseIds' => []]);
        $this->userManager
            ->shouldReceive('getDirectedSchoolIds')
            ->with($this->userId)
            ->andReturn([]);
        $this->userManager
            ->shouldReceive('getAdministeredSchoolIds')
            ->with($this->userId)
            ->andReturn([]);
        $this->userManager
            ->shouldReceive('getInstructorGroupIds')
            ->with($this->userId)
            ->andReturn($instructorGroupIds);
        $this->assertTrue($this->sessionUser->performsNonLearnerFunction());
    }

    /**
     * @covers SessionUser::performsNonLearnerFunction()
     */
    public function testPerformsNonLearnerFunctionIfUserIsTeachingInCourses()
    {
        $courseIds = [2, 3];
        $this->userManager
            ->shouldReceive('getDirectedCourseAndSchoolIds')
            ->with($this->userId)
            ->andReturn(['courseIds' => []]);
        $this->userManager
            ->shouldReceive('getAdministeredCourseAndSchoolIds')
            ->with($this->userId)
            ->andReturn(['courseIds' => []]);
        $this->userManager
            ->shouldReceive('getDirectedSchoolIds')
            ->with($this->userId)
            ->andReturn([]);
        $this->userManager
            ->shouldReceive('getAdministeredSchoolIds')
            ->with($this->userId)
            ->andReturn([]);
        $this->userManager
            ->shouldReceive('getInstructorGroupIds')
            ->with($this->userId)
            ->andReturn([]);
        $this->userManager
            ->shouldReceive('getInstructedOfferingIlmSessionCourseAndSchoolIds')
            ->with($this->userId)
            ->andReturn(['courseIds' => $courseIds]);
        $this->assertTrue($this->sessionUser->performsNonLearnerFunction());
    }

    /**
     * @covers SessionUser::performsNonLearnerFunction()
     */
    public function testPerformsNonLearnerFunctionIfUserIsSessionAdministrator()
    {
        $sessionIds = [2, 3];
        $this->userManager
            ->shouldReceive('getDirectedCourseAndSchoolIds')
            ->with($this->userId)
            ->andReturn(['courseIds' => []]);
        $this->userManager
            ->shouldReceive('getAdministeredCourseAndSchoolIds')
            ->with($this->userId)
            ->andReturn(['courseIds' => []]);
        $this->userManager
            ->shouldReceive('getDirectedSchoolIds')
            ->with($this->userId)
            ->andReturn([]);
        $this->userManager
            ->shouldReceive('getAdministeredSchoolIds')
            ->with($this->userId)
            ->andReturn([]);
        $this->userManager
            ->shouldReceive('getInstructorGroupIds')
            ->with($this->userId)
            ->andReturn([]);
        $this->userManager
            ->shouldReceive('getInstructedOfferingIlmSessionCourseAndSchoolIds')
            ->with($this->userId)
            ->andReturn(['courseIds' => []]);
        $this->userManager
            ->shouldReceive('getAdministeredSessionCourseAndSchoolIds')
            ->with($this->userId)
            ->andReturn(['sessionIds' => $sessionIds]);
        $this->assertTrue($this->sessionUser->performsNonLearnerFunction());
    }

    /**
     * @covers SessionUser::performsNonLearnerFunction()
     */
    public function testPerformsNonLearnerFunctionIfUserIsInstructingInSessions()
    {
        $sessionIds = [2, 3];
        $this->userManager
            ->shouldReceive('getDirectedCourseAndSchoolIds')
            ->with($this->userId)
            ->andReturn(['courseIds' => []]);
        $this->userManager
            ->shouldReceive('getAdministeredCourseAndSchoolIds')
            ->with($this->userId)
            ->andReturn(['courseIds' => []]);
        $this->userManager
            ->shouldReceive('getDirectedSchoolIds')
            ->with($this->userId)
            ->andReturn([]);
        $this->userManager
            ->shouldReceive('getAdministeredSchoolIds')
            ->with($this->userId)
            ->andReturn([]);
        $this->userManager
            ->shouldReceive('getInstructorGroupIds')
            ->with($this->userId)
            ->andReturn([]);
        $this->userManager
            ->shouldReceive('getInstructedOfferingIlmSessionCourseAndSchoolIds')
            ->with($this->userId)
            ->andReturn(['courseIds' => [], 'sessionIds' => $sessionIds]);
        $this->userManager
            ->shouldReceive('getAdministeredSessionCourseAndSchoolIds')
            ->with($this->userId)
            ->andReturn(['sessionIds' => []]);
        $this->assertTrue($this->sessionUser->performsNonLearnerFunction());
    }

    /**
     * @covers SessionUser::performsNonLearnerFunction()
     */
    public function testPerformsNonLearnerFunctionIfUserIsProgramDirector()
    {
        $programIds = [2, 3];
        $this->userManager
            ->shouldReceive('getDirectedCourseAndSchoolIds')
            ->with($this->userId)
            ->andReturn(['courseIds' => []]);
        $this->userManager
            ->shouldReceive('getAdministeredCourseAndSchoolIds')
            ->with($this->userId)
            ->andReturn(['courseIds' => []]);
        $this->userManager
            ->shouldReceive('getDirectedSchoolIds')
            ->with($this->userId)
            ->andReturn([]);
        $this->userManager
            ->shouldReceive('getAdministeredSchoolIds')
            ->with($this->userId)
            ->andReturn([]);
        $this->userManager
            ->shouldReceive('getInstructorGroupIds')
            ->with($this->userId)
            ->andReturn([]);
        $this->userManager
            ->shouldReceive('getInstructedOfferingIlmSessionCourseAndSchoolIds')
            ->with($this->userId)
            ->andReturn(['courseIds' => [], 'sessionIds' => []]);
        $this->userManager
            ->shouldReceive('getAdministeredSessionCourseAndSchoolIds')
            ->with($this->userId)
            ->andReturn(['sessionIds' => []]);
        $this->userManager
            ->shouldReceive('getDirectedProgramAndSchoolIds')
            ->with($this->userId)
            ->andReturn(['programIds' => $programIds]);
        $this->assertTrue($this->sessionUser->performsNonLearnerFunction());
    }

    /**
     * @covers SessionUser::performsNonLearnerFunction()
     */
    public function testPerformsNonLearnerFunctionIfUserIsProgramYearDirector()
    {
        $programYearIds = [2, 3];
        $this->userManager
            ->shouldReceive('getDirectedCourseAndSchoolIds')
            ->with($this->userId)
            ->andReturn(['courseIds' => []]);
        $this->userManager
            ->shouldReceive('getAdministeredCourseAndSchoolIds')
            ->with($this->userId)
            ->andReturn(['courseIds' => []]);
        $this->userManager
            ->shouldReceive('getDirectedSchoolIds')
            ->with($this->userId)
            ->andReturn([]);
        $this->userManager
            ->shouldReceive('getAdministeredSchoolIds')
            ->with($this->userId)
            ->andReturn([]);
        $this->userManager
            ->shouldReceive('getInstructorGroupIds')
            ->with($this->userId)
            ->andReturn([]);
        $this->userManager
            ->shouldReceive('getInstructedOfferingIlmSessionCourseAndSchoolIds')
            ->with($this->userId)
            ->andReturn(['courseIds' => [], 'sessionIds' => []]);
        $this->userManager
            ->shouldReceive('getAdministeredSessionCourseAndSchoolIds')
            ->with($this->userId)
            ->andReturn(['sessionIds' => []]);
        $this->userManager
            ->shouldReceive('getDirectedProgramAndSchoolIds')
            ->with($this->userId)
            ->andReturn(['programIds' => []]);
        $this->userManager
            ->shouldReceive('getDirectedProgramYearProgramAndSchoolIds')
            ->with($this->userId)
            ->andReturn(['programYearIds' => $programYearIds]);
        $this->assertTrue($this->sessionUser->performsNonLearnerFunction());
    }

    /**
     * @covers SessionUser::performsNonLearnerFunction()
     */
    public function testPerformsNonLearnerFunctionIfUserIsCurriculumInventoryReportAdministrator()
    {
        $reportIds = [2, 3];
        $this->userManager
            ->shouldReceive('getDirectedCourseAndSchoolIds')
            ->with($this->userId)
            ->andReturn(['courseIds' => []]);
        $this->userManager
            ->shouldReceive('getAdministeredCourseAndSchoolIds')
            ->with($this->userId)
            ->andReturn(['courseIds' => []]);
        $this->userManager
            ->shouldReceive('getDirectedSchoolIds')
            ->with($this->userId)
            ->andReturn([]);
        $this->userManager
            ->shouldReceive('getAdministeredSchoolIds')
            ->with($this->userId)
            ->andReturn([]);
        $this->userManager
            ->shouldReceive('getInstructorGroupIds')
            ->with($this->userId)
            ->andReturn([]);
        $this->userManager
            ->shouldReceive('getInstructedOfferingIlmSessionCourseAndSchoolIds')
            ->with($this->userId)
            ->andReturn(['courseIds' => [], 'sessionIds' => []]);
        $this->userManager
            ->shouldReceive('getAdministeredSessionCourseAndSchoolIds')
            ->with($this->userId)
            ->andReturn(['sessionIds' => []]);
        $this->userManager
            ->shouldReceive('getDirectedProgramAndSchoolIds')
            ->with($this->userId)
            ->andReturn(['programIds' => []]);
        $this->userManager
            ->shouldReceive('getDirectedProgramYearProgramAndSchoolIds')
            ->with($this->userId)
            ->andReturn(['programYearIds' => []]);
        $this->userManager
            ->shouldReceive('getAdministeredCurriculumInventoryReportAndSchoolIds')
            ->with($this->userId)
            ->andReturn(['reportIds' => $reportIds]);
        $this->assertTrue($this->sessionUser->performsNonLearnerFunction());
    }

    /**
     * @covers SessionUser::performsNonLearnerFunction()
     */
    public function testDoesNotPerformNonLearnerFunction()
    {
        $reportIds = [2, 3];
        $this->userManager
            ->shouldReceive('getDirectedCourseAndSchoolIds')
            ->with($this->userId)
            ->andReturn(['courseIds' => []]);
        $this->userManager
            ->shouldReceive('getAdministeredCourseAndSchoolIds')
            ->with($this->userId)
            ->andReturn(['courseIds' => []]);
        $this->userManager
            ->shouldReceive('getDirectedSchoolIds')
            ->with($this->userId)
            ->andReturn([]);
        $this->userManager
            ->shouldReceive('getAdministeredSchoolIds')
            ->with($this->userId)
            ->andReturn([]);
        $this->userManager
            ->shouldReceive('getInstructorGroupIds')
            ->with($this->userId)
            ->andReturn([]);
        $this->userManager
            ->shouldReceive('getInstructedOfferingIlmSessionCourseAndSchoolIds')
            ->with($this->userId)
            ->andReturn(['courseIds' => [], 'sessionIds' => []]);
        $this->userManager
            ->shouldReceive('getAdministeredSessionCourseAndSchoolIds')
            ->with($this->userId)
            ->andReturn(['sessionIds' => []]);
        $this->userManager
            ->shouldReceive('getDirectedProgramAndSchoolIds')
            ->with($this->userId)
            ->andReturn(['programIds' => []]);
        $this->userManager
            ->shouldReceive('getDirectedProgramYearProgramAndSchoolIds')
            ->with($this->userId)
            ->andReturn(['programYearIds' => []]);
        $this->userManager
            ->shouldReceive('getAdministeredCurriculumInventoryReportAndSchoolIds')
            ->with($this->userId)
            ->andReturn(['reportIds' => []]);
        $this->assertFalse($this->sessionUser->performsNonLearnerFunction());
    }

    /**
     * @covers SessionUser::getDirectedCourseIds
     */
    public function testGetDirectedCourseIds()
    {
        $courseIds = [2, 3];
        $this->userManager
            ->shouldReceive('getDirectedCourseAndSchoolIds')
            ->with($this->userId)
            ->andReturn(['courseIds' => $courseIds]);
        $this->assertEquals($courseIds, $this->sessionUser->getDirectedCourseIds());
    }

    /**
     * @covers SessionUser::getAdministeredCourseIds
     */
    public function testGetAdministeredCourseIds()
    {
        $courseIds = [2, 3];
        $this->userManager
            ->shouldReceive('getAdministeredCourseAndSchoolIds')
            ->with($this->userId)
            ->andReturn(['courseIds' => $courseIds]);
        $this->assertEquals($courseIds, $this->sessionUser->getAdministeredCourseIds());
    }

    /**
     * @covers SessionUser::getDirectedSchoolIds
     */
    public function testGetDirectedSchoolIds()
    {
        $schoolIds = [2, 3];
        $this->userManager
            ->shouldReceive('getDirectedSchoolIds')
            ->with($this->userId)
            ->andReturn($schoolIds);
        $this->assertEquals($schoolIds, $this->sessionUser->getDirectedSchoolIds());
    }

    /**
     * @covers SessionUser::getAdministeredSchoolIds
     */
    public function testGetAdministeredSchoolIds()
    {
        $schoolIds = [2, 3];
        $this->userManager
            ->shouldReceive('getAdministeredSchoolIds')
            ->with($this->userId)
            ->andReturn($schoolIds);
        $this->assertEquals($schoolIds, $this->sessionUser->getAdministeredSchoolIds());
    }

    /**
     * @covers SessionUser::getDirectedCourseSchoolIds
     */
    public function testGetDirectedCourseSchoolIds()
    {
        $schoolIds = [2, 3];
        $this->userManager
            ->shouldReceive('getDirectedCourseAndSchoolIds')
            ->with($this->userId)
            ->andReturn(['schoolIds' => $schoolIds]);
        $this->assertEquals($schoolIds, $this->sessionUser->getDirectedCourseSchoolIds());
    }

    /**
     * @covers SessionUser::getAdministeredCourseSchoolIds
     */
    public function testGetAdministeredCourseSchoolIds()
    {
        $schoolIds = [2, 3];
        $this->userManager
            ->shouldReceive('getAdministeredCourseAndSchoolIds')
            ->with($this->userId)
            ->andReturn(['schoolIds' => $schoolIds]);
        $this->assertEquals($schoolIds, $this->sessionUser->getAdministeredCourseSchoolIds());
    }

    /**
     * @covers SessionUser::getAdministeredSessionSchoolIds
     */
    public function testGetAdministeredSessionSchoolIds()
    {
        $schoolIds = [2, 3];
        $this->userManager
            ->shouldReceive('getAdministeredSessionCourseAndSchoolIds')
            ->with($this->userId)
            ->andReturn(['schoolIds' => $schoolIds]);
        $this->assertEquals($schoolIds, $this->sessionUser->getAdministeredSessionSchoolIds());
    }

    /**
     * @covers SessionUser::getAdministeredSessionCourseIds
     */
    public function testGetAdministeredSessionCourseIds()
    {
        $courseIds = [2, 3];
        $this->userManager
            ->shouldReceive('getAdministeredSessionCourseAndSchoolIds')
            ->with($this->userId)
            ->andReturn(['courseIds' => $courseIds]);
        $this->assertEquals($courseIds, $this->sessionUser->getAdministeredSessionCourseIds());
    }

    /**
     * @covers SessionUser::getTaughtCourseIds
     */
    public function testGetTaughtCourseIds()
    {
        $courseIds = [2, 3];
        $this->userManager
            ->shouldReceive('getInstructedOfferingIlmSessionCourseAndSchoolIds')
            ->with($this->userId)
            ->andReturn(['courseIds' => $courseIds]);
        $this->assertEquals($courseIds, $this->sessionUser->getTaughtCourseIds());
    }

    /**
     * @covers SessionUser::getAdministeredSessionIds
     */
    public function testGetAdministeredSessionIds()
    {
        $sessionIds = [2, 3];
        $this->userManager
            ->shouldReceive('getAdministeredSessionCourseAndSchoolIds')
            ->with($this->userId)
            ->andReturn(['sessionIds' => $sessionIds]);
        $this->assertEquals($sessionIds, $this->sessionUser->getAdministeredSessionIds());
    }

    /**
     * @covers SessionUser::getInstructedSessionIds
     */
    public function testGetInstructedSessionIds()
    {
        $sessionIds = [2, 3];
        $this->userManager
            ->shouldReceive('getInstructedOfferingIlmSessionCourseAndSchoolIds')
            ->with($this->userId)
            ->andReturn(['sessionIds' => $sessionIds]);
        $this->assertEquals($sessionIds, $this->sessionUser->getInstructedSessionIds());
    }

    /**
     * @covers SessionUser::getInstructedOfferingIds
     */
    public function testGetInstructedOfferingIds()
    {
        $userId = 1;
        $offeringIds = [1, 2, 3];
        $this->userManager
            ->shouldReceive('getInstructedOfferingIlmSessionCourseAndSchoolIds')
            ->with($userId)
            ->andReturn(['offeringIds' => $offeringIds]);
        $this->assertEquals($offeringIds, $this->sessionUser->getInstructedOfferingIds());
    }

    /**
     * @covers SessionUser::getInstructedIlmIds
     */
    public function testGetInstructedIlmIds()
    {
        $userId = 1;
        $ilmIds = [1, 2, 3];
        $this->userManager
            ->shouldReceive('getInstructedOfferingIlmSessionCourseAndSchoolIds')
            ->with($userId)
            ->andReturn(['ilmIds' => $ilmIds]);
        $this->assertEquals($ilmIds, $this->sessionUser->getInstructedIlmIds());
    }

    /**
     * @covers SessionUser::getTaughtCourseSchoolIds
     */
    public function testGetTaughtCourseSchoolIds()
    {
        $schoolIds = [2, 3];
        $this->userManager
            ->shouldReceive('getInstructedOfferingIlmSessionCourseAndSchoolIds')
            ->with($this->userId)
            ->andReturn(['schoolIds' => $schoolIds]);
        $this->assertEquals($schoolIds, $this->sessionUser->getTaughtCourseSchoolIds());
    }

    /**
     * @covers SessionUser::getDirectedProgramIds
     */
    public function testGetDirectedProgramIds()
    {
        $programIds = [2, 3];
        $this->userManager
            ->shouldReceive('getDirectedProgramAndSchoolIds')
            ->with($this->userId)
            ->andReturn(['programIds' => $programIds]);
        $this->assertEquals($programIds, $this->sessionUser->getDirectedProgramIds());
    }

    /**
     * @covers SessionUser::getDirectedProgramYearIds
     */
    public function testGetDirectedProgramYearIds()
    {
        $programYearIds = [2, 3];
        $this->userManager
            ->shouldReceive('getDirectedProgramYearProgramAndSchoolIds')
            ->with($this->userId)
            ->andReturn(['programYearIds' => $programYearIds]);
        $this->assertEquals($programYearIds, $this->sessionUser->getDirectedProgramYearIds());
    }

    /**
     * @covers SessionUser::getDirectedProgramYearProgramIds
     */
    public function testGetDirectedProgramYearProgramIds()
    {
        $programIds = [2, 3];
        $this->userManager
            ->shouldReceive('getDirectedProgramYearProgramAndSchoolIds')
            ->with($this->userId)
            ->andReturn(['programIds' => $programIds]);
        $this->assertEquals($programIds, $this->sessionUser->getDirectedProgramYearProgramIds());
    }

    /**
     * @covers SessionUser::getAdministeredCurriculumInventoryReportIds
     */
    public function testGetAdministeredCurriculumInventoryReportIds()
    {
        $reportIds = [2, 3];
        $this->userManager
            ->shouldReceive('getAdministeredCurriculumInventoryReportAndSchoolIds')
            ->with($this->userId)
            ->andReturn(['reportIds' => $reportIds]);
        $this->assertEquals($reportIds, $this->sessionUser->getAdministeredCurriculumInventoryReportIds());
    }

    /**
     * @covers SessionUser::getAdministeredCurriculumInventoryReportSchoolIds
     */
    public function testGetAdministeredCurriculumInventoryReportSchoolIds()
    {
        $schoolIds = [2, 3];
        $this->userManager
            ->shouldReceive('getAdministeredCurriculumInventoryReportAndSchoolIds')
            ->with($this->userId)
            ->andReturn(['schoolIds' => $schoolIds]);
        $this->assertEquals($schoolIds, $this->sessionUser->getAdministeredCurriculumInventoryReportSchoolIds());
    }

    protected function createMockUser(
        int $userId,
        SchoolInterface $school,
        bool $isRoot = false,
        bool $enabled = true,
        AuthenticationInterface $authentication = null
    ) : UserInterface {
        $iliosUser = m::mock(UserInterface::class);
        $iliosUser->shouldReceive('getId')->andReturn($userId);
        $iliosUser->shouldReceive('getSchool')->andReturn($school);
        $iliosUser->shouldReceive('isRoot')->andReturn($isRoot);
        $iliosUser->shouldReceive('isEnabled')->andReturn($enabled);
        $iliosUser->shouldReceive('getAuthentication')->andReturn($authentication);
        return $iliosUser;
    }
}
