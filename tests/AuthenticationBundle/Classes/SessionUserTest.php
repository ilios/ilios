<?php

namespace Tests\AuthenticationBundle\Classes;

use Ilios\AuthenticationBundle\Classes\SessionUser;
use Ilios\CoreBundle\Entity\Manager\UserManager;
use Ilios\CoreBundle\Entity\School;
use Ilios\CoreBundle\Entity\UserInterface;
use Mockery as m;
use Symfony\Bundle\FrameworkBundle\Tests\TestCase;

/**
 * Class SessionUserTest
 * @package Tests\AuthenticationBundle\Classes
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
        $this->markTestIncomplete('to be implemented.');
    }

    /**
     * @covers SessionUser::isTeachingCourseInSchool
     */
    public function testIsNotTeachingCourseInSchool()
    {
        $this->markTestIncomplete('to be implemented.');
    }

    /**
     * @covers SessionUser::isTeachingCourse
     */
    public function testIsTeachingCourse()
    {
        $this->markTestIncomplete('to be implemented.');
    }

    /**
     * @covers SessionUser::isTeachingCourse
     */
    public function testIsNotTeachingCourse()
    {
        $this->markTestIncomplete('to be implemented.');
    }

    /**
     * @covers SessionUser::isAdministeringSessionInCourse
     */
    public function testIsAdministeringSessionInCourse()
    {
        $this->markTestIncomplete('to be implemented.');
    }

    /**
     * @covers SessionUser::isAdministeringSessionInCourse
     */
    public function testIsNotAdministeringSessionInCourse()
    {
        $this->markTestIncomplete('to be implemented.');
    }

    /**
     * @covers SessionUser::isAdministeringSession
     */
    public function testIsAdministeringSession()
    {
        $this->markTestIncomplete('to be implemented.');
    }

    /**
     * @covers SessionUser::isAdministeringSession
     */
    public function testIsNotAdministeringSession()
    {
        $this->markTestIncomplete('to be implemented.');
    }

    /**
     * @covers SessionUser::isTeachingSession
     */
    public function testIsTeachingSession()
    {
        $this->markTestIncomplete('to be implemented.');
    }

    /**
     * @covers SessionUser::isTeachingSession
     */
    public function testIsNotTeachingSession()
    {
        $this->markTestIncomplete('to be implemented.');
    }

    /**
     * @covers SessionUser::isDirectingProgram
     */
    public function testIsDirectingProgram()
    {
        $this->markTestIncomplete('to be implemented.');
    }

    /**
     * @covers SessionUser::isDirectingProgram
     */
    public function testIsNotDirectingProgram()
    {
        $this->markTestIncomplete('to be implemented.');
    }

    /**
     * @covers SessionUser::isDirectingProgramYear
     */
    public function testIsDirectingProgramYear()
    {
        $this->markTestIncomplete('to be implemented.');
    }

    /**
     * @covers SessionUser::isDirectingProgramYear
     */
    public function testIsNotDirectingProgramYear()
    {
        $this->markTestIncomplete('to be implemented.');
    }

    /**
     * @covers SessionUser::isDirectingProgramYearInProgram
     */
    public function testIsDirectingProgramYearInProgram()
    {
        $this->markTestIncomplete('to be implemented.');
    }

    /**
     * @covers SessionUser::isDirectingProgramYearInProgram
     */
    public function testIsNotDirectingProgramYearInProgram()
    {
        $this->markTestIncomplete('to be implemented.');
    }

    /**
     * @covers SessionUser::isAdministeringCurriculumInventoryReport
     */
    public function testIsAdministeringCurriculumInventoryReport()
    {
        $this->markTestIncomplete('to be implemented.');
    }

    /**
     * @covers SessionUser::isAdministeringCurriculumInventoryReport
     */
    public function testIsNotAdministeringCurriculumInventoryReport()
    {
        $this->markTestIncomplete('to be implemented.');
    }

    /**
     * @covers SessionUser::isAdministeringCurriculumInventoryReportInSchool
     */
    public function testIsAdministeringCurriculumInventoryReportInSchool()
    {
        $this->markTestIncomplete('to be implemented.');
    }

    /**
     * @covers SessionUser::isAdministeringCurriculumInventoryReportInSchool
     */
    public function testIsNotAdministeringCurriculumInventoryReportInSchool()
    {
        $this->markTestIncomplete('to be implemented.');
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
