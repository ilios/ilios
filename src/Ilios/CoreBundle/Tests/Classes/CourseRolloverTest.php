<?php
namespace Ilios\CliBundle\Tests\Command;

use Ilios\CliBundle\Command\RolloverCourseCommand;
use Ilios\CoreBundle\Classes\CourseRollover;
use Ilios\CoreBundle\Entity\Course;
use Ilios\CoreBundle\Entity\CourseClerkshipType;
use Ilios\CoreBundle\Entity\MeshDescriptor;
use Ilios\CoreBundle\Entity\Objective;
use Ilios\CoreBundle\Entity\School;
use Ilios\CoreBundle\Entity\Session;
use Ilios\CoreBundle\Entity\SessionType;
use Symfony\Component\Console\Application;
use Symfony\Component\Console\Tester\CommandTester;
use Doctrine\Common\Collections\Collection;
use Mockery as m;
use \DateTime;

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
     * @var m\MockInterface
     */
    protected $objectiveManager;

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
        $this->objectiveManager = m::mock('Ilios\CoreBundle\Entity\Manager\ManagerInterface');
        $this->service = new CourseRollover(
            $this->courseManager,
            $this->learningMaterialManager,
            $this->courseLearningMaterialManager,
            $this->sessionManager,
            $this->sessionLearningMaterialManager,
            $this->offeringManager,
            $this->objectiveManager
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
        unset($this->objectiveManager);
        unset($this->service);
        m::close();
    }

    public function testRolloverWithEverything()
    {
        $course = $this->createTestCourse();
        $newYear = $course->getYear() + 1;
        $this->courseManager->shouldReceive('findOneBy')->withArgs([['id' => $course->getId()]])->andReturn($course);
        $this->courseManager
            ->shouldReceive('findBy')
            ->withArgs([['title' => $course->getTitle(), 'year' => $newYear]])
            ->andReturn(false);

        $newCourse = m::mock('Ilios\CoreBundle\Entity\CourseInterface');
        $newCourse->shouldReceive('setTitle')->with($course->getTitle());
        $newCourse->shouldReceive('setYear')->with($newYear);
        $newCourse->shouldReceive('setLevel')->with($course->getLevel());
        $newCourse->shouldReceive('setExternalId')->with($course->getExternalId());
        $newCourse->shouldReceive('setLocked')->with(false);
        $newCourse->shouldReceive('setArchived')->with(false);
        $newCourse->shouldReceive('setPublished')->with(false);
        $newCourse->shouldReceive('setPublishedAsTbd')->with(false);

        //@todo better comparison of startDate and newStartDate
        $newCourse->shouldReceive('setStartDate')->with(m::on(function(DateTime $newStartDate) use ($course) {
            return $newStartDate > $course->getStartDate();
        }));

        //@todo better comparison of endDate and newEndDate
        $newCourse->shouldReceive('setEndDate')->with(m::on(function(DateTime $newEndDate) use ($course) {
            return $newEndDate > $course->getEndDate();
        }));
        $newCourse->shouldReceive('setClerkshipType')->with($course->getClerkshipType());
        $newCourse->shouldReceive('setSchool')->with($course->getSchool());
        $newCourse->shouldReceive('setDirectors')->with($course->getDirectors());
        $newCourse->shouldReceive('setTerms')->with($course->getTerms());
        $newCourse->shouldReceive('setMeshDescriptors')->with($course->getMeshDescriptors());

        foreach ($course->getObjectives() as $objective) {
            $newObjective = m::mock('Ilios\CoreBundle\Entity\Objective');
            $newObjective->shouldReceive('setTitle')->with($objective->getTitle());
            $newObjective->shouldReceive('addCourse')->with($newCourse);
            $newObjective->shouldReceive('setMeshDescriptors')->with($objective->getMeshDescriptors());
            $this->objectiveManager
                ->shouldReceive('create')->once()
                ->andReturn($newObjective);
            $this->objectiveManager->shouldReceive('update')->withArgs([$newObjective, false, false]);
        }

        foreach ($course->getSessions() as $session) {
            $newSession = m::mock('Ilios\CoreBundle\Entity\Session');
            $newSession->shouldReceive('setTitle')->with($session->getTitle());
            $newSession->shouldReceive('setCourse')->with($newCourse);
            $newSession->shouldReceive('setAttireRequired')->with($session->isAttireRequired());
            $newSession->shouldReceive('setEquipmentRequired')->with($session->isEquipmentRequired());
            $newSession->shouldReceive('setSessionType')->with($session->getSessionType());
            $newSession->shouldReceive('setSupplemental')->with($session->isSupplemental());
            $newSession->shouldReceive('setPublished')->with(false);
            $newSession->shouldReceive('setPublishedAsTbd')->with(false);
            $newSession->shouldReceive('setMeshDescriptors')->with($session->getMeshDescriptors());
            $newSession->shouldReceive('setTerms')->with($session->getTerms());
            $this->sessionManager
                ->shouldReceive('create')
                ->andReturn($newSession);
            $this->sessionManager->shouldReceive('update')->withArgs([$newSession, false, false]);

            foreach ($session->getObjectives() as $objective) {
                $newObjective = m::mock('Ilios\CoreBundle\Entity\Objective');
                $newObjective->shouldReceive('setTitle')->with($objective->getTitle());
                $newObjective->shouldReceive('addSession')->with($newSession);
                $newObjective->shouldReceive('setMeshDescriptors')->with($objective->getMeshDescriptors());
                $newObjective->shouldReceive('setParents')->with(m::on(function(Collection $collection) use ($objective) {
                    return count($collection) === count($objective->getParents());
                }));
                $this->objectiveManager
                    ->shouldReceive('create')->once()
                    ->andReturn($newObjective);
                $this->objectiveManager->shouldReceive('update')->withArgs([$newObjective, false, false]);
            }
        }


        $this->courseManager->shouldReceive('update')->withArgs([$newCourse, false, false]);

        $this->courseManager
            ->shouldReceive('create')
            ->andReturn($newCourse);

        $this->courseManager->shouldReceive('flushAndClear')->once();

        $rhett = $this->service->rolloverCourse($course->getId(), $newYear, ['']);
        $this->assertSame($newCourse, $rhett);
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
        $course = $this->createTestCourse();
        $newYear = $course->getYear() + 1;
        $this->courseManager->shouldReceive('findOneBy')->withArgs([['id' => $course->getId()]])->andReturn($course);
        $this->courseManager
            ->shouldReceive('findBy')
            ->withArgs([['title' => $course->getTitle(), 'year' => $newYear]])
            ->andReturn(new Course());

        $this->setExpectedException(
            \Exception::class,
            "Another course with the same title and academic year already exists."
            . " If the year is correct, consider setting a new course title with '--new-course-title' option."
        );

        $this->service->rolloverCourse($course->getId(), $newYear, ['']);

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

    public function testRolloverOfCourseObjectives()
    {
        $this->markTestIncomplete();
    }

    protected function createTestCourse()
    {
        $course = new Course();
        $course->setId(10);
        $course->setTitle('test course');
        $course->setLevel(1);
        $course->setYear(2015);
        $course->setStartDate(new DateTime('yesterday'));
        $course->setEndDate(new DateTime('tomorrow'));
        $course->setExternalId('I45');
        $course->setLocked(true);
        $course->setArchived(true);
        $course->setPublished(true);
        $course->setPublishedAsTbd(true);

        $course->setClerkshipType(new CourseClerkshipType());
        $course->setSchool(new School());
        $courseObjective1 = new Objective();
        $courseObjective1->setId(808);
        $courseObjective1->setTitle('test course objective1');
        $courseObjective1->addMeshDescriptor(new MeshDescriptor());
        $course->addObjective($courseObjective1);
        $courseObjective2 = new Objective();
        $courseObjective2->setId(42);
        $courseObjective2->setTitle('test course objective2');
        $course->addObjective($courseObjective2);

        $session1 = new Session();
        $session1->setSessionType(new SessionType());
        $sessionObjective1 = new Objective();
        $sessionObjective1->setId(99);
        $sessionObjective1->setTitle('test session objective');
        $sessionObjective1->addMeshDescriptor(new MeshDescriptor());
        $sessionObjective1->addParent($courseObjective1);
        $sessionObjective1->addParent($courseObjective2);
        $session1->addObjective($sessionObjective1);

        $course->addSession($session1);

        return $course;
    }
}
