<?php
namespace Ilios\CliBundle\Tests\Command;

use Ilios\CliBundle\Command\RolloverCourseCommand;
use Ilios\CoreBundle\Classes\CourseRollover;
use Ilios\CoreBundle\Entity\Course;
use Symfony\Component\Console\Application;
use Symfony\Component\Console\Tester\CommandTester;

use Mockery as m;

/**
 * Class CourseRolloverTest
 * @package Ilios\CliBundle\Tests\Command
 */
class CourseRolloverTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var m\MockInterface
     */
    protected $courseManager;

    /**
     * @var m\MockInterface
     */
    protected $learningMaterialManager;

    /**
     * @var m\MockInterface
     */
    protected $courseLearningMaterialManager;

    /**
     * @var m\MockInterface
     */
    protected $sessionManager;

    /**
     * @var m\MockInterface
     */
    protected $sessionLearningMaterialManager;

    /**
     * @var m\MockInterface
     */
    protected $offeringManager;

    /**
     * @var CourseRollover
     */
    protected $service;


    /**
     * @inheritdoc
     */
    public function setUp()
    {
        $this->courseManager = m::mock('Ilios\CoreBundle\Entity\Manager\ManagerInterface');
        $this->learningMaterialManager = m::mock('Ilios\CoreBundle\Entity\Manager\ManagerInterface');
        $this->courseLearningMaterialManager = m::mock('Ilios\CoreBundle\Entity\Manager\ManagerInterface');
        $this->sessionManager = m::mock('Ilios\CoreBundle\Entity\Manager\ManagerInterface');
        $this->sessionLearningMaterialManager = m::mock('Ilios\CoreBundle\Entity\Manager\ManagerInterface');
        $this->offeringManager = m::mock('Ilios\CoreBundle\Entity\Manager\ManagerInterface');
        $this->service = new CourseRollover(
            $this->courseManager,
            $this->learningMaterialManager,
            $this->courseLearningMaterialManager,
            $this->sessionManager,
            $this->sessionLearningMaterialManager,
            $this->offeringManager
        );
    }

    /**
     * @inheritdoc
     */
    public function tearDown()
    {
        unset($this->courseManager);
        unset($this->learningMaterialManager);
        unset($this->courseLearningMaterialManager);
        unset($this->sessionManager);
        unset($this->sessionLearningMaterialManager);
        unset($this->offeringManager);
        unset($this->service);
        m::close();
    }

    public function testRolloverWithEverything()
    {
        $this->markTestIncomplete();
    }

    public function testRolloverWithNewStartDate()
    {
        $this->markTestIncomplete();
    }

    public function testRolloverWithoutSessions()
    {
        $this->markTestIncomplete();
    }

    public function testRolloverWithoutCourseLearningMaterials()
    {
        $this->markTestIncomplete();
    }

    public function testRolloverWithoutCourseObjectives()
    {
        $this->markTestIncomplete();
    }

    public function testRolloverWithoutCourseTopics()
    {
        $this->markTestIncomplete();
    }

    public function testRolloverWithoutCourseMesh()
    {
        $this->markTestIncomplete();
    }

    public function testRolloverWithoutSessionLearningMaterials()
    {
        $this->markTestIncomplete();
    }

    public function testRolloverWithoutSessionObjectives()
    {
        $this->markTestIncomplete();
    }

    public function testRolloverWithoutSessionTopics()
    {
        $this->markTestIncomplete();
    }

    public function testRolloverWithoutSessionMesh()
    {
        $this->markTestIncomplete();
    }

    public function testRolloverWithoutOfferings()
    {
        $this->markTestIncomplete();
    }

    public function testRolloverWithoutInstructors()
    {
        $this->markTestIncomplete();
    }

    public function testRolloverWithoutInstructorGroups()
    {
        $this->markTestIncomplete();
    }

    public function testRolloverWithNewCourseTitle()
    {
        $this->markTestIncomplete();
    }

    // @todo test the hell out of this. use a data provider here. [ST 2016/06/17]
    public function testRolloverOffsetCalculation()
    {
        $this->markTestIncomplete();
    }

    public function testRolloverFailsOnDuplicate()
    {
        $course = new Course();
        $course->setId(10);
        $course->setTitle('lorem ipsum');
        $courseId = 10;
        $futureDate = new \DateTime();
        $futureDate->add(\DateInterval::createFromDateString('+2 year'));
        $year = $futureDate->format('Y');
        $this->courseManager->shouldReceive('findOneBy')->withArgs([['id' => $course->getId()]])->andReturn($course);
        $this->courseManager
            ->shouldReceive('findBy')
            ->withArgs([['title' => $course->getTitle(), 'year' => $year]])
            ->andReturn(new Course());

        $this->setExpectedException(
            \Exception::class,
            "Another course with the same title and academic year already exists."
            . " If the year is correct, consider setting a new course title with '--new-course-title' option."
        );

        $this->service->rolloverCourse($courseId, $year, ['']);

    }

    public function testRolloverFailsOnYearPast()
    {
        $courseId = 10;
        $pastDate = new \DateTime();
        $pastDate->add(\DateInterval::createFromDateString('-2 year'));
        $year = $pastDate->format('Y');

        $this->setExpectedException(
            \Exception::class,
            "You cannot rollover a course to a new year or start date that is already in the past."
        );

        $this->service->rolloverCourse($courseId, $year, []);
    }

    public function testRolloverFailsOnMissingCourse()
    {
        $courseId = -1;
        $futureDate = new \DateTime();
        $futureDate->add(\DateInterval::createFromDateString('+2 year'));
        $year = $futureDate->format('Y');
        $this->courseManager->shouldReceive('findOneBy')->withArgs([['id' => $courseId]])->andReturn(false);

        $this->setExpectedException(\Exception::class, "There are no courses with courseId {$courseId}.");

        $this->service->rolloverCourse($courseId, $year, []);

    }
}
