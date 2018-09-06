<?php

namespace Tests\AppBundle\Classes;

use AppBundle\Classes\SessionUser;
use AppBundle\Entity\Manager\UserManager;
use AppBundle\Entity\School;
use AppBundle\Entity\UserInterface;
use Mockery as m;
use Symfony\Bundle\FrameworkBundle\Tests\TestCase;

/**
 * Class SessionUserTest
 * @package Tests\AppBundle\Classes
 */
class SessionUserTest extends TestCase
{
    use m\Adapter\Phpunit\MockeryPHPUnitIntegration;

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
     * @inheritdoc
     */
    public function setUp()
    {
        $this->userManager = m::mock(UserManager::class);
        $this->iliosUser = m::mock(UserInterface::class);

        $school = m::mock(School::class);
        $school->shouldReceive('getId')->andReturn(1);

        $this->iliosUser->shouldReceive('getId')->andReturn(1);
        $this->iliosUser->shouldReceive('isRoot')->andReturn(false);
        $this->iliosUser->shouldReceive('isEnabled')->andReturn(true);
        $this->iliosUser->shouldReceive('getSchool')->andReturn($school);
        $this->iliosUser->shouldReceive('getAuthentication')->andReturn(null);

        $this->sessionUser = new SessionUser($this->iliosUser, $this->userManager);
    }

    /**
     * @inheritdoc
     */
    public function tearDown()
    {
        unset($this->sessionUser);
        unset($this->iliosUser);
        unset($this->userManager);
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
            ->shouldReceive('getInstructedSessionCourseAndSchoolIds')
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
            ->shouldReceive('getInstructedSessionCourseAndSchoolIds')
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
            ->shouldReceive('getInstructedSessionCourseAndSchoolIds')
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
            ->shouldReceive('getInstructedSessionCourseAndSchoolIds')
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
            ->shouldReceive('getInstructedSessionCourseAndSchoolIds')
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
            ->shouldReceive('getInstructedSessionCourseAndSchoolIds')
            ->andReturn($taughtSessionIds);
        $this->assertFalse($this->sessionUser->isTeachingSession(1));
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
        $this->markTestIncomplete('to be implemented.');
    }

    /**
     * @covers SessionUser::rolesInCourse
     */
    public function testRolesInCourse()
    {
        $this->markTestIncomplete('to be implemented.');
    }

    /**
     * @covers SessionUser::rolesInSession
     */
    public function testRolesInSession()
    {
        $this->markTestIncomplete('to be implemented.');
    }

    /**
     * @covers SessionUser::rolesInProgram
     */
    public function testRolesInProgram()
    {
        $this->markTestIncomplete('to be implemented.');
    }

    /**
     * @covers SessionUser::rolesInProgramYear
     */
    public function testRolesInProgramYear()
    {
        $this->markTestIncomplete('to be implemented.');
    }

    /**
     * @covers SessionUser::rolesInCurriculumInventoryReport
     */
    public function testRolesInCurriculumInventoryReport()
    {
        $this->markTestIncomplete('to be implemented.');
    }

    /**
     * @covers SessionUser::performsNonLearnerFunction()
     */
    public function testPerformsNonLearnerFunction()
    {
        $this->markTestIncomplete('to be implemented.');
    }

    /**
     * @covers SessionUser::getDirectedCourseIds
     */
    public function testGetDirectedCourseIds()
    {
        $this->markTestIncomplete('to be implemented.');
    }

    /**
     * @covers SessionUser::getAdministeredCourseIds
     */
    public function testGetAdministeredCourseIds()
    {
        $this->markTestIncomplete('to be implemented.');
    }

    /**
     * @covers SessionUser::getDirectedSchoolIds
     */
    public function testGetDirectedSchoolIds()
    {
        $this->markTestIncomplete('to be implemented.');
    }

    /**
     * @covers SessionUser::getAdministeredSchoolIds
     */
    public function testGetAdministeredSchoolIds()
    {
        $this->markTestIncomplete('to be implemented.');
    }

    /**
     * @covers SessionUser::getDirectedCourseSchoolIds
     */
    public function testGetDirectedCourseSchoolIds()
    {
        $this->markTestIncomplete('to be implemented.');
    }

    /**
     * @covers SessionUser::getAdministeredCourseSchoolIds
     */
    public function testGetAdministeredCourseSchoolIds()
    {
        $this->markTestIncomplete('to be implemented.');
    }

    /**
     * @covers SessionUser::getAdministeredSessionSchoolIds
     */
    public function testGetAdministeredSessionSchoolIds()
    {
        $this->markTestIncomplete('to be implemented.');
    }

    /**
     * @covers SessionUser::getAdministeredSessionCourseIds
     */
    public function testGetAdministeredSessionCourseIds()
    {
        $this->markTestIncomplete('to be implemented.');
    }

    /**
     * @covers SessionUser::getTaughtCourseIds
     */
    public function testGetTaughtCourseIds()
    {
        $this->markTestIncomplete('to be implemented.');
    }

    /**
     * @covers SessionUser::getAdministeredSessionIds
     */
    public function testGetAdministeredSessionIds()
    {
        $this->markTestIncomplete('to be implemented.');
    }

    /**
     * @covers SessionUser::getInstructedSessionIds
     */
    public function testGetInstructedSessionIds()
    {
        $this->markTestIncomplete('to be implemented.');
    }

    /**
     * @covers SessionUser::getTaughtCourseSchoolIds
     */
    public function testGetTaughtCourseSchoolIds()
    {
        $this->markTestIncomplete('to be implemented.');
    }

    /**
     * @covers SessionUser::getDirectedProgramIds
     */
    public function testGetDirectedProgramIds()
    {
        $this->markTestIncomplete('to be implemented.');
    }

    /**
     * @covers SessionUser::getDirectedProgramYearIds
     */
    public function testGetDirectedProgramYearIds()
    {
        $this->markTestIncomplete('to be implemented.');
    }

    /**
     * @covers SessionUser::getDirectedProgramYearProgramIds
     */
    public function testGetDirectedProgramYearProgramIds()
    {
        $this->markTestIncomplete('to be implemented.');
    }

    /**
     * @covers SessionUser::getAdministeredCurriculumInventoryReportIds
     */
    public function testGetAdministeredCurriculumInventoryReportIds()
    {
        $this->markTestIncomplete('to be implemented.');
    }

    /**
     * @covers SessionUser::getAdministeredCurriculumInventoryReportSchoolIds
     */
    public function testGetAdministeredCurriculumInventoryReportSchoolIds()
    {
        $this->markTestIncomplete('to be implemented.');
    }
}
