<?php

declare(strict_types=1);

namespace App\Tests\Classes;

use App\Classes\SessionUser;
use App\Classes\SessionUserInterface;
use App\Classes\UserRoles;
use App\Entity\School;
use App\Entity\SchoolInterface;
use App\Entity\UserInterface;
use App\Repository\UserRepository;
use App\Service\AuthenticationInterface;
use App\Tests\TestCase;
use Mockery as m;
use Symfony\Component\Security\Core\User\UserInterface as SymfonyUserInterface;

/**
 * Class SessionUserTest
 * @covers \App\Classes\SessionUser
 */
class SessionUserTest extends TestCase
{
    protected m\MockInterface $iliosUser;

    protected m\MockInterface $userRepository;

    protected SessionUserInterface $sessionUser;

    protected int $userId;

    protected m\MockInterface $school;

    public function setUp(): void
    {
        parent::setUp();
        $this->userId = 1;
        $this->userRepository = m::mock(UserRepository::class);

        $this->school = m::mock(School::class);
        $this->school->shouldReceive('getId')->andReturn(1);

        $this->iliosUser = $this->createMockUser($this->userId, $this->school);

        $this->sessionUser = new SessionUser($this->iliosUser, $this->userRepository);
    }

    public function tearDown(): void
    {
        parent::tearDown();
        unset($this->sessionUser);
        unset($this->iliosUser);
        unset($this->userRepository);
        unset($this->school);
        unset($this->userId);
    }

    public function testIsDirectingCourse()
    {
        $directedCourseAndSchoolIds = ['courseIds' => [1, 2, 3]];
        $this->userRepository
            ->shouldReceive('getDirectedCourseAndSchoolIds')
            ->andReturn($directedCourseAndSchoolIds);
        $this->assertTrue($this->sessionUser->isDirectingCourse(1));
    }

    public function testIsNotDirectingCourse()
    {
        $directedCourseAndSchoolIds = ['courseIds' => [2, 3]];
        $this->userRepository
            ->shouldReceive('getDirectedCourseAndSchoolIds')
            ->andReturn($directedCourseAndSchoolIds);
        $this->assertFalse($this->sessionUser->isDirectingCourse(1));
    }

    public function testIsDirectingProgramLinkedToCourse()
    {
        $linkedCourseIds = ['courseIds' => [1, 2, 3]];
        $this->userRepository
            ->shouldReceive('getCoursesCohortsProgramYearAndProgramIdsLinkedToProgramsDirectedByUser')
            ->andReturn($linkedCourseIds);
        $this->assertTrue($this->sessionUser->isDirectingProgramLinkedToCourse(1));
    }

    public function testIsNotDirectingProgramLinkedToCourse()
    {
        $linkedCourseIds = ['courseIds' => [2, 3]];
        $this->userRepository
            ->shouldReceive('getCoursesCohortsProgramYearAndProgramIdsLinkedToProgramsDirectedByUser')
            ->andReturn($linkedCourseIds);
        $this->assertFalse($this->sessionUser->isDirectingProgramLinkedToCourse(1));
    }

    public function testIsAdministeringCourse()
    {
        $administeredCourseAndIds = ['courseIds' => [1, 2, 3]];
        $this->userRepository
            ->shouldReceive('getAdministeredCourseAndSchoolIds')
            ->andReturn($administeredCourseAndIds);
        $this->assertTrue($this->sessionUser->isAdministeringCourse(1));
    }

    public function testIsNotAdministeringCourse()
    {
        $administeredCourseAndIds = ['courseIds' => [2, 3]];
        $this->userRepository
            ->shouldReceive('getAdministeredCourseAndSchoolIds')
            ->andReturn($administeredCourseAndIds);
        $this->assertFalse($this->sessionUser->isAdministeringCourse(1));
    }

    public function testIsDirectingSchool()
    {
        $directedSchoolIds = [1, 2, 3];
        $this->userRepository->shouldReceive('getDirectedSchoolIds')->andReturn($directedSchoolIds);
        $this->assertTrue($this->sessionUser->isDirectingSchool(1));
    }

    public function testIsNotDirectingSchool()
    {
        $directedSchoolIds = [2, 3];
        $this->userRepository->shouldReceive('getDirectedSchoolIds')->andReturn($directedSchoolIds);
        $this->assertFalse($this->sessionUser->isDirectingSchool(1));
    }

    public function testIsAdministeringSchool()
    {
        $administeredSchoolIds = [1, 2, 3];
        $this->userRepository->shouldReceive('getAdministeredSchoolIds')->andReturn($administeredSchoolIds);
        $this->assertTrue($this->sessionUser->isAdministeringSchool(1));
    }

    public function testIsNotAdministeringSchool()
    {
        $administeredSchoolIds = [2, 3];
        $this->userRepository->shouldReceive('getAdministeredSchoolIds')->andReturn($administeredSchoolIds);
        $this->assertFalse($this->sessionUser->isAdministeringSchool(1));
    }

    public function testIsDirectingCourseInSchool()
    {
        $directedCourseAndSchoolIds = ['schoolIds' => [1, 2, 3]];
        $this->userRepository
            ->shouldReceive('getDirectedCourseAndSchoolIds')
            ->andReturn($directedCourseAndSchoolIds);
        $this->assertTrue($this->sessionUser->isDirectingCourseInSchool(1));
    }

    public function testIsNotDirectingCourseInSchool()
    {
        $directedCourseAndSchoolIds = ['schoolIds' => [2, 3]];
        $this->userRepository
            ->shouldReceive('getDirectedCourseAndSchoolIds')
            ->andReturn($directedCourseAndSchoolIds);
        $this->assertFalse($this->sessionUser->isDirectingCourseInSchool(1));
    }

    public function testIsAdministeringCourseInSchool()
    {
        $administeredCourseAndSchoolIds = ['schoolIds' => [1, 2, 3]];
        $this->userRepository
            ->shouldReceive('getAdministeredCourseAndSchoolIds')
            ->andReturn($administeredCourseAndSchoolIds);
        $this->assertTrue($this->sessionUser->isAdministeringCourseInSchool(1));
    }

    public function testIsNotAdministeringCourseInSchool()
    {
        $administeredCourseAndSchoolIds = ['schoolIds' => [2, 3]];
        $this->userRepository
            ->shouldReceive('getAdministeredCourseAndSchoolIds')
            ->andReturn($administeredCourseAndSchoolIds);
        $this->assertFalse($this->sessionUser->isAdministeringCourseInSchool(1));
    }

    public function testIsAdministeringSessionInSchool()
    {
        $administeredSessionCourseAndSchoolIds = ['schoolIds' => [1, 2, 3]];
        $this->userRepository
            ->shouldReceive('getAdministeredSessionCourseAndSchoolIds')
            ->andReturn($administeredSessionCourseAndSchoolIds);
        $this->assertTrue($this->sessionUser->isAdministeringSessionInSchool(1));
    }

    public function testIsNotAdministeringSessionInSchool()
    {
        $administeredSessionCourseAndSchoolIds = ['schoolIds' => [2, 3]];
        $this->userRepository
            ->shouldReceive('getAdministeredSessionCourseAndSchoolIds')
            ->andReturn($administeredSessionCourseAndSchoolIds);
        $this->assertFalse($this->sessionUser->isAdministeringSessionInSchool(1));
    }

    public function testIsTeachingCourseInSchool()
    {
        $taughtSchoolIds = ['schoolIds' => [1, 2, 3]];
        $this->userRepository
            ->shouldReceive('getInstructedOfferingIlmSessionCourseAndSchoolIds')
            ->andReturn($taughtSchoolIds);
        $this->assertTrue($this->sessionUser->isTeachingCourseInSchool(1));
    }

    public function testIsNotTeachingCourseInSchool()
    {
        $taughtSchoolIds = ['schoolIds' => [2, 3]];
        $this->userRepository
            ->shouldReceive('getInstructedOfferingIlmSessionCourseAndSchoolIds')
            ->andReturn($taughtSchoolIds);
        $this->assertFalse($this->sessionUser->isTeachingCourseInSchool(1));
    }

    public function testIsTeachingCourse()
    {
        $taughtCourseIds = ['courseIds' => [1, 2, 3]];
        $this->userRepository
            ->shouldReceive('getInstructedOfferingIlmSessionCourseAndSchoolIds')
            ->andReturn($taughtCourseIds);
        $this->assertTrue($this->sessionUser->isTeachingCourse(1));
    }

    public function testIsNotTeachingCourse()
    {
        $taughtCourseIds = ['courseIds' => [2, 3]];
        $this->userRepository
            ->shouldReceive('getInstructedOfferingIlmSessionCourseAndSchoolIds')
            ->andReturn($taughtCourseIds);
        $this->assertFalse($this->sessionUser->isTeachingCourse(1));
    }

    public function testIsAdministeringSessionInCourse()
    {
        $administeredCourseIds = ['courseIds' => [1, 2, 3]];
        $this->userRepository
            ->shouldReceive('getAdministeredSessionCourseAndSchoolIds')
            ->andReturn($administeredCourseIds);
        $this->assertTrue($this->sessionUser->isAdministeringSessionInCourse(1));
    }

    public function testIsNotAdministeringSessionInCourse()
    {
        $administeredCourseIds = ['courseIds' => [2, 3]];
        $this->userRepository
            ->shouldReceive('getAdministeredSessionCourseAndSchoolIds')
            ->andReturn($administeredCourseIds);
        $this->assertFalse($this->sessionUser->isAdministeringSessionInCourse(1));
    }

    public function testIsAdministeringSession()
    {
        $administeredSessionIds = ['sessionIds' => [1, 2, 3]];
        $this->userRepository
            ->shouldReceive('getAdministeredSessionCourseAndSchoolIds')
            ->andReturn($administeredSessionIds);
        $this->assertTrue($this->sessionUser->isAdministeringSession(1));
    }

    public function testIsNotAdministeringSession()
    {
        $administeredSessionIds = ['sessionIds' => [2, 3]];
        $this->userRepository
            ->shouldReceive('getAdministeredSessionCourseAndSchoolIds')
            ->andReturn($administeredSessionIds);
        $this->assertFalse($this->sessionUser->isAdministeringSession(1));
    }

    public function testIsTeachingSession()
    {
        $taughtSessionIds = ['sessionIds' => [1, 2, 3]];
        $this->userRepository
            ->shouldReceive('getInstructedOfferingIlmSessionCourseAndSchoolIds')
            ->andReturn($taughtSessionIds);
        $this->assertTrue($this->sessionUser->isTeachingSession(1));
    }

    public function testIsNotTeachingSession()
    {
        $taughtSessionIds = ['sessionIds' => [2, 3]];
        $this->userRepository
            ->shouldReceive('getInstructedOfferingIlmSessionCourseAndSchoolIds')
            ->andReturn($taughtSessionIds);
        $this->assertFalse($this->sessionUser->isTeachingSession(1));
    }

    public function testIsInstructingOffering()
    {
        $taughtSessionIds = ['offeringIds' => [1, 2, 3]];
        $this->userRepository
            ->shouldReceive('getInstructedOfferingIlmSessionCourseAndSchoolIds')
            ->andReturn($taughtSessionIds);
        $this->assertTrue($this->sessionUser->isInstructingOffering(1));
    }

    public function testIsNotInstructingOffering()
    {
        $taughtSessionIds = ['offeringIds' => [2, 3]];
        $this->userRepository
            ->shouldReceive('getInstructedOfferingIlmSessionCourseAndSchoolIds')
            ->andReturn($taughtSessionIds);
        $this->assertFalse($this->sessionUser->isInstructingOffering(1));
    }

    public function testIsInstructingIlm()
    {
        $taughtSessionIds = ['ilmIds' => [1, 2, 3]];
        $this->userRepository
            ->shouldReceive('getInstructedOfferingIlmSessionCourseAndSchoolIds')
            ->andReturn($taughtSessionIds);
        $this->assertTrue($this->sessionUser->isInstructingIlm(1));
    }

    public function testIsNotInstructingIlm()
    {
        $taughtSessionIds = ['ilmIds' => [2, 3]];
        $this->userRepository
            ->shouldReceive('getInstructedOfferingIlmSessionCourseAndSchoolIds')
            ->andReturn($taughtSessionIds);
        $this->assertFalse($this->sessionUser->isInstructingIlm(1));
    }

    public function testIsDirectingProgram()
    {
        $directedProgramIds = ['programIds' => [1, 2, 3]];
        $this->userRepository
            ->shouldReceive('getDirectedProgramAndSchoolIds')
            ->andReturn($directedProgramIds);
        $this->assertTrue($this->sessionUser->isDirectingProgram(1));
    }

    public function testIsNotDirectingProgram()
    {
        $directedProgramIds = ['programIds' => [2, 3]];
        $this->userRepository
            ->shouldReceive('getDirectedProgramAndSchoolIds')
            ->andReturn($directedProgramIds);
        $this->assertFalse($this->sessionUser->isDirectingProgram(1));
    }

    public function testIsDirectingProgramYear()
    {
        $directedProgramYearIds = ['programYearIds' => [1, 2, 3]];
        $this->userRepository
            ->shouldReceive('getDirectedProgramYearProgramAndSchoolIds')
            ->andReturn($directedProgramYearIds);
        $this->assertTrue($this->sessionUser->isDirectingProgramYear(1));
    }

    public function testIsNotDirectingProgramYear()
    {
        $directedProgramYearIds = ['programYearIds' => [2, 3]];
        $this->userRepository
            ->shouldReceive('getDirectedProgramYearProgramAndSchoolIds')
            ->andReturn($directedProgramYearIds);
        $this->assertFalse($this->sessionUser->isDirectingProgramYear(1));
    }

    public function testIsDirectingProgramYearInProgram()
    {
        $directedProgramIds = ['programIds' => [1, 2, 3]];
        $this->userRepository
            ->shouldReceive('getDirectedProgramYearProgramAndSchoolIds')
            ->andReturn($directedProgramIds);
        $this->assertTrue($this->sessionUser->isDirectingProgramYearInProgram(1));
    }

    public function testIsNotDirectingProgramYearInProgram()
    {
        $directedProgramIds = ['programIds' => [2, 3]];
        $this->userRepository
            ->shouldReceive('getDirectedProgramYearProgramAndSchoolIds')
            ->andReturn($directedProgramIds);
        $this->assertFalse($this->sessionUser->isDirectingProgramYearInProgram(1));
    }

    public function testIsAdministeringCurriculumInventoryReport()
    {
        $administeredReportIds = ['reportIds' => [1, 2, 3]];
        $this->userRepository
            ->shouldReceive('getAdministeredCurriculumInventoryReportAndSchoolIds')
            ->andReturn($administeredReportIds);
        $this->assertTrue($this->sessionUser->isAdministeringCurriculumInventoryReport(1));
    }

    public function testIsNotAdministeringCurriculumInventoryReport()
    {
        $administeredReportIds = ['reportIds' => [2, 3]];
        $this->userRepository
            ->shouldReceive('getAdministeredCurriculumInventoryReportAndSchoolIds')
            ->andReturn($administeredReportIds);
        $this->assertFalse($this->sessionUser->isAdministeringCurriculumInventoryReport(1));
    }

    public function testIsAdministeringCurriculumInventoryReportInSchool()
    {
        $administeredSchoolIds = ['schoolIds' => [1, 2, 3]];
        $this->userRepository
            ->shouldReceive('getAdministeredCurriculumInventoryReportAndSchoolIds')
            ->andReturn($administeredSchoolIds);
        $this->assertTrue($this->sessionUser->isAdministeringCurriculumInventoryReportInSchool(1));
    }

    public function testIsNotAdministeringCurriculumInventoryReportInSchool()
    {
        $administeredSchoolIds = ['schoolIds' => [2, 3]];
        $this->userRepository
            ->shouldReceive('getAdministeredCurriculumInventoryReportAndSchoolIds')
            ->andReturn($administeredSchoolIds);
        $this->assertFalse($this->sessionUser->isAdministeringCurriculumInventoryReportInSchool(1));
    }

    public function testIsInLearnerGroup()
    {
        $learnerGroupIds = [1, 2, 3];
        $this->userRepository
            ->shouldReceive('getLearnerGroupIds')
            ->andReturn($learnerGroupIds);
        $this->assertTrue($this->sessionUser->isInLearnerGroup(1));
    }

    public function testIsNotInLearnerGroup()
    {
        $learnerGroupIds = [2, 3];
        $this->userRepository
            ->shouldReceive('getLearnerGroupIds')
            ->andReturn($learnerGroupIds);
        $this->assertFalse($this->sessionUser->isInLearnerGroup(1));
    }

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
        $this->userRepository
            ->shouldReceive('getDirectedSchoolIds')
            ->with($this->userId)
            ->andReturn([$schoolId]);
        $this->userRepository
            ->shouldReceive('getAdministeredSchoolIds')
            ->with($this->userId)
            ->andReturn([$schoolId]);
        $this->userRepository
            ->shouldReceive('getDirectedCourseAndSchoolIds')
            ->with($this->userId)
            ->andReturn(['schoolIds' => [$schoolId]]);
        $this->userRepository
            ->shouldReceive('getAdministeredCourseAndSchoolIds')
            ->with($this->userId)
            ->andReturn(['schoolIds' => [$schoolId]]);
        $this->userRepository
            ->shouldReceive('getAdministeredSessionCourseAndSchoolIds')
            ->with($this->userId)
            ->andReturn(['schoolIds' => [$schoolId]]);
        $this->userRepository
            ->shouldReceive('getInstructedOfferingIlmSessionCourseAndSchoolIds')
            ->with($this->userId)
            ->andReturn(['schoolIds' => [$schoolId]]);
        $this->userRepository
            ->shouldReceive('getAdministeredCurriculumInventoryReportAndSchoolIds')
            ->with($this->userId)
            ->andReturn(['schoolIds' => [$schoolId]]);
        $this->userRepository
            ->shouldReceive('getDirectedProgramAndSchoolIds')
            ->with($this->userId)
            ->andReturn(['schoolIds' => [$schoolId]]);
        $this->assertEquals($roles, $this->sessionUser->rolesInSchool($schoolId));
    }

    public function testRolesInCourse()
    {
        $courseId = 2;
        $roles = [UserRoles::COURSE_DIRECTOR,
            UserRoles::COURSE_ADMINISTRATOR,
            UserRoles::SESSION_ADMINISTRATOR,
            UserRoles::COURSE_INSTRUCTOR,
        ];
        $this->userRepository
            ->shouldReceive('getDirectedCourseAndSchoolIds')
            ->with($this->userId)
            ->andReturn(['courseIds' => [$courseId]]);
        $this->userRepository
            ->shouldReceive('getAdministeredCourseAndSchoolIds')
            ->with($this->userId)
            ->andReturn(['courseIds' => [$courseId]]);
        $this->userRepository
            ->shouldReceive('getAdministeredSessionCourseAndSchoolIds')
            ->with($this->userId)
            ->andReturn(['courseIds' => [$courseId]]);
        $this->userRepository
            ->shouldReceive('getInstructedOfferingIlmSessionCourseAndSchoolIds')
            ->with($this->userId)
            ->andReturn(['courseIds' => [$courseId]]);
        $this->assertEquals($roles, $this->sessionUser->rolesInCourse($courseId));
    }

    /**
     * @dataProvider rolesInSessionProvider
     */
    public function testRolesInSession(
        int $sessionId,
        array $administeredSessions,
        array $instructedSessions,
        array $expectedRoles
    ): void {
        $sessionId = 2;
        $this->userRepository
            ->shouldReceive('getAdministeredSessionCourseAndSchoolIds')
            ->with($this->userId)
            ->andReturn(['sessionIds' => $administeredSessions]);
        $this->userRepository
            ->shouldReceive('getInstructedOfferingIlmSessionCourseAndSchoolIds')
            ->with($this->userId)
            ->andReturn(['sessionIds' => $instructedSessions]);
        $this->assertEquals($expectedRoles, $this->sessionUser->rolesInSession($sessionId));
    }

    public function testRolesInProgram()
    {
        $programId = 2;
        $roles = [UserRoles::PROGRAM_DIRECTOR, UserRoles::PROGRAM_YEAR_DIRECTOR];
        $this->userRepository
            ->shouldReceive('getDirectedProgramAndSchoolIds')
            ->with($this->userId)
            ->andReturn(['programIds' => [$programId]]);
        $this->userRepository
            ->shouldReceive('getDirectedProgramYearProgramAndSchoolIds')
            ->with($this->userId)
            ->andReturn(['programIds' => [$programId]]);
        $this->assertEquals($roles, $this->sessionUser->rolesInProgram($programId));
    }

    public function testRolesInProgramYear()
    {
        $programYearId = 2;
        $roles = [UserRoles::PROGRAM_YEAR_DIRECTOR];
        $this->userRepository
            ->shouldReceive('getDirectedProgramYearProgramAndSchoolIds')
            ->with($this->userId)
            ->andReturn(['programYearIds' => [$programYearId]]);
        $this->assertEquals($roles, $this->sessionUser->rolesInProgramYear($programYearId));
    }

    public function testRolesInCurriculumInventoryReport()
    {
        $reportId = 2;
        $roles = [UserRoles::CURRICULUM_INVENTORY_REPORT_ADMINISTRATOR];
        $this->userRepository
            ->shouldReceive('getAdministeredCurriculumInventoryReportAndSchoolIds')
            ->with($this->userId)
            ->andReturn(['reportIds' => [$reportId]]);
        $this->assertEquals($roles, $this->sessionUser->rolesInCurriculumInventoryReport($reportId));
    }

    public function testRolesInCurriculumInventoryReportNoMatchingReport()
    {
        $reportId = 2;
        $roles = [UserRoles::CURRICULUM_INVENTORY_REPORT_ADMINISTRATOR];
        $this->userRepository
            ->shouldReceive('getAdministeredCurriculumInventoryReportAndSchoolIds')
            ->with($this->userId)
            ->andReturn(['reportIds' => []]);
        $this->assertEmpty($this->sessionUser->rolesInCurriculumInventoryReport($reportId));
    }

    public function testRolesInCurriculumInventoryReportNoMatchingRoles()
    {
        $reportId = 2;
        $this->assertEmpty($this->sessionUser->rolesInCurriculumInventoryReport($reportId, []));
        $this->assertEmpty(
            $this->sessionUser->rolesInCurriculumInventoryReport($reportId, [UserRoles::COURSE_DIRECTOR])
        );
    }

    public function testPerformsNonLearnerFunctionIfUserIsRoot()
    {
        $this->iliosUser = $this->createMockUser($this->userId, $this->school, true);
        $this->sessionUser = new SessionUser($this->iliosUser, $this->userRepository);
        $this->assertTrue($this->sessionUser->performsNonLearnerFunction());
    }

    public function testPerformsNonLearnerFunctionIfUserIsCourseDirector()
    {
        $courseIds = [2, 3];
        $this->userRepository
            ->shouldReceive('getDirectedCourseAndSchoolIds')
            ->with($this->userId)
            ->andReturn(['courseIds' => $courseIds]);
        $this->assertTrue($this->sessionUser->performsNonLearnerFunction());
    }

    public function testPerformsNonLearnerFunctionIfUserIsCourseAdministrator()
    {
        $courseIds = [2, 3];
        $this->userRepository
            ->shouldReceive('getDirectedCourseAndSchoolIds')
            ->with($this->userId)
            ->andReturn(['courseIds' => []]);
        $this->userRepository
            ->shouldReceive('getAdministeredCourseAndSchoolIds')
            ->with($this->userId)
            ->andReturn(['courseIds' => $courseIds]);
        $this->assertTrue($this->sessionUser->performsNonLearnerFunction());
    }

    public function testPerformsNonLearnerFunctionIfUserIsSchoolDirector()
    {
        $schoolIds = [2, 3];
        $this->userRepository
            ->shouldReceive('getDirectedCourseAndSchoolIds')
            ->with($this->userId)
            ->andReturn(['courseIds' => []]);
        $this->userRepository
            ->shouldReceive('getAdministeredCourseAndSchoolIds')
            ->with($this->userId)
            ->andReturn(['courseIds' => []]);
        $this->userRepository
            ->shouldReceive('getDirectedSchoolIds')
            ->with($this->userId)
            ->andReturn($schoolIds);
        $this->assertTrue($this->sessionUser->performsNonLearnerFunction());
    }

    public function testPerformsNonLearnerFunctionIfUserIsSchoolAdministrator()
    {
        $schoolIds = [2, 3];
        $this->userRepository
            ->shouldReceive('getDirectedCourseAndSchoolIds')
            ->with($this->userId)
            ->andReturn(['courseIds' => []]);
        $this->userRepository
            ->shouldReceive('getAdministeredCourseAndSchoolIds')
            ->with($this->userId)
            ->andReturn(['courseIds' => []]);
        $this->userRepository
            ->shouldReceive('getDirectedSchoolIds')
            ->with($this->userId)
            ->andReturn([]);
        $this->userRepository
            ->shouldReceive('getAdministeredSchoolIds')
            ->with($this->userId)
            ->andReturn($schoolIds);
        $this->assertTrue($this->sessionUser->performsNonLearnerFunction());
    }

    public function testPerformsNonLearnerFunctionIfUserIsInInstructorGroups()
    {
        $instructorGroupIds = [2, 3];
        $this->userRepository
            ->shouldReceive('getDirectedCourseAndSchoolIds')
            ->with($this->userId)
            ->andReturn(['courseIds' => []]);
        $this->userRepository
            ->shouldReceive('getAdministeredCourseAndSchoolIds')
            ->with($this->userId)
            ->andReturn(['courseIds' => []]);
        $this->userRepository
            ->shouldReceive('getDirectedSchoolIds')
            ->with($this->userId)
            ->andReturn([]);
        $this->userRepository
            ->shouldReceive('getAdministeredSchoolIds')
            ->with($this->userId)
            ->andReturn([]);
        $this->userRepository
            ->shouldReceive('getInstructorGroupIds')
            ->with($this->userId)
            ->andReturn($instructorGroupIds);
        $this->assertTrue($this->sessionUser->performsNonLearnerFunction());
    }

    public function testPerformsNonLearnerFunctionIfUserIsTeachingInCourses()
    {
        $courseIds = [2, 3];
        $this->userRepository
            ->shouldReceive('getDirectedCourseAndSchoolIds')
            ->with($this->userId)
            ->andReturn(['courseIds' => []]);
        $this->userRepository
            ->shouldReceive('getAdministeredCourseAndSchoolIds')
            ->with($this->userId)
            ->andReturn(['courseIds' => []]);
        $this->userRepository
            ->shouldReceive('getDirectedSchoolIds')
            ->with($this->userId)
            ->andReturn([]);
        $this->userRepository
            ->shouldReceive('getAdministeredSchoolIds')
            ->with($this->userId)
            ->andReturn([]);
        $this->userRepository
            ->shouldReceive('getInstructorGroupIds')
            ->with($this->userId)
            ->andReturn([]);
        $this->userRepository
            ->shouldReceive('getInstructedOfferingIlmSessionCourseAndSchoolIds')
            ->with($this->userId)
            ->andReturn(['courseIds' => $courseIds]);
        $this->assertTrue($this->sessionUser->performsNonLearnerFunction());
    }

    public function testPerformsNonLearnerFunctionIfUserIsSessionAdministrator()
    {
        $sessionIds = [2, 3];
        $this->userRepository
            ->shouldReceive('getDirectedCourseAndSchoolIds')
            ->with($this->userId)
            ->andReturn(['courseIds' => []]);
        $this->userRepository
            ->shouldReceive('getAdministeredCourseAndSchoolIds')
            ->with($this->userId)
            ->andReturn(['courseIds' => []]);
        $this->userRepository
            ->shouldReceive('getDirectedSchoolIds')
            ->with($this->userId)
            ->andReturn([]);
        $this->userRepository
            ->shouldReceive('getAdministeredSchoolIds')
            ->with($this->userId)
            ->andReturn([]);
        $this->userRepository
            ->shouldReceive('getInstructorGroupIds')
            ->with($this->userId)
            ->andReturn([]);
        $this->userRepository
            ->shouldReceive('getInstructedOfferingIlmSessionCourseAndSchoolIds')
            ->with($this->userId)
            ->andReturn(['courseIds' => []]);
        $this->userRepository
            ->shouldReceive('getAdministeredSessionCourseAndSchoolIds')
            ->with($this->userId)
            ->andReturn(['sessionIds' => $sessionIds]);
        $this->assertTrue($this->sessionUser->performsNonLearnerFunction());
    }

    public function testPerformsNonLearnerFunctionIfUserIsInstructingInSessions()
    {
        $sessionIds = [2, 3];
        $this->userRepository
            ->shouldReceive('getDirectedCourseAndSchoolIds')
            ->with($this->userId)
            ->andReturn(['courseIds' => []]);
        $this->userRepository
            ->shouldReceive('getAdministeredCourseAndSchoolIds')
            ->with($this->userId)
            ->andReturn(['courseIds' => []]);
        $this->userRepository
            ->shouldReceive('getDirectedSchoolIds')
            ->with($this->userId)
            ->andReturn([]);
        $this->userRepository
            ->shouldReceive('getAdministeredSchoolIds')
            ->with($this->userId)
            ->andReturn([]);
        $this->userRepository
            ->shouldReceive('getInstructorGroupIds')
            ->with($this->userId)
            ->andReturn([]);
        $this->userRepository
            ->shouldReceive('getInstructedOfferingIlmSessionCourseAndSchoolIds')
            ->with($this->userId)
            ->andReturn(['courseIds' => [], 'sessionIds' => $sessionIds]);
        $this->userRepository
            ->shouldReceive('getAdministeredSessionCourseAndSchoolIds')
            ->with($this->userId)
            ->andReturn(['sessionIds' => []]);
        $this->assertTrue($this->sessionUser->performsNonLearnerFunction());
    }

    public function testPerformsNonLearnerFunctionIfUserIsProgramDirector()
    {
        $programIds = [2, 3];
        $this->userRepository
            ->shouldReceive('getDirectedCourseAndSchoolIds')
            ->with($this->userId)
            ->andReturn(['courseIds' => []]);
        $this->userRepository
            ->shouldReceive('getAdministeredCourseAndSchoolIds')
            ->with($this->userId)
            ->andReturn(['courseIds' => []]);
        $this->userRepository
            ->shouldReceive('getDirectedSchoolIds')
            ->with($this->userId)
            ->andReturn([]);
        $this->userRepository
            ->shouldReceive('getAdministeredSchoolIds')
            ->with($this->userId)
            ->andReturn([]);
        $this->userRepository
            ->shouldReceive('getInstructorGroupIds')
            ->with($this->userId)
            ->andReturn([]);
        $this->userRepository
            ->shouldReceive('getInstructedOfferingIlmSessionCourseAndSchoolIds')
            ->with($this->userId)
            ->andReturn(['courseIds' => [], 'sessionIds' => []]);
        $this->userRepository
            ->shouldReceive('getAdministeredSessionCourseAndSchoolIds')
            ->with($this->userId)
            ->andReturn(['sessionIds' => []]);
        $this->userRepository
            ->shouldReceive('getDirectedProgramAndSchoolIds')
            ->with($this->userId)
            ->andReturn(['programIds' => $programIds]);
        $this->assertTrue($this->sessionUser->performsNonLearnerFunction());
    }

    public function testPerformsNonLearnerFunctionIfUserIsProgramYearDirector()
    {
        $programYearIds = [2, 3];
        $this->userRepository
            ->shouldReceive('getDirectedCourseAndSchoolIds')
            ->with($this->userId)
            ->andReturn(['courseIds' => []]);
        $this->userRepository
            ->shouldReceive('getAdministeredCourseAndSchoolIds')
            ->with($this->userId)
            ->andReturn(['courseIds' => []]);
        $this->userRepository
            ->shouldReceive('getDirectedSchoolIds')
            ->with($this->userId)
            ->andReturn([]);
        $this->userRepository
            ->shouldReceive('getAdministeredSchoolIds')
            ->with($this->userId)
            ->andReturn([]);
        $this->userRepository
            ->shouldReceive('getInstructorGroupIds')
            ->with($this->userId)
            ->andReturn([]);
        $this->userRepository
            ->shouldReceive('getInstructedOfferingIlmSessionCourseAndSchoolIds')
            ->with($this->userId)
            ->andReturn(['courseIds' => [], 'sessionIds' => []]);
        $this->userRepository
            ->shouldReceive('getAdministeredSessionCourseAndSchoolIds')
            ->with($this->userId)
            ->andReturn(['sessionIds' => []]);
        $this->userRepository
            ->shouldReceive('getDirectedProgramAndSchoolIds')
            ->with($this->userId)
            ->andReturn(['programIds' => []]);
        $this->userRepository
            ->shouldReceive('getDirectedProgramYearProgramAndSchoolIds')
            ->with($this->userId)
            ->andReturn(['programYearIds' => $programYearIds]);
        $this->assertTrue($this->sessionUser->performsNonLearnerFunction());
    }

    public function testPerformsNonLearnerFunctionIfUserIsCurriculumInventoryReportAdministrator()
    {
        $reportIds = [2, 3];
        $this->userRepository
            ->shouldReceive('getDirectedCourseAndSchoolIds')
            ->with($this->userId)
            ->andReturn(['courseIds' => []]);
        $this->userRepository
            ->shouldReceive('getAdministeredCourseAndSchoolIds')
            ->with($this->userId)
            ->andReturn(['courseIds' => []]);
        $this->userRepository
            ->shouldReceive('getDirectedSchoolIds')
            ->with($this->userId)
            ->andReturn([]);
        $this->userRepository
            ->shouldReceive('getAdministeredSchoolIds')
            ->with($this->userId)
            ->andReturn([]);
        $this->userRepository
            ->shouldReceive('getInstructorGroupIds')
            ->with($this->userId)
            ->andReturn([]);
        $this->userRepository
            ->shouldReceive('getInstructedOfferingIlmSessionCourseAndSchoolIds')
            ->with($this->userId)
            ->andReturn(['courseIds' => [], 'sessionIds' => []]);
        $this->userRepository
            ->shouldReceive('getAdministeredSessionCourseAndSchoolIds')
            ->with($this->userId)
            ->andReturn(['sessionIds' => []]);
        $this->userRepository
            ->shouldReceive('getDirectedProgramAndSchoolIds')
            ->with($this->userId)
            ->andReturn(['programIds' => []]);
        $this->userRepository
            ->shouldReceive('getDirectedProgramYearProgramAndSchoolIds')
            ->with($this->userId)
            ->andReturn(['programYearIds' => []]);
        $this->userRepository
            ->shouldReceive('getAdministeredCurriculumInventoryReportAndSchoolIds')
            ->with($this->userId)
            ->andReturn(['reportIds' => $reportIds]);
        $this->assertTrue($this->sessionUser->performsNonLearnerFunction());
    }

    public function testDoesNotPerformNonLearnerFunction()
    {
        $reportIds = [2, 3];
        $this->userRepository
            ->shouldReceive('getDirectedCourseAndSchoolIds')
            ->with($this->userId)
            ->andReturn(['courseIds' => []]);
        $this->userRepository
            ->shouldReceive('getAdministeredCourseAndSchoolIds')
            ->with($this->userId)
            ->andReturn(['courseIds' => []]);
        $this->userRepository
            ->shouldReceive('getDirectedSchoolIds')
            ->with($this->userId)
            ->andReturn([]);
        $this->userRepository
            ->shouldReceive('getAdministeredSchoolIds')
            ->with($this->userId)
            ->andReturn([]);
        $this->userRepository
            ->shouldReceive('getInstructorGroupIds')
            ->with($this->userId)
            ->andReturn([]);
        $this->userRepository
            ->shouldReceive('getInstructedOfferingIlmSessionCourseAndSchoolIds')
            ->with($this->userId)
            ->andReturn(['courseIds' => [], 'sessionIds' => []]);
        $this->userRepository
            ->shouldReceive('getAdministeredSessionCourseAndSchoolIds')
            ->with($this->userId)
            ->andReturn(['sessionIds' => []]);
        $this->userRepository
            ->shouldReceive('getDirectedProgramAndSchoolIds')
            ->with($this->userId)
            ->andReturn(['programIds' => []]);
        $this->userRepository
            ->shouldReceive('getDirectedProgramYearProgramAndSchoolIds')
            ->with($this->userId)
            ->andReturn(['programYearIds' => []]);
        $this->userRepository
            ->shouldReceive('getAdministeredCurriculumInventoryReportAndSchoolIds')
            ->with($this->userId)
            ->andReturn(['reportIds' => []]);
        $this->assertFalse($this->sessionUser->performsNonLearnerFunction());
    }

    public function testGetDirectedCourseIds()
    {
        $courseIds = [2, 3];
        $this->userRepository
            ->shouldReceive('getDirectedCourseAndSchoolIds')
            ->with($this->userId)
            ->andReturn(['courseIds' => $courseIds]);
        $this->assertEquals($courseIds, $this->sessionUser->getDirectedCourseIds());
    }

    public function testGetAdministeredCourseIds()
    {
        $courseIds = [2, 3];
        $this->userRepository
            ->shouldReceive('getAdministeredCourseAndSchoolIds')
            ->with($this->userId)
            ->andReturn(['courseIds' => $courseIds]);
        $this->assertEquals($courseIds, $this->sessionUser->getAdministeredCourseIds());
    }

    public function getCourseIdsLinkedToProgramsDirectedByUser()
    {
        $courseIds = [2, 3];
        $this->userRepository
            ->shouldReceive('getCoursesCohortsProgramYearAndProgramIdsLinkedToProgramsDirectedByUser')
            ->with($this->userId)
            ->andReturn(['courseIds' => $courseIds]);
        $this->assertEquals($courseIds, $this->sessionUser->getCourseIdsLinkedToProgramsDirectedByUser());
    }

    public function testGetDirectedSchoolIds()
    {
        $schoolIds = [2, 3];
        $this->userRepository
            ->shouldReceive('getDirectedSchoolIds')
            ->with($this->userId)
            ->andReturn($schoolIds);
        $this->assertEquals($schoolIds, $this->sessionUser->getDirectedSchoolIds());
    }

    public function testGetAdministeredSchoolIds()
    {
        $schoolIds = [2, 3];
        $this->userRepository
            ->shouldReceive('getAdministeredSchoolIds')
            ->with($this->userId)
            ->andReturn($schoolIds);
        $this->assertEquals($schoolIds, $this->sessionUser->getAdministeredSchoolIds());
    }

    public function testGetDirectedCourseSchoolIds()
    {
        $schoolIds = [2, 3];
        $this->userRepository
            ->shouldReceive('getDirectedCourseAndSchoolIds')
            ->with($this->userId)
            ->andReturn(['schoolIds' => $schoolIds]);
        $this->assertEquals($schoolIds, $this->sessionUser->getDirectedCourseSchoolIds());
    }

    public function testGetAdministeredCourseSchoolIds()
    {
        $schoolIds = [2, 3];
        $this->userRepository
            ->shouldReceive('getAdministeredCourseAndSchoolIds')
            ->with($this->userId)
            ->andReturn(['schoolIds' => $schoolIds]);
        $this->assertEquals($schoolIds, $this->sessionUser->getAdministeredCourseSchoolIds());
    }

    public function testGetAdministeredSessionSchoolIds()
    {
        $schoolIds = [2, 3];
        $this->userRepository
            ->shouldReceive('getAdministeredSessionCourseAndSchoolIds')
            ->with($this->userId)
            ->andReturn(['schoolIds' => $schoolIds]);
        $this->assertEquals($schoolIds, $this->sessionUser->getAdministeredSessionSchoolIds());
    }

    public function testGetAdministeredSessionCourseIds()
    {
        $courseIds = [2, 3];
        $this->userRepository
            ->shouldReceive('getAdministeredSessionCourseAndSchoolIds')
            ->with($this->userId)
            ->andReturn(['courseIds' => $courseIds]);
        $this->assertEquals($courseIds, $this->sessionUser->getAdministeredSessionCourseIds());
    }

    public function testGetTaughtCourseIds()
    {
        $courseIds = [2, 3];
        $this->userRepository
            ->shouldReceive('getInstructedOfferingIlmSessionCourseAndSchoolIds')
            ->with($this->userId)
            ->andReturn(['courseIds' => $courseIds]);
        $this->assertEquals($courseIds, $this->sessionUser->getTaughtCourseIds());
    }

    public function testGetAdministeredSessionIds()
    {
        $sessionIds = [2, 3];
        $this->userRepository
            ->shouldReceive('getAdministeredSessionCourseAndSchoolIds')
            ->with($this->userId)
            ->andReturn(['sessionIds' => $sessionIds]);
        $this->assertEquals($sessionIds, $this->sessionUser->getAdministeredSessionIds());
    }

    public function testGetInstructedSessionIds()
    {
        $sessionIds = [2, 3];
        $this->userRepository
            ->shouldReceive('getInstructedOfferingIlmSessionCourseAndSchoolIds')
            ->with($this->userId)
            ->andReturn(['sessionIds' => $sessionIds]);
        $this->assertEquals($sessionIds, $this->sessionUser->getInstructedSessionIds());
    }

    public function testGetInstructedOfferingIds()
    {
        $userId = 1;
        $offeringIds = [1, 2, 3];
        $this->userRepository
            ->shouldReceive('getInstructedOfferingIlmSessionCourseAndSchoolIds')
            ->with($userId)
            ->andReturn(['offeringIds' => $offeringIds]);
        $this->assertEquals($offeringIds, $this->sessionUser->getInstructedOfferingIds());
    }

    public function testGetInstructedIlmIds()
    {
        $userId = 1;
        $ilmIds = [1, 2, 3];
        $this->userRepository
            ->shouldReceive('getInstructedOfferingIlmSessionCourseAndSchoolIds')
            ->with($userId)
            ->andReturn(['ilmIds' => $ilmIds]);
        $this->assertEquals($ilmIds, $this->sessionUser->getInstructedIlmIds());
    }

    public function testGetTaughtCourseSchoolIds()
    {
        $schoolIds = [2, 3];
        $this->userRepository
            ->shouldReceive('getInstructedOfferingIlmSessionCourseAndSchoolIds')
            ->with($this->userId)
            ->andReturn(['schoolIds' => $schoolIds]);
        $this->assertEquals($schoolIds, $this->sessionUser->getTaughtCourseSchoolIds());
    }

    public function testGetDirectedProgramIds()
    {
        $programIds = [2, 3];
        $this->userRepository
            ->shouldReceive('getDirectedProgramAndSchoolIds')
            ->with($this->userId)
            ->andReturn(['programIds' => $programIds]);
        $this->assertEquals($programIds, $this->sessionUser->getDirectedProgramIds());
    }

    public function testGetDirectedProgramYearIds()
    {
        $programYearIds = [2, 3];
        $this->userRepository
            ->shouldReceive('getDirectedProgramYearProgramAndSchoolIds')
            ->with($this->userId)
            ->andReturn(['programYearIds' => $programYearIds]);
        $this->assertEquals($programYearIds, $this->sessionUser->getDirectedProgramYearIds());
    }

    public function testGetDirectedProgramYearProgramIds()
    {
        $programIds = [2, 3];
        $this->userRepository
            ->shouldReceive('getDirectedProgramYearProgramAndSchoolIds')
            ->with($this->userId)
            ->andReturn(['programIds' => $programIds]);
        $this->assertEquals($programIds, $this->sessionUser->getDirectedProgramYearProgramIds());
    }

    public function testGetAdministeredCurriculumInventoryReportIds()
    {
        $reportIds = [2, 3];
        $this->userRepository
            ->shouldReceive('getAdministeredCurriculumInventoryReportAndSchoolIds')
            ->with($this->userId)
            ->andReturn(['reportIds' => $reportIds]);
        $this->assertEquals($reportIds, $this->sessionUser->getAdministeredCurriculumInventoryReportIds());
    }

    public function testGetAdministeredCurriculumInventoryReportSchoolIds()
    {
        $schoolIds = [2, 3];
        $this->userRepository
            ->shouldReceive('getAdministeredCurriculumInventoryReportAndSchoolIds')
            ->with($this->userId)
            ->andReturn(['schoolIds' => $schoolIds]);
        $this->assertEquals($schoolIds, $this->sessionUser->getAdministeredCurriculumInventoryReportSchoolIds());
    }

    public function testIsEqualTo(): void
    {
        $user1 = new SessionUser($this->createMockUser(1, $this->school), $this->userRepository);
        $anotherUser1 = new SessionUser($this->createMockUser(1, $this->school), $this->userRepository);
        $user2 = new SessionUser($this->createMockUser(2, $this->school), $this->userRepository);
        $notAnIliosSessionUser = m::mock(SymfonyUserInterface::class);
        $this->assertTrue($user1->isEqualTo($user1));
        $this->assertTrue($user1->isEqualTo($anotherUser1));
        $this->assertFalse($user1->isEqualTo($user2));
        $this->assertFalse($user1->isEqualTo($notAnIliosSessionUser));
    }

    public function testIsTheUser(): void
    {
        $iliosUser1 = $this->createMockUser(1, $this->school);
        $anotherIliosUser1 = $this->createMockUser(1, $this->school);
        $iliosUser2 = $this->createMockUser(2, $this->school);
        $sessionUser = new SessionUser($iliosUser1, $this->userRepository);

        $this->assertTrue($sessionUser->isTheUser($iliosUser1));
        $this->assertTrue($sessionUser->isTheUser($anotherIliosUser1));
        $this->assertFalse($sessionUser->isTheUser($iliosUser2));
    }

    public function testIsThePrimarySchool(): void
    {
        $otherSchool = new School();
        $otherSchool->setId(2);
        $sessionUser = new SessionUser($this->createMockUser(1, $this->school), $this->userRepository);
        $this->assertTrue($sessionUser->isThePrimarySchool($this->school));
        $this->assertFalse($sessionUser->isThePrimarySchool($otherSchool));
    }

    public static function rolesInSessionProvider(): array
    {
        $sessionId = 2;
        return [
            [$sessionId, [], [], []],
            [$sessionId, [$sessionId], [], [UserRoles::SESSION_ADMINISTRATOR]],
            [$sessionId, [], [$sessionId], [UserRoles::SESSION_INSTRUCTOR]],
            [$sessionId, [$sessionId], [$sessionId], [UserRoles::SESSION_ADMINISTRATOR, UserRoles::SESSION_INSTRUCTOR]],
        ];
    }

    protected function createMockUser(
        int $userId,
        SchoolInterface $school,
        bool $isRoot = false,
        bool $enabled = true,
        ?AuthenticationInterface $authentication = null
    ): m\MockInterface {
        $iliosUser = m::mock(UserInterface::class);
        $iliosUser->shouldReceive('getId')->andReturn($userId);
        $iliosUser->shouldReceive('getSchool')->andReturn($school);
        $iliosUser->shouldReceive('isRoot')->andReturn($isRoot);
        $iliosUser->shouldReceive('isEnabled')->andReturn($enabled);
        $iliosUser->shouldReceive('getAuthentication')->andReturn($authentication);
        return $iliosUser;
    }
}
