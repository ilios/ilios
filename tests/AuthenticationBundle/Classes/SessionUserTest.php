<?php

namespace Tests\AuthenticationBundle\Classes;

use Ilios\AuthenticationBundle\Classes\SessionUser;
use Ilios\CoreBundle\Entity\Manager\UserManager;
use Ilios\CoreBundle\Entity\School;
use Ilios\CoreBundle\Entity\UserInterface;
use Ilios\CoreBundle\Service\Config;
use Mockery as m;
use Symfony\Bundle\FrameworkBundle\Tests\TestCase;

/**
 * Class SessionUserTest
 * @package Tests\AuthenticationBundle\Classes
 */
class SessionUserTest extends TestCase
{
    use m\Adapter\Phpunit\MockeryPHPUnitIntegration;

    protected $relationships;

    protected $iliosUser;

    protected $userManager;

    /**
     * @inheritdoc
     */
    public function setUp()
    {
        $this->userManager = m::mock(UserManager::class);
        $this->iliosUser = m::mock(UserInterface::class);

        $this->relationships = [
            'nonStudentSchoolIds' => [],
            'directedCourseIds' => [],
            'administeredCourseIds' => [],
            'directedSchoolIds' => [],
            'administeredSchoolIds' => [],
            'directedCourseSchoolIds' => [],
            'administeredCourseSchoolIds' => [],
            'administeredSessionSchoolIds' => [],
            'administeredSessionCourseIds' => [],
            'taughtCourseIds' => [],
            'taughtCourseSchoolIds' => [],
            'administeredSessionIds' => [],
            'instructedSessionIds' => [],
            'directedProgramIds' => [],
            'directedProgramYearIds' => [],
            'directedProgramYearProgramIds' => [],
            'directedCohortIds' => [],
            'administeredCurriculumInventoryReportIds' => [],
            'administeredCurriculumInventoryReportSchoolIds' => [],
        ];

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
        unset($this->relationships);
        unset($this->iliosUser);
        unset($this->userManager);
        unset($this->config);
    }
    /**
     * @return array
     */
    public function performsNonLearnerFunctionProvider()
    {
        return [
            [[], false],
            [['directedCourseIds' => [1]], true],
            [['administeredCourseIds' => [1]], true],
            [['administeredSchoolIds' => [1]], true],
            [['taughtCourseIds' => [1]], true],
            [['administeredSessionIds' => [1]], true],
            [['instructedSessionIds' => [1]], true],
            [['directedProgramIds' => [1]], true],
            [['directedProgramYearIds' => [1]], true],
            [['directedCohortIds' => [1]], true],
            [['administeredCurriculumInventoryReportIds' => [1]], true],
        ];
    }

    /**
     * @dataProvider performsNonLearnerFunctionProvider
     * @covers \Ilios\AuthenticationBundle\Classes\SessionUser::performsNonLearnerFunction
     */
    public function testPerformsNonLearnerFunction($modifiedRelationships, $expectedResult)
    {
        $relationships = array_merge($this->relationships, $modifiedRelationships);
        $this->userManager->shouldReceive('buildSessionRelationships')->andReturn($relationships);
        $sessionUser = new SessionUser($this->iliosUser, $this->userManager);
        $this->assertEquals($expectedResult, $sessionUser->performsNonLearnerFunction());
    }
}
