<?php

namespace Tests\AuthenticationBundle\Classes;

use Ilios\AuthenticationBundle\Classes\SessionUser;
use Ilios\CoreBundle\Entity\Manager\UserManager;
use Ilios\CoreBundle\Entity\School;
use Ilios\CoreBundle\Entity\Session;
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

    protected $iliosUser;

    protected $userManager;

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
    }

    /**
     * @inheritdoc
     */
    public function tearDown()
    {
        unset($this->iliosUser);
        unset($this->userManager);
        unset($this->config);
    }

    /**
     * @covers SessionUser::isDirectingCourse
     */
    public function testIsDirectingCourse()
    {
        $this->markTestIncomplete('to be implemented.');
    }

    /**
     * @covers SessionUser::isDirectingCourse
     */
    public function testIsNotDirectingCourse()
    {
        $this->markTestIncomplete('to be implemented.');
    }

    /**
     * @covers SessionUser::isAdministeringCourse
     */
    public function testIsAdministeringCourse()
    {
        $this->markTestIncomplete('to be implemented.');
    }

    /**
     * @covers SessionUser::isAdministeringCourse
     */
    public function testIsNotAdministeringCourse()
    {
        $this->markTestIncomplete('to be implemented.');
    }

    /**
     * @covers SessionUser::isDirectingCourse
     */
    public function testIsDirectingSchool()
    {
        $this->markTestIncomplete('to be implemented.');
    }

    /**
     * @covers SessionUser::isAdministeringSchool
     */
    public function testIsAdministeringSchool()
    {
        $this->markTestIncomplete('to be implemented.');
    }

    /**
     * @covers SessionUser::isAdministeringSchool
     */
    public function testIsNotAdministeringSchool()
    {
        $this->markTestIncomplete('to be implemented.');
    }

    /**
     * @covers SessionUser::isDirectingCourseInSchool
     */
    public function testIsDirectingCourseInSchool()
    {
        $this->markTestIncomplete('to be implemented.');
    }

    /**
     * @covers SessionUser::isDirectingCourseInSchool
     */
    public function testIsNotDirectingCourseInSchool()
    {
        $this->markTestIncomplete('to be implemented.');
    }

    /**
     * @covers SessionUser::isAdministeringCourseInSchool
     */
    public function testIsAdministeringCourseInSchool()
    {
        $this->markTestIncomplete('to be implemented.');

    }

    /**
     * @covers SessionUser::isAdministeringCourseInSchool
     */
    public function testIsNotAdministeringCourseInSchool()
    {
        $this->markTestIncomplete('to be implemented.');

    }

    /**
     * @covers SessionUser::isAdministeringSessionInSchool
     */
    public function testIsAdministeringSessionInSchool()
    {
        $this->markTestIncomplete('to be implemented.');

    }

    /**
     * @covers SessionUser::isAdministeringSessionInSchool
     */
    public function testIsNotAdministeringSessionInSchool()
    {
        $this->markTestIncomplete('to be implemented.');

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
