<?php
namespace Tests\CoreBundle\Service;

use Ilios\CoreBundle\Entity\Manager\CourseLearningMaterialManager;
use Ilios\CoreBundle\Entity\Manager\CourseManager;
use Ilios\CoreBundle\Entity\Manager\IlmSessionManager;
use Ilios\CoreBundle\Entity\Manager\LearningMaterialManager;
use Ilios\CoreBundle\Entity\Manager\ObjectiveManager;
use Ilios\CoreBundle\Entity\Manager\OfferingManager;
use Ilios\CoreBundle\Entity\Manager\SessionDescriptionManager;
use Ilios\CoreBundle\Entity\Manager\SessionLearningMaterialManager;
use Ilios\CoreBundle\Entity\Manager\SessionManager;
use Ilios\CoreBundle\Service\CourseRollover;
use Ilios\CoreBundle\Entity\Cohort;
use Ilios\CoreBundle\Entity\Course;
use Ilios\CoreBundle\Entity\CourseClerkshipType;
use Ilios\CoreBundle\Entity\CourseInterface;
use Ilios\CoreBundle\Entity\CourseLearningMaterial;
use Ilios\CoreBundle\Entity\IlmSession;
use Ilios\CoreBundle\Entity\InstructorGroup;
use Ilios\CoreBundle\Entity\LearnerGroup;
use Ilios\CoreBundle\Entity\LearningMaterial;
use Ilios\CoreBundle\Entity\MeshDescriptor;
use Ilios\CoreBundle\Entity\Objective;
use Ilios\CoreBundle\Entity\Offering;
use Ilios\CoreBundle\Entity\School;
use Ilios\CoreBundle\Entity\Session;
use Ilios\CoreBundle\Entity\SessionDescription;
use Ilios\CoreBundle\Entity\SessionLearningMaterial;
use Ilios\CoreBundle\Entity\SessionType;
use Ilios\CoreBundle\Entity\Term;
use Ilios\CoreBundle\Entity\User;
use Doctrine\Common\Collections\Collection;
use Mockery as m;
use \DateTime;
use Tests\CoreBundle\TestCase;

/**
 * Class CourseRolloverTest
 */
class CourseRolloverTest extends TestCase
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
     * @var m\MockInterface
     */
    protected $sessionDescriptionManager;

    /**
     * @var m\MockInterface
     */
    protected $ilmSessionManager;

    /**
     * @var CourseRollover
     */
    protected $service;


    /**
     * @inheritdoc
     */
    public function setUp()
    {
        $this->courseManager = m::mock(CourseManager::class);
        $this->learningMaterialManager = m::mock(LearningMaterialManager::class);
        $this->courseLearningMaterialManager = m::mock(CourseLearningMaterialManager::class);
        $this->sessionManager = m::mock(SessionManager::class);
        $this->sessionDescriptionManager = m::mock(SessionDescriptionManager::class);
        $this->sessionLearningMaterialManager = m::mock(SessionLearningMaterialManager::class);
        $this->offeringManager = m::mock(OfferingManager::class);
        $this->objectiveManager = m::mock(ObjectiveManager::class);
        $this->ilmSessionManager = m::mock(IlmSessionManager::class);
        $this->service = new CourseRollover(
            $this->courseManager,
            $this->learningMaterialManager,
            $this->courseLearningMaterialManager,
            $this->sessionManager,
            $this->sessionDescriptionManager,
            $this->sessionLearningMaterialManager,
            $this->offeringManager,
            $this->objectiveManager,
            $this->ilmSessionManager
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
        unset($this->sessionDescriptionManager);
        unset($this->sessionLearningMaterialManager);
        unset($this->offeringManager);
        unset($this->objectiveManager);
        unset($this->ilmSessionManager);
        unset($this->service);
    }

    public function testRolloverWithEverything()
    {
        $course = $this->createTestCourseWithAssociations();
        $newCourse = m::mock('Ilios\CoreBundle\Entity\CourseInterface');
        $newYear = $this->setupCourseManager($course, $newCourse);

        $newCourse->shouldReceive('setTitle')->with($course->getTitle())->once();
        $newCourse->shouldReceive('setYear')->with($newYear)->once();
        $newCourse->shouldReceive('setLevel')->with($course->getLevel())->once();
        $newCourse->shouldReceive('setExternalId')->with($course->getExternalId())->once();

        $newCourse->shouldReceive('setStartDate')->with(m::on(function (DateTime $newStart) use ($course) {
            $oldStart = $course->getStartDate();
            return (
                //day of the week is the same
                $oldStart->format('w') === $newStart->format('w') &&
                //Week of the year is the same
                $oldStart->format('W') === $newStart->format('W')
            );
        }))->once();

        $newCourse->shouldReceive('setEndDate')->with(m::on(function (DateTime $newEnd) use ($course) {
            $oldEnd = $course->getEndDate();
            return (
                //day of the week is the same
                $oldEnd->format('w') === $newEnd->format('w') &&
                //Week of the year is the same
                $oldEnd->format('W') === $newEnd->format('W')
            );
        }))->once();
        $newCourse->shouldReceive('setClerkshipType')->with($course->getClerkshipType())->once();
        $newCourse->shouldReceive('setSchool')->with($course->getSchool())->once();
        $newCourse->shouldReceive('setDirectors')->with($course->getDirectors())->once();
        $newCourse->shouldReceive('setTerms')->with($course->getTerms())->once();
        $newCourse->shouldReceive('setMeshDescriptors')->with($course->getMeshDescriptors())->once();

        $ancestor = $course->getAncestor();
        $newCourse->shouldReceive('setAncestor')->with($ancestor)->once();

        /** @var Objective $objective */
        foreach ($course->getObjectives() as $objective) {
            $newObjective = m::mock('Ilios\CoreBundle\Entity\Objective');
            $newObjective->shouldReceive('setTitle')->with($objective->getTitle())->once();
            $newObjective->shouldReceive('addCourse')->with($newCourse)->once();
            $newObjective->shouldReceive('setMeshDescriptors')->with($objective->getMeshDescriptors())->once();
            $ancestor = $objective->getAncestor();
            if ($ancestor) {
                $newObjective->shouldReceive('setAncestor')->with($ancestor)->once();
            } else {
                $newObjective->shouldReceive('setAncestor')->with($objective)->once();
            }
            $this->objectiveManager
                ->shouldReceive('create')->once()
                ->andReturn($newObjective);
            $this->objectiveManager->shouldReceive('update')->once()->withArgs([$newObjective, false, false]);
        }

        foreach ($course->getLearningMaterials() as $learningMaterial) {
            $newLearningMaterial = m::mock('Ilios\CoreBundle\Entity\CourseLearningMaterial');
            $newLearningMaterial->shouldReceive('setLearningMaterial')
                ->with($learningMaterial->getLearningMaterial())->once();
            $newLearningMaterial->shouldReceive('setCourse')->with($newCourse)->once();
            $newLearningMaterial->shouldReceive('setNotes')->with($learningMaterial->getNotes())->once();
            $newLearningMaterial->shouldReceive('setPublicNotes')->with($learningMaterial->hasPublicNotes())->once();
            $newLearningMaterial->shouldReceive('setRequired')->with($learningMaterial->isRequired())->once();
            $newLearningMaterial->shouldReceive('setMeshDescriptors')
                ->with($learningMaterial->getMeshDescriptors())->once();
            $this->courseLearningMaterialManager
                ->shouldReceive('create')->once()
                ->andReturn($newLearningMaterial);
            $this->courseLearningMaterialManager->shouldReceive('update')->once()
                ->withArgs([$newLearningMaterial, false, false]);
        }

        foreach ($course->getSessions() as $session) {
            $newSession = m::mock('Ilios\CoreBundle\Entity\Session');
            $newSession->shouldReceive('setTitle')->with($session->getTitle())->once();
            $newSession->shouldReceive('setCourse')->with($newCourse)->once();
            $newSession->shouldReceive('setAttireRequired')->with($session->isAttireRequired())->once();
            $newSession->shouldReceive('setEquipmentRequired')->with($session->isEquipmentRequired())->once();
            $newSession->shouldReceive('setSessionType')->with($session->getSessionType())->once();
            $newSession->shouldReceive('setSupplemental')->with($session->isSupplemental())->once();
            $newSession->shouldReceive('setPublished')->with(false)->once();
            $newSession->shouldReceive('setPublishedAsTbd')->with(false)->once();
            $newSession->shouldReceive('setMeshDescriptors')->with($session->getMeshDescriptors())->once();
            $newSession->shouldReceive('setTerms')->with($session->getTerms())->once();
            $this->sessionManager
                ->shouldReceive('create')->once()
                ->andReturn($newSession);
            $this->sessionManager->shouldReceive('update')->withArgs([$newSession, false, false])->once();

            /** @var Objective $objective */
            foreach ($session->getObjectives() as $objective) {
                $newObjective = m::mock('Ilios\CoreBundle\Entity\Objective');
                $newObjective->shouldReceive('setTitle')->with($objective->getTitle())->once();
                $newObjective->shouldReceive('addSession')->with($newSession)->once();
                $newObjective->shouldReceive('setMeshDescriptors')->with($objective->getMeshDescriptors())->once();
                $newObjective->shouldReceive('setParents')
                    ->with(m::on(function (Collection $collection) use ($objective) {
                        return count($collection) === count($objective->getParents());
                    }))->once();
                $this->objectiveManager
                    ->shouldReceive('create')->once()
                    ->andReturn($newObjective);
                $ancestor = $objective->getAncestor();
                if ($ancestor) {
                    $newObjective->shouldReceive('setAncestor')->with($ancestor)->once();
                } else {
                    $newObjective->shouldReceive('setAncestor')->with($objective)->once();
                }
                $this->objectiveManager->shouldReceive('update')->withArgs([$newObjective, false, false]);
            }

            foreach ($session->getLearningMaterials() as $learningMaterial) {
                $newLearningMaterial = m::mock('Ilios\CoreBundle\Entity\SessionLearningMaterial');
                $newLearningMaterial->shouldReceive('setLearningMaterial')
                    ->with($learningMaterial->getLearningMaterial())->once();
                $newLearningMaterial->shouldReceive('setSession')->with($newSession)->once();
                $newLearningMaterial->shouldReceive('setNotes')->with($learningMaterial->getNotes())->once();
                $newLearningMaterial->shouldReceive('setPublicNotes')
                    ->with($learningMaterial->hasPublicNotes())->once();
                $newLearningMaterial->shouldReceive('setRequired')->with($learningMaterial->isRequired())->once();
                $newLearningMaterial->shouldReceive('setMeshDescriptors')
                    ->with($learningMaterial->getMeshDescriptors())->once();
                $this->sessionLearningMaterialManager
                    ->shouldReceive('create')->once()
                    ->andReturn($newLearningMaterial);
                $this->sessionLearningMaterialManager->shouldReceive('update')->once()
                    ->withArgs([$newLearningMaterial, false, false]);
            }

            if ($oldDescription = $session->getSessionDescription()) {
                $newDescription = m::mock('Ilios\CoreBundle\Entity\SessionDescriptionInterface');
                $newDescription->shouldReceive('setDescription')->with($oldDescription->getDescription())->once();
                $newSession->shouldReceive('setSessionDescription')->with($newDescription)->once();
                $this->sessionDescriptionManager
                    ->shouldReceive('create')->once()
                    ->andReturn($newDescription);
                $this->sessionDescriptionManager->shouldReceive('update')->once()
                    ->withArgs([$newDescription, false, false]);
            }

            if ($oldIlmSession = $session->getIlmSession()) {
                $newIlmSession = m::mock('Ilios\CoreBundle\Entity\IlmSessionInterface');
                $newIlmSession->shouldReceive('setHours')->with($oldIlmSession->getHours())->once();
                $newIlmSession->shouldReceive('setDueDate')
                    ->with(m::on(function (DateTime $newDueDate) use ($oldIlmSession) {
                        $oldDueDate = $oldIlmSession->getDueDate();
                        return (
                            //day of the week is the same
                            $oldDueDate->format('w') === $newDueDate->format('w') &&
                            //Week of the year is the same
                            $oldDueDate->format('W') === $newDueDate->format('W')
                        );
                    }))->once();
                $newSession->shouldReceive('setIlmSession')->with($newIlmSession)->once();
                $this->ilmSessionManager
                    ->shouldReceive('create')->once()
                    ->andReturn($newIlmSession);
                $this->ilmSessionManager->shouldReceive('update')->once()
                    ->withArgs([$newIlmSession, false, false]);
            }

            foreach ($session->getOfferings() as $offering) {
                $newOffering = m::mock('Ilios\CoreBundle\Entity\Offering');
                $newOffering->shouldReceive('setRoom')->once()->with($offering->getRoom());
                $newOffering->shouldReceive('setSite')->once()->with($offering->getSite());
                $newOffering->shouldReceive('setStartDate')->with(m::on(function (DateTime $newStart) use ($offering) {
                    $oldStart = $offering->getStartDate();
                    return (
                        //day of the week is the same
                        $oldStart->format('w') === $newStart->format('w') &&
                        //Week of the year is the same
                        $oldStart->format('W') === $newStart->format('W')
                    );
                }))->once();
                $newOffering->shouldReceive('setEndDate')->with(m::on(function (DateTime $newEnd) use ($offering) {
                    $oldEnd = $offering->getEndDate();
                    return (
                        //day of the week is the same
                        $oldEnd->format('w') === $newEnd->format('w') &&
                        //Week of the year is the same
                        $oldEnd->format('W') === $newEnd->format('W')
                    );
                }))->once();

                $newOffering->shouldReceive('setSession')->once()->with($newSession);
                $newOffering->shouldReceive('setInstructors')->once()->with($offering->getInstructors());
                $newOffering->shouldReceive('setInstructorGroups')->once()->with($offering->getInstructorGroups());
                $newOffering->shouldNotReceive('setLearnerGroups');
                $newOffering->shouldNotReceive('setLearners');

                $this->offeringManager->shouldReceive('create')->once()->andReturn($newOffering);
                $this->offeringManager->shouldReceive('update')->once()->withArgs([$newOffering, false, false]);
            }
        }

        $rhett = $this->service->rolloverCourse($course->getId(), $newYear, []);
        $this->assertSame($newCourse, $rhett);
    }

    public function testRolloverWithYearFarInTheFuture()
    {
        $course = $this->createTestCourseWithOfferings();


        $newCourse = m::mock('Ilios\CoreBundle\Entity\CourseInterface');
        $newCourse->shouldIgnoreMissing();
        $newYear = $this->setupCourseManager($course, $newCourse, 15);
        $newCourse->shouldReceive('setYear')->with($newYear)->once();

        $newCourse->shouldReceive('setStartDate')->with(m::on(function (DateTime $newStart) use ($course) {
            $oldStart = $course->getStartDate();
            return (
                //day of the week is the same
                $oldStart->format('w') === $newStart->format('w') &&
                //Week of the year is the same
                $oldStart->format('W') === $newStart->format('W')
            );
        }))->once();

        $newCourse->shouldReceive('setEndDate')->with(m::on(function (DateTime $newEnd) use ($course) {
            $oldEnd = $course->getEndDate();
            return (
                //day of the week is the same
                $oldEnd->format('w') === $newEnd->format('w') &&
                //Week of the year is the same
                $oldEnd->format('W') === $newEnd->format('W')
            );
        }))->once();

        foreach ($course->getSessions() as $session) {
            $newSession = m::mock('Ilios\CoreBundle\Entity\Session');
            $newSession->shouldIgnoreMissing();

            foreach ($session->getOfferings() as $offering) {
                $newOffering = m::mock('Ilios\CoreBundle\Entity\Offering');
                $newOffering->shouldIgnoreMissing();
                $newOffering->shouldReceive('setStartDate')->with(m::on(function (DateTime $newStart) use ($offering) {
                    $oldStart = $offering->getStartDate();
                    return (
                        //day of the week is the same
                        $oldStart->format('w') === $newStart->format('w') &&
                        //Week of the year is the same
                        $oldStart->format('W') === $newStart->format('W')
                    );
                }))->once();
                $newOffering->shouldReceive('setEndDate')->with(m::on(function (DateTime $newEnd) use ($offering) {
                    $oldEnd = $offering->getEndDate();
                    return (
                        //day of the week is the same
                        $oldEnd->format('w') === $newEnd->format('w') &&
                        //Week of the year is the same
                        $oldEnd->format('W') === $newEnd->format('W')
                    );
                }))->once();

                $this->offeringManager->shouldReceive('create')->once()->andReturn($newOffering);
                $this->offeringManager->shouldReceive('update')->once()->withArgs([$newOffering, false, false]);
            }

            $this->sessionManager->shouldReceive('create')->once()->andReturn($newSession);
            $this->sessionManager->shouldReceive('update')->once()->withArgs([$newSession, false, false]);
        }
        $this->service->rolloverCourse($course->getId(), $newYear, []);
    }

    public function testRolloverWithSpecificStartDate()
    {
        $course = $this->createTestCourseWithOfferings();

        $newCourse = m::mock('Ilios\CoreBundle\Entity\CourseInterface');
        $newCourse->shouldIgnoreMissing();
        $newYear = $this->setupCourseManager($course, $newCourse);

        $newCourse->shouldReceive('setYear')->with($newYear)->once();

        $newStartDate = clone $course->getStartDate();
        //start the new course two weeks later
        $newStartDate->add(new \DateInterval('P54W'));
        $this->assertEquals($course->getStartDate()->format('w'), $newStartDate->format('w'));

        $newCourse
            ->shouldReceive('setStartDate')->with(m::on(function (\DateTime $newStart) use ($course, $newStartDate) {
                $oldStart = $course->getStartDate();
                $oldStartWeekOfYear = (int) $oldStart->format('W');
                $newStartWeekOfYear = (int) $newStart->format('W');
                $weeksDiff = 0;
                if ($newStartWeekOfYear > $oldStartWeekOfYear) {
                    $weeksDiff = $newStartWeekOfYear - $oldStartWeekOfYear;
                } elseif ($newStartWeekOfYear < $oldStartWeekOfYear) {
                    /* @link http://stackoverflow.com/a/21480444 */
                    $weeksInOldYear = (int) (new DateTime("December 28th, {$oldStart->format('Y')}"))->format('W');
                    $weeksDiff = ($weeksInOldYear - $oldStartWeekOfYear) + $newStartWeekOfYear;
                }
                return (
                    $newStart->format('c') === $newStartDate->format('c')
                    // day of the week is the same
                    && $oldStart->format('w') === $newStart->format('w')
                    // dates are two weeks apart
                    && 2 === $weeksDiff
                );
            }))->once();

        $newCourse->shouldReceive('setEndDate')->with(m::on(function (DateTime $newEnd) use ($course) {
            $oldEnd = $course->getEndDate();
            $oldEndWeekOfYear = (int) $oldEnd->format('W');
            $newEndWeekOfYear = (int) $newEnd->format('W');
            $weeksDiff = 0;
            if ($newEndWeekOfYear > $oldEndWeekOfYear) {
                $weeksDiff = $newEndWeekOfYear - $oldEndWeekOfYear;
            } elseif ($newEndWeekOfYear < $oldEndWeekOfYear) {
                $weeksInOldYear = (int) (new DateTime("December 28th, {$oldEnd->format('Y')}"))->format('W');
                $weeksDiff = ($weeksInOldYear - $oldEndWeekOfYear) + $newEndWeekOfYear;
            }
            return (
                //day of the week is the same
                $oldEnd->format('w') === $newEnd->format('w')
                // dates are two weeks apart
                && 2 === $weeksDiff
            );
        }))->once();

        foreach ($course->getSessions() as $session) {
            $newSession = m::mock('Ilios\CoreBundle\Entity\Session');
            $newSession->shouldIgnoreMissing();

            foreach ($session->getOfferings() as $offering) {
                $newOffering = m::mock('Ilios\CoreBundle\Entity\Offering');
                $newOffering->shouldIgnoreMissing();
                $newOffering->shouldReceive('setStartDate')->with(m::on(function (DateTime $newStart) use ($offering) {
                    $oldStart = $offering->getStartDate();
                    $expectedStartWeek = (int) $oldStart->format('W') + 2;
                    if ($expectedStartWeek > 52) {
                        $expectedStartWeek = $expectedStartWeek - 52;
                    }
                    return (
                        //day of the week is the same
                        $oldStart->format('w') === $newStart->format('w') &&
                        //Week of the year is the same
                        $expectedStartWeek ===  (int) $newStart->format('W')
                    );
                }))->once();
                $newOffering->shouldReceive('setEndDate')->with(m::on(function (DateTime $newEnd) use ($offering) {
                    $oldEnd = $offering->getEndDate();
                    $expectedEndWeek = (int) $oldEnd->format('W') + 2;
                    if ($expectedEndWeek > 52) {
                        $expectedEndWeek = $expectedEndWeek - 52;
                    }
                    return (
                        //day of the week is the same
                        $oldEnd->format('w') === $newEnd->format('w') &&
                        //Week of the year is the same
                        $expectedEndWeek ===  (int) $newEnd->format('W')
                    );
                }))->once();

                $this->offeringManager->shouldReceive('create')->once()->andReturn($newOffering);
                $this->offeringManager->shouldReceive('update')->once()->withArgs([$newOffering, false, false]);
            }

            $this->sessionManager->shouldReceive('create')->once()->andReturn($newSession);
            $this->sessionManager->shouldReceive('update')->once()->withArgs([$newSession, false, false]);
        }
        $this->service->rolloverCourse($course->getId(), $newYear, ['new-start-date' => $newStartDate->format('c')]);
    }


    public function testRolloverWithInSameYearWithNewStartDate()
    {
        $course = $this->createTestCourseWithOfferings();

        $newCourse = m::mock('Ilios\CoreBundle\Entity\CourseInterface');
        $newCourse->shouldIgnoreMissing();

        $newYear = $course->getYear();
        $newTitle = $course->getTitle();

        $this->courseManager->shouldReceive('findOneBy')
            ->withArgs([['id' => $course->getId()]])->andReturn($course)->once();
        $this->courseManager
            ->shouldReceive('findBy')
            ->withArgs([['title' => $newTitle, 'year' => $newYear]])
            ->andReturn(false)->once();
        $this->courseManager->shouldReceive('update')->withArgs([$newCourse, false, false])->once();
        $this->courseManager
            ->shouldReceive('create')->once()
            ->andReturn($newCourse);
        $this->courseManager->shouldReceive('flushAndClear')->once();

        $newCourse->shouldReceive('setYear')->with($newYear)->once();
        $newCourse->shouldReceive('setTitle')->with($newTitle)->once();

        $newStartDate = clone $course->getStartDate();
        //start the new course 16 weeks (112 days) later
        $newStartDate->add(new \DateInterval('P112D'));

        $newCourse
            ->shouldReceive('setStartDate')->with(m::on(function (DateTime $newStart) use ($course, $newStartDate) {
                $oldStart = $course->getStartDate();
                $expectedStartWeek = (int) $oldStart->format('W') + 16;
                if ($expectedStartWeek > 52) {
                    $expectedStartWeek = $expectedStartWeek - 52;
                }
                return (
                    $newStart->format('c') === $newStartDate->format('c') &&
                    //day of the week is the same
                    $oldStart->format('w') === $newStart->format('w') &&
                    //Week of the year is two weeks later
                    $expectedStartWeek ===  (int) $newStart->format('W')
                );
            }))->once();

        $newCourse->shouldReceive('setEndDate')->with(m::on(function (DateTime $newEnd) use ($course) {
            $oldEnd = $course->getEndDate();
            $expectedEndWeek = (int) $oldEnd->format('W') + 16;
            if ($expectedEndWeek > 52) {
                $expectedEndWeek = $expectedEndWeek - 52;
            }
            return (
                //day of the week is the same
                $oldEnd->format('w') === $newEnd->format('w') &&
                //Week of the year is two weeks laters
                $expectedEndWeek ===  (int) $newEnd->format('W')
            );
        }))->once();

        foreach ($course->getSessions() as $session) {
            $newSession = m::mock('Ilios\CoreBundle\Entity\Session');
            $newSession->shouldIgnoreMissing();

            foreach ($session->getOfferings() as $offering) {
                $newOffering = m::mock('Ilios\CoreBundle\Entity\Offering');
                $newOffering->shouldIgnoreMissing();
                $newOffering->shouldReceive('setStartDate')->with(m::on(function (DateTime $newStart) use ($offering) {
                    $oldStart = $offering->getStartDate();
                    $expectedStartWeek = (int) $oldStart->format('W') + 16;
                    if ($expectedStartWeek > 52) {
                        $expectedStartWeek = $expectedStartWeek - 52;
                    }
                    return (
                        //day of the week is the same
                        $oldStart->format('w') === $newStart->format('w') &&
                        //Week of the year is the same
                        $expectedStartWeek ===  (int) $newStart->format('W')
                    );
                }))->once();
                $newOffering->shouldReceive('setEndDate')->with(m::on(function (DateTime $newEnd) use ($offering) {
                    $oldEnd = $offering->getEndDate();
                    $expectedEndWeek = (int) $oldEnd->format('W') + 16;
                    if ($expectedEndWeek > 52) {
                        $expectedEndWeek = $expectedEndWeek - 52;
                    }
                    return (
                        //day of the week is the same
                        $oldEnd->format('w') === $newEnd->format('w') &&
                        //Week of the year is the same
                        $expectedEndWeek ===  (int) $newEnd->format('W')
                    );
                }))->once();

                $this->offeringManager->shouldReceive('create')->once()->andReturn($newOffering);
                $this->offeringManager->shouldReceive('update')->once()->withArgs([$newOffering, false, false]);
            }

            $this->sessionManager->shouldReceive('create')->once()->andReturn($newSession);
            $this->sessionManager->shouldReceive('update')->once()->withArgs([$newSession, false, false]);
        }
        $this->service->rolloverCourse($course->getId(), $newYear, ['new-start-date' => $newStartDate->format('c')]);
    }

    public function testRolloverSessionObjectiveWithOrphanedParents()
    {
        $course = $this->createTestCourse();
        $course->setSchool(new School());

        $courseObjective = new Objective();
        $courseObjective->setId(13);
        $courseObjective->setTitle('test');
        $course->addObjective($courseObjective);
        $this->objectiveManager->shouldReceive('create')->once()->andReturn(new Objective());

        $session = new Session();
        $session->setSessionType(new SessionType());
        $sessionObjective = new Objective();
        $sessionObjective->addParent(new Objective());
        $sessionObjective->addParent($courseObjective);
        $session->addObjective($sessionObjective);
        $course->addSession($session);

        $newCourse = m::mock('Ilios\CoreBundle\Entity\CourseInterface');
        $newCourse->shouldIgnoreMissing();
        $newYear = $this->setupCourseManager($course, $newCourse);

        $newSession = m::mock('Ilios\CoreBundle\Entity\Session');
        $newSession->shouldIgnoreMissing();
        $newObjective = m::mock('Ilios\CoreBundle\Entity\Objective');
        $newObjective->shouldIgnoreMissing();

        $self = $this;
        //We should end up with 1 parent since the other one is an orphan
        $newObjective->shouldReceive('setParents')
            ->with(m::on(function (Collection $collection) use ($self) {
                $this->assertEquals($collection->count(), 1);
                $this->assertEquals('test', $collection->first()->getTitle());
                return count($collection) === 1;
            }));
        $this->objectiveManager->shouldReceive('create')->once()->andReturn($newObjective);
        $this->sessionManager->shouldReceive('create')->once()->andReturn($newSession);

        $this->sessionManager->shouldIgnoreMissing();
        $this->objectiveManager->shouldIgnoreMissing();

        $rhett = $this->service->rolloverCourse($course->getId(), $newYear, ['']);
        $this->assertSame($newCourse, $rhett);
    }

    public function testRolloverInSameYearKeepsRelationships()
    {
        $course = $this->createTestCourseWithAssociations();
        $newCourse = m::mock('Ilios\CoreBundle\Entity\CourseInterface');
        $newYear = $course->getYear();
        $newTitle = $course->getTitle() . ' again';
        $this->courseManager->shouldReceive('findOneBy')
            ->withArgs([['id' => $course->getId()]])->andReturn($course)->once();
        $this->courseManager
            ->shouldReceive('findBy')
            ->withArgs([['title' => $newTitle, 'year' => $newYear]])
            ->andReturn(false)->once();
        $this->courseManager->shouldReceive('update')->withArgs([$newCourse, false, false])->once();

        $this->courseManager
            ->shouldReceive('create')->once()
            ->andReturn($newCourse);

        $this->courseManager->shouldReceive('flushAndClear')->once();
        $newCourse->shouldReceive()->setCohorts()->with($course->getCohorts());
        $newCourse->shouldIgnoreMissing();

        foreach ($course->getObjectives() as $objective) {
            $newObjective = m::mock('Ilios\CoreBundle\Entity\Objective');
            $newObjective->shouldReceive('setTitle')->with($objective->getTitle())->once();
            $newObjective->shouldReceive('addCourse')->with($newCourse)->once();
            $newObjective->shouldReceive('setMeshDescriptors')->with($objective->getMeshDescriptors())->once();
            $newObjective->shouldReceive('setParents')->with($objective->getParents());
            $newObjective->shouldReceive('setAncestor')->with($objective->getAncestorOrSelf())->once();
            $this->objectiveManager->shouldReceive('create')->once()->andReturn($newObjective);
            $this->objectiveManager->shouldReceive('update')->once()->withArgs([$newObjective, false, false]);
        }

        foreach ($course->getLearningMaterials() as $learningMaterial) {
            $newLearningMaterial = m::mock('Ilios\CoreBundle\Entity\CourseLearningMaterial');
            $newLearningMaterial->shouldIgnoreMissing();
            $this->courseLearningMaterialManager->shouldReceive('create')->once()->andReturn($newLearningMaterial);
            $this->courseLearningMaterialManager->shouldIgnoreMissing();
        }

        foreach ($course->getSessions() as $session) {
            $newSession = m::mock('Ilios\CoreBundle\Entity\Session');
            $newSession->shouldIgnoreMissing();
            $this->sessionManager
                ->shouldReceive('create')->once()
                ->andReturn($newSession);
            $this->sessionManager->shouldReceive('update')->withArgs([$newSession, false, false])->once();

            foreach ($session->getObjectives() as $objective) {
                $newObjective = m::mock('Ilios\CoreBundle\Entity\Objective');
                $newObjective->shouldIgnoreMissing();
                $this->objectiveManager->shouldReceive('create')->once()->andReturn($newObjective);
                $this->objectiveManager->shouldIgnoreMissing();
            }

            foreach ($session->getLearningMaterials() as $learningMaterial) {
                $newLearningMaterial = m::mock('Ilios\CoreBundle\Entity\SessionLearningMaterial');
                $newLearningMaterial->shouldIgnoreMissing();
                $this->sessionLearningMaterialManager->shouldReceive('create')->once()->andReturn($newLearningMaterial);
                $this->sessionLearningMaterialManager->shouldIgnoreMissing();
            }

            if ($oldDescription = $session->getSessionDescription()) {
                $newDescription = m::mock('Ilios\CoreBundle\Entity\SessionDescriptionInterface');
                $newDescription->shouldReceive('setDescription')->with($oldDescription->getDescription())->once();
                $newSession->shouldReceive('setSessionDescription')->with($newDescription)->once();
                $this->sessionDescriptionManager
                    ->shouldReceive('create')->once()
                    ->andReturn($newDescription);
                $this->sessionDescriptionManager->shouldReceive('update')->once()
                    ->withArgs([$newDescription, false, false]);
            }

            if ($oldIlmSession = $session->getIlmSession()) {
                $newIlmSession = m::mock('Ilios\CoreBundle\Entity\IlmSessionInterface');
                $newIlmSession->shouldReceive('setHours')->with($oldIlmSession->getHours())->once();
                $newIlmSession->shouldReceive('setDueDate')
                    ->with(m::on(function (DateTime $newDueDate) use ($oldIlmSession) {
                        $oldDueDate = $oldIlmSession->getDueDate();
                        return (
                            //day of the week is the same
                            $oldDueDate->format('w') === $newDueDate->format('w') &&
                            //Week of the year is the same
                            $oldDueDate->format('W') === $newDueDate->format('W')
                        );
                    }))->once();
                $newSession->shouldReceive('setIlmSession')->with($newIlmSession)->once();
                $this->ilmSessionManager
                    ->shouldReceive('create')->once()
                    ->andReturn($newIlmSession);
                $this->ilmSessionManager->shouldReceive('update')->once()
                    ->withArgs([$newIlmSession, false, false]);
            }

            foreach ($session->getOfferings() as $offering) {
                $newOffering = m::mock('Ilios\CoreBundle\Entity\Offering');
                $newOffering->shouldReceive('setRoom')->once()->with($offering->getRoom());
                $newOffering->shouldReceive('setSite')->once()->with($offering->getSite());
                $newOffering->shouldReceive('setStartDate')->with(m::on(function (DateTime $newStart) use ($offering) {
                    $oldStart = $offering->getStartDate();
                    return (
                        //day of the week is the same
                        $oldStart->format('w') === $newStart->format('w') &&
                        //Week of the year is the same
                        $oldStart->format('W') === $newStart->format('W')
                    );
                }))->once();
                $newOffering->shouldReceive('setEndDate')->with(m::on(function (DateTime $newEnd) use ($offering) {
                    $oldEnd = $offering->getEndDate();
                    return (
                        //day of the week is the same
                        $oldEnd->format('w') === $newEnd->format('w') &&
                        //Week of the year is the same
                        $oldEnd->format('W') === $newEnd->format('W')
                    );
                }))->once();

                $newOffering->shouldReceive('setSession')->once()->with($newSession);
                $newOffering->shouldReceive('setInstructors')->once()->with($offering->getInstructors());
                $newOffering->shouldReceive('setInstructorGroups')->once()->with($offering->getInstructorGroups());
                $newOffering->shouldNotReceive('setLearnerGroups');
                $newOffering->shouldNotReceive('setLearners');
                $this->offeringManager->shouldReceive('create')->once()->andReturn($newOffering);
                $this->offeringManager->shouldReceive('update')->once()->withArgs([$newOffering, false, false]);
            }
        }
        $this->objectiveManager->shouldIgnoreMissing();

        $rhett = $this->service->rolloverCourse($course->getId(), $newYear, ['new-course-title' => $newTitle]);

        $this->assertSame($newCourse, $rhett);
    }

    public function testRolloverWithEmptyClerkshipType()
    {
        $course = $this->createTestCourse();
        $course->setSchool(new School());
        $newCourse = m::mock('Ilios\CoreBundle\Entity\CourseInterface');
        $newCourse->shouldIgnoreMissing();
        $newCourse->shouldNotReceive('setClerkshipType');
        $newYear = $this->setupCourseManager($course, $newCourse);

        $this->service->rolloverCourse($course->getId(), $newYear, ['']);
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
        $course = $this->createTestCourse();
        $course->setSchool(new School());
        $course->addLearningMaterial(new CourseLearningMaterial());
        $newCourse = m::mock('Ilios\CoreBundle\Entity\CourseInterface');
        $newCourse->shouldIgnoreMissing();
        $newCourse->shouldNotReceive('addLearningMaterial');
        $this->courseLearningMaterialManager->shouldNotReceive('create');
        $newYear = $this->setupCourseManager($course, $newCourse);

        $this->service->rolloverCourse($course->getId(), $newYear, ['skip-course-learning-materials' => true]);
    }

    public function testRolloverWithoutCourseObjectives()
    {
        $course = $this->createTestCourse();
        $course->setSchool(new School());
        $course->addObjective(new Objective());
        $newCourse = m::mock('Ilios\CoreBundle\Entity\CourseInterface');
        $newCourse->shouldIgnoreMissing();
        $newCourse->shouldNotReceive('addObjective');
        $this->objectiveManager->shouldNotReceive('create');
        $newYear = $this->setupCourseManager($course, $newCourse);

        $this->service->rolloverCourse($course->getId(), $newYear, ['skip-course-objectives' => true]);
    }

    public function testRolloverWithoutCourseTerms()
    {
        $course = $this->createTestCourse();
        $course->setSchool(new School());
        $course->addTerm(new Term());
        $newCourse = m::mock('Ilios\CoreBundle\Entity\CourseInterface');
        $newCourse->shouldIgnoreMissing();
        $newCourse->shouldNotReceive('setTerms');
        $newYear = $this->setupCourseManager($course, $newCourse);

        $this->service->rolloverCourse($course->getId(), $newYear, ['skip-course-terms' => true]);
    }

    public function testRolloverWithoutCourseMesh()
    {
        $course = $this->createTestCourse();
        $course->setSchool(new School());
        $course->addMeshDescriptor(new MeshDescriptor());
        $newCourse = m::mock('Ilios\CoreBundle\Entity\CourseInterface');
        $newCourse->shouldIgnoreMissing();
        $newCourse->shouldNotReceive('setMeshDescriptors');
        $newYear = $this->setupCourseManager($course, $newCourse);

        $this->service->rolloverCourse($course->getId(), $newYear, ['skip-course-mesh' => true]);
    }

    public function testRolloverWithoutCourseAncestor()
    {
        $course = $this->createTestCourse();
        $course->setSchool(new School());
        $newCourse = m::mock('Ilios\CoreBundle\Entity\CourseInterface');
        $newCourse->shouldIgnoreMissing();
        $newCourse->shouldReceive('setAncestor')->with($course)->once();
        $newYear = $this->setupCourseManager($course, $newCourse);

        $this->service->rolloverCourse($course->getId(), $newYear, 1);
    }

    public function testRolloverWithoutSessionLearningMaterials()
    {
        $this->markTestIncomplete();
    }

    public function testRolloverWithoutSessionObjectives()
    {
        $this->markTestIncomplete();
    }

    public function testRolloverWithoutSessionTerms()
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

        $this->expectException(
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

        $this->expectException(
            \Exception::class,
            "Courses cannot be rolled over to a new year before"
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

        $this->expectException(\Exception::class, "There are no courses with courseId {$courseId}.");

        $this->service->rolloverCourse($courseId, $year, []);
    }

    public function testRolloverFailsOnStartDateOnDifferentDay()
    {
        $course = $this->createTestCourse();
        $course->setSchool(new School());

        $newCourse = m::mock('Ilios\CoreBundle\Entity\CourseInterface');
        $newCourse->shouldIgnoreMissing();
        $newYear = $course->getYear() + 1;
        $this->courseManager->shouldReceive('findOneBy')
            ->withArgs([['id' => $course->getId()]])->andReturn($course)->once();
        $this->courseManager
            ->shouldReceive('findBy')
            ->withArgs([['title' => $course->getTitle(), 'year' => $newYear]])
            ->andReturn(false)->once();

        $newStartDate = clone $course->getStartDate();
        $newStartDate->add(new \DateInterval('P1Y2D'));

        $this->expectException(
            \Exception::class,
            "The new start date must take place on the same day of the week as the original course start date"
        );
        $this->service->rolloverCourse($course->getId(), $newYear, ['new-start-date' => $newStartDate->format('c')]);
    }

    /**
     * Gets a basic filled out course
     *
     * @return Course
     */
    protected function createTestCourse()
    {
        $course = new Course();
        $course->setId(10);
        $course->setTitle('test course');
        $course->setLevel(1);
        $now = new DateTime();
        $course->setYear((int) $now->format('Y'));
        $course->setStartDate(new DateTime('yesterday'));
        $course->setEndDate(new DateTime('tomorrow'));
        $course->setExternalId('I45');
        $course->setLocked(true);
        $course->setArchived(true);
        $course->setPublished(true);
        $course->setPublishedAsTbd(true);

        return $course;
    }

    /**
     * Gets a course with a bunch of relationsips attached
     * @return Course
     */
    protected function createTestCourseWithAssociations()
    {
        $course = $this->createTestCourse();

        $course->setClerkshipType(new CourseClerkshipType());
        $course->setSchool(new School());

        $ancestorCourse = new Course();
        $ancestorCourse->setId(1);
        $ancestorCourse->setTitle('test ancestor course');
        $course->setAncestor($ancestorCourse);

        $ancestorObjective = new Objective();
        $ancestorObjective->setId(1);
        $ancestorObjective->setTitle('test ancestor objective');

        $courseObjective1 = new Objective();
        $courseObjective1->setId(808);
        $courseObjective1->setTitle('test course objective1');
        $courseObjective1->addMeshDescriptor(new MeshDescriptor());
        $courseObjective1->addParent(new Objective());
        $course->addObjective($courseObjective1);
        $courseObjective2 = new Objective();
        $courseObjective2->setId(42);
        $courseObjective2->setTitle('test course objective2');
        $courseObjective2->setAncestor($ancestorObjective);
        $course->addObjective($courseObjective2);

        $courseTerm1 = new Term();
        $courseTerm1->setId(808);
        $course->addTerm($courseTerm1);

        $lm = new LearningMaterial();

        $courseLearningMaterial1 = new CourseLearningMaterial();
        $courseLearningMaterial1->setLearningMaterial($lm);
        $courseLearningMaterial1->setId(808);
        $courseLearningMaterial1->addMeshDescriptor(new MeshDescriptor());
        $courseLearningMaterial1->setNotes('notes');
        $courseLearningMaterial1->setPublicNotes(true);
        $courseLearningMaterial1->setRequired(false);
        $course->addLearningMaterial($courseLearningMaterial1);

        $course->addCohort(new Cohort());

        $session1 = new Session();
        $session1->setSessionType(new SessionType());

        $sessionAncestor = new Objective();
        $sessionAncestor->setId(2);
        $sessionAncestor->setTitle('test session ancestor');

        $sessionObjective1 = new Objective();
        $sessionObjective1->setId(99);
        $sessionObjective1->setTitle('test session objective 1');
        $sessionObjective1->addMeshDescriptor(new MeshDescriptor());
        $sessionObjective1->addParent($courseObjective1);
        $sessionObjective1->addParent($courseObjective2);
        $session1->addObjective($sessionObjective1);

        $sessionObjective2 = new Objective();
        $sessionObjective2->setId(9);
        $sessionObjective2->setTitle('test session objective 2');
        $sessionObjective2->addMeshDescriptor(new MeshDescriptor());
        $sessionObjective2->addParent($courseObjective1);
        $sessionObjective2->setAncestor($sessionAncestor);
        $session1->addObjective($sessionObjective2);

        $sessionLearningMaterial1 = new SessionLearningMaterial();
        $sessionLearningMaterial1->setLearningMaterial($lm);
        $sessionLearningMaterial1->setId(808);
        $sessionLearningMaterial1->addMeshDescriptor(new MeshDescriptor());
        $sessionLearningMaterial1->setNotes('notes');
        $sessionLearningMaterial1->setPublicNotes(true);
        $sessionLearningMaterial1->setRequired(false);
        $session1->addLearningMaterial($sessionLearningMaterial1);

        $sessionTerm1 = new Term();
        $sessionTerm1->setId(808);
        $session1->addTerm($sessionTerm1);

        $description = new SessionDescription();
        $description->setDescription('test description');
        $session1->setSessionDescription($description);

        $user = new User();

        $offering1 = new Offering();
        $offering1->setRoom('111b');
        $offering1->setSite('Off Campus');
        $offering1->setStartDate(new DateTime('8am'));
        $offering1->setEndDate(new DateTime('9am'));
        $offering1->addInstructor($user);
        $offering1->addLearner($user);

        $instructorGroup = new InstructorGroup();
        $instructorGroup->addUser($user);
        $offering1->addInstructorGroup($instructorGroup);

        $learnerGroup = new LearnerGroup();
        $learnerGroup->addUser($user);
        $offering1->addLearnerGroup($learnerGroup);

        $session1->addOffering($offering1);

        $course->addSession($session1);

        $session2 = new Session();
        $session2->setSessionType(new SessionType());
        $ilm = new IlmSession();
        $ilm->setHours(4.3);
        $ilm->setDueDate(new DateTime());
        $ilm->addInstructorGroup($instructorGroup);
        $ilm->addLearnerGroup($learnerGroup);
        $ilm->addInstructor($user);
        $ilm->addLearner($user);
        $session2->setIlmSession($ilm);

        $course->addSession($session2);

        return $course;
    }

    /**
     * Gets a course with a few offerings to use in date testing
     *
     * @return Course
     */
    protected function createTestCourseWithOfferings()
    {
        $course = $this->createTestCourse();
        $course->setSchool(new School());
        $session = new Session();
        $session->setSessionType(new SessionType());
        $offering1 = new Offering();
        $offering1->setStartDate(new DateTime('8am'));
        $offering1->setEndDate(new DateTime('9am'));
        $session->addOffering($offering1);

        $offering2 = new Offering();
        $offering2->setStartDate(new DateTime('1pm tomorrow'));
        $offering2->setEndDate(new DateTime('10am next week'));
        $session->addOffering($offering2);

        $course->addSession($session);

        return $course;
    }

    /**
     * Setup the course manager mock to do basic stuff we need in most tests
     *
     * @param CourseInterface $course
     * @param CourseInterface $newCourse
     * @param integer $interval the length of time in the future for the new academic year
     *
     * @return int
     */
    protected function setupCourseManager(CourseInterface $course, CourseInterface $newCourse, $interval = 1)
    {
        $newYear = $course->getYear() + $interval;
        $this->courseManager->shouldReceive('findOneBy')
            ->withArgs([['id' => $course->getId()]])->andReturn($course)->once();
        $this->courseManager
            ->shouldReceive('findBy')
            ->withArgs([['title' => $course->getTitle(), 'year' => $newYear]])
            ->andReturn(false)->once();
        $this->courseManager->shouldReceive('update')->withArgs([$newCourse, false, false])->once();

        $this->courseManager
            ->shouldReceive('create')->once()
            ->andReturn($newCourse);

        $this->courseManager->shouldReceive('flushAndClear')->once();

        return $newYear;
    }
}
