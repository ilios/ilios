<?php

declare(strict_types=1);

namespace App\Tests\Service;

use App\Entity\Cohort;
use App\Entity\Course;
use App\Entity\CourseClerkshipType;
use App\Entity\CourseInterface;
use App\Entity\CourseLearningMaterial;
use App\Entity\CourseLearningMaterialInterface;
use App\Entity\CourseObjective;
use App\Entity\CourseObjectiveInterface;
use App\Entity\IlmSession;
use App\Entity\IlmSessionInterface;
use App\Entity\InstructorGroup;
use App\Entity\LearnerGroup;
use App\Entity\LearningMaterial;
use App\Entity\MeshDescriptor;
use App\Entity\Offering;
use App\Entity\OfferingInterface;
use App\Entity\ProgramYear;
use App\Entity\ProgramYearObjective;
use App\Entity\School;
use App\Entity\Session;
use App\Entity\SessionInterface;
use App\Entity\SessionLearningMaterial;
use App\Entity\SessionLearningMaterialInterface;
use App\Entity\SessionObjective;
use App\Entity\SessionObjectiveInterface;
use App\Entity\SessionType;
use App\Entity\Term;
use App\Entity\User;
use App\Repository\CohortRepository;
use App\Repository\CourseLearningMaterialRepository;
use App\Repository\CourseObjectiveRepository;
use App\Repository\CourseRepository;
use App\Repository\IlmSessionRepository;
use App\Repository\LearningMaterialRepository;
use App\Repository\OfferingRepository;
use App\Repository\SessionLearningMaterialRepository;
use App\Repository\SessionObjectiveRepository;
use App\Repository\SessionRepository;
use App\Service\CourseRollover;
use App\Tests\TestCase;
use DateInterval;
use DateTime;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Exception;
use Mockery as m;

/**
 * Class CourseRolloverTest
 */
final class CourseRolloverTest extends TestCase
{
    protected m\MockInterface $courseRepository;
    protected m\MockInterface $learningMaterialRepository;
    protected m\MockInterface $courseLearningMaterialRepository;
    protected m\MockInterface $sessionRepository;
    protected m\MockInterface $sessionLearningMaterialRepository;
    protected m\MockInterface $offeringRepository;
    protected m\MockInterface $ilmSessionRepository;
    protected m\MockInterface $cohortRepository;
    protected m\MockInterface $courseObjectiveRepository;
    protected m\MockInterface $sessionObjectiveRepository;
    protected CourseRollover $service;


    public function setUp(): void
    {
        parent::setUp();
        $this->courseRepository = m::mock(CourseRepository::class);
        $this->learningMaterialRepository = m::mock(LearningMaterialRepository::class);
        $this->courseLearningMaterialRepository = m::mock(CourseLearningMaterialRepository::class);
        $this->sessionRepository = m::mock(SessionRepository::class);
        $this->sessionLearningMaterialRepository = m::mock(SessionLearningMaterialRepository::class);
        $this->offeringRepository = m::mock(OfferingRepository::class);
        $this->ilmSessionRepository = m::mock(IlmSessionRepository::class);
        $this->cohortRepository = m::mock(CohortRepository::class);
        $this->sessionObjectiveRepository = m::mock(SessionObjectiveRepository::class);
        $this->courseObjectiveRepository = m::mock(CourseObjectiveRepository::class);
        $this->service = new CourseRollover(
            $this->courseRepository,
            $this->learningMaterialRepository,
            $this->courseLearningMaterialRepository,
            $this->sessionRepository,
            $this->sessionLearningMaterialRepository,
            $this->offeringRepository,
            $this->ilmSessionRepository,
            $this->cohortRepository,
            $this->courseObjectiveRepository,
            $this->sessionObjectiveRepository
        );
    }

    public function tearDown(): void
    {
        parent::tearDown();
        unset($this->courseRepository);
        unset($this->learningMaterialRepository);
        unset($this->courseLearningMaterialRepository);
        unset($this->sessionRepository);
        unset($this->sessionLearningMaterialRepository);
        unset($this->offeringRepository);
        unset($this->ilmSessionRepository);
        unset($this->cohortRepository);
        unset($this->courseObjectiveRepository);
        unset($this->sessionObjectiveRepository);
        unset($this->service);
    }

    public function testRolloverWithEverything(): void
    {
        $course = $this->createTestCourseWithAssociations();
        $newCourse = m::mock(CourseInterface::class);
        $newYear = $this->setupCourseRepository($course, $newCourse);

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
        $newCourse->shouldReceive('setAdministrators')->with($course->getAdministrators())->once();
        $newCourse->shouldReceive('setTerms')->with($course->getTerms())->once();
        $newCourse->shouldReceive('setMeshDescriptors')->with($course->getMeshDescriptors())->once();

        $ancestor = $course->getAncestor();
        $newCourse->shouldReceive('setAncestor')->with($ancestor)->once();

        /** @var CourseObjectiveInterface $courseObjective */
        foreach ($course->getCourseObjectives() as $courseObjective) {
            $newCourseObjective = m::mock(CourseObjectiveInterface::class);
            $newCourseObjective->shouldReceive('setTitle')->with($courseObjective->getTitle())->once();
            $newCourseObjective->shouldReceive('setMeshDescriptors')
                ->with($courseObjective->getMeshDescriptors())->once();
            $newCourseObjective->shouldReceive('setPosition')->with($courseObjective->getPosition())->once();
            $newCourseObjective->shouldReceive('setCourse')->with($newCourse)->once();
            $newCourseObjective->shouldReceive('setTerms')->with($courseObjective->getTerms())->once();

            $ancestor = $courseObjective->getAncestor();
            if ($ancestor) {
                $newCourseObjective->shouldReceive('setAncestor')->with($ancestor)->once();
            } else {
                $newCourseObjective->shouldReceive('setAncestor')->with($courseObjective)->once();
            }

            $this->courseObjectiveRepository
                ->shouldReceive('create')->once()
                ->andReturn($newCourseObjective);
            $this->courseObjectiveRepository->shouldReceive('update')
                ->once()->withArgs([$newCourseObjective, false, false]);
        }

        foreach ($course->getLearningMaterials() as $learningMaterial) {
            $newLearningMaterial = m::mock(CourseLearningMaterialInterface::class);
            $newLearningMaterial->shouldReceive('setLearningMaterial')
                ->with($learningMaterial->getLearningMaterial())->once();
            $newLearningMaterial->shouldReceive('setCourse')->with($newCourse)->once();
            $newLearningMaterial->shouldReceive('setNotes')->with($learningMaterial->getNotes())->once();
            $newLearningMaterial->shouldReceive('setPublicNotes')->with($learningMaterial->hasPublicNotes())->once();
            $newLearningMaterial->shouldReceive('setRequired')->with($learningMaterial->isRequired())->once();
            $newLearningMaterial->shouldReceive('setMeshDescriptors')
                ->with($learningMaterial->getMeshDescriptors())->once();
            $newLearningMaterial->shouldReceive('setPosition')->with($learningMaterial->getPosition())->once();

            $this->courseLearningMaterialRepository
                ->shouldReceive('create')->once()
                ->andReturn($newLearningMaterial);
            $this->courseLearningMaterialRepository->shouldReceive('update')->once()
                ->withArgs([$newLearningMaterial, false, false]);
        }

        /** @var SessionInterface $session */
        foreach ($course->getSessions() as $session) {
            $newSession = m::mock(SessionInterface::class);
            $newSession->shouldReceive('setTitle')->with($session->getTitle())->once();
            $newSession->shouldReceive('setDescription')->with($session->getDescription())->once();
            $newSession->shouldReceive('setCourse')->with($newCourse)->once();
            $newSession->shouldReceive('setAttireRequired')->with($session->isAttireRequired())->once();
            $newSession->shouldReceive('setEquipmentRequired')->with($session->isEquipmentRequired())->once();
            $newSession->shouldReceive('setSessionType')->with($session->getSessionType())->once();
            $newSession->shouldReceive('setSupplemental')->with($session->isSupplemental())->once();
            $newSession->shouldReceive('setPublished')->with(false)->once();
            $newSession->shouldReceive('setPublishedAsTbd')->with(false)->once();
            $newSession->shouldReceive('setInstructionalNotes')->with($session->getInstructionalNotes())->once();
            $newSession->shouldReceive('setMeshDescriptors')->with($session->getMeshDescriptors())->once();
            $newSession->shouldReceive('setTerms')->with($session->getTerms())->once();
            $this->sessionRepository
                ->shouldReceive('create')->once()
                ->andReturn($newSession);
            $this->sessionRepository->shouldReceive('update')->withArgs([$newSession, false, false])->once();

            /** @var SessionObjectiveInterface $sessionObjective */
            foreach ($session->getSessionObjectives() as $sessionObjective) {
                $newSessionObjective = m::mock(SessionObjectiveInterface::class);
                $newSessionObjective->shouldReceive('setTitle')->with($sessionObjective->getTitle())->once();
                $newSessionObjective->shouldReceive('setMeshDescriptors')
                    ->with($sessionObjective->getMeshDescriptors())->once();
                $newSessionObjective->shouldReceive('setPosition')->with($sessionObjective->getPosition())->once();
                $newSessionObjective->shouldReceive('setSession')->with($newSession)->once();
                $newSessionObjective->shouldReceive('setTerms')->with($sessionObjective->getTerms())->once();
                $newSessionObjective->shouldReceive('setCourseObjectives')->with(m::on(
                    fn(Collection $collection) => count($collection) === count($sessionObjective->getCourseObjectives())
                ))->once();
                $ancestor = $sessionObjective->getAncestor();
                if ($ancestor) {
                    $newSessionObjective->shouldReceive('setAncestor')->with($ancestor)->once();
                } else {
                    $newSessionObjective->shouldReceive('setAncestor')->with($sessionObjective)->once();
                }

                $this->sessionObjectiveRepository
                    ->shouldReceive('create')->once()
                    ->andReturn($newSessionObjective);
                $this->sessionObjectiveRepository->shouldReceive('update')
                    ->withArgs([$newSessionObjective, false, false]);
            }

            foreach ($session->getLearningMaterials() as $learningMaterial) {
                $newLearningMaterial = m::mock(SessionLearningMaterialInterface::class);
                $newLearningMaterial->shouldReceive('setLearningMaterial')
                    ->with($learningMaterial->getLearningMaterial())->once();
                $newLearningMaterial->shouldReceive('setSession')->with($newSession)->once();
                $newLearningMaterial->shouldReceive('setNotes')->with($learningMaterial->getNotes())->once();
                $newLearningMaterial->shouldReceive('setPublicNotes')
                    ->with($learningMaterial->hasPublicNotes())->once();
                $newLearningMaterial->shouldReceive('setRequired')->with($learningMaterial->isRequired())->once();
                $newLearningMaterial->shouldReceive('setPosition')->with($learningMaterial->getPosition())->once();

                $newLearningMaterial->shouldReceive('setMeshDescriptors')
                    ->with($learningMaterial->getMeshDescriptors())->once();
                $this->sessionLearningMaterialRepository
                    ->shouldReceive('create')->once()
                    ->andReturn($newLearningMaterial);
                $this->sessionLearningMaterialRepository->shouldReceive('update')->once()
                    ->withArgs([$newLearningMaterial, false, false]);
            }

            if ($oldIlmSession = $session->getIlmSession()) {
                $newIlmSession = m::mock(IlmSessionInterface::class);
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
                $this->ilmSessionRepository
                    ->shouldReceive('create')->once()
                    ->andReturn($newIlmSession);
                $this->ilmSessionRepository->shouldReceive('update')->once()
                    ->withArgs([$newIlmSession, false, false]);
            }

            foreach ($session->getOfferings() as $offering) {
                $newOffering = m::mock(OfferingInterface::class);
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

                $this->offeringRepository->shouldReceive('create')->once()->andReturn($newOffering);
                $this->offeringRepository->shouldReceive('update')->once()->withArgs([$newOffering, false, false]);
            }
        }

        $newCourse->shouldReceive('getCohorts')->once()->andReturn(new ArrayCollection());
        $rhett = $this->service->rolloverCourse($course->getId(), $newYear, []);
        $this->assertSame($newCourse, $rhett);
    }

    public function testRolloverWithYearFarInTheFuture(): void
    {
        $course = $this->createTestCourseWithOfferings();


        $newCourse = m::mock(CourseInterface::class);
        $newCourse->shouldIgnoreMissing();
        $newYear = $this->setupCourseRepository($course, $newCourse, 15);
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
            $newSession = m::mock(SessionInterface::class);
            $newSession->shouldIgnoreMissing();

            foreach ($session->getOfferings() as $offering) {
                $newOffering = m::mock(OfferingInterface::class);
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

                $this->offeringRepository->shouldReceive('create')->once()->andReturn($newOffering);
                $this->offeringRepository->shouldReceive('update')->once()->withArgs([$newOffering, false, false]);
            }

            $this->sessionRepository->shouldReceive('create')->once()->andReturn($newSession);
            $this->sessionRepository->shouldReceive('update')->once()->withArgs([$newSession, false, false]);
        }
        $this->service->rolloverCourse($course->getId(), $newYear, []);
    }

    public function testRolloverWithSpecificStartDate(): void
    {
        $course = $this->createTestCourseWithOfferings();

        $newCourse = m::mock(CourseInterface::class);
        $newCourse->shouldIgnoreMissing();
        $newYear = $this->setupCourseRepository($course, $newCourse);

        $newCourse->shouldReceive('setYear')->with($newYear)->once();

        $newStartDate = clone $course->getStartDate();
        $newStartDate->setISODate(
            $newYear,
            (int) $course->getStartDate()->format('W') + 2,
            (int) $course->getStartDate()->format('N')
        );
        $this->assertEquals($course->getStartDate()->format('w'), $newStartDate->format('w'));

        $newCourse
            ->shouldReceive('setStartDate')->with(m::on(function (DateTime $newStart) use ($course, $newStartDate) {
                $oldStart = $course->getStartDate();
                $oldStartWeekOfYear = (int) $oldStart->format('W');
                $newStartWeekOfYear = (int) $newStart->format('W');
                $weeksDiff = 0;
                if ($newStartWeekOfYear > $oldStartWeekOfYear) {
                    $weeksDiff = $newStartWeekOfYear - $oldStartWeekOfYear;
                } elseif ($newStartWeekOfYear < $oldStartWeekOfYear) {
                    /* @link http://stackoverflow.com/a/21480444 */
                    $yearPreviousToNewYear = $newStart->format('Y') - 1;
                    $weeksInPreviousYear = (int) (new DateTime("December 28th, {$yearPreviousToNewYear}"))->format('W');
                    $weeksDiff = ($weeksInPreviousYear - $oldStartWeekOfYear) + $newStartWeekOfYear;
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
                $yearPreviousToNewYear = $newEnd->format('Y') - 1;
                $weeksInPreviousYear = (int) (new DateTime("December 28th, {$yearPreviousToNewYear}"))->format('W');
                $weeksDiff = ($weeksInPreviousYear - $oldEndWeekOfYear) + $newEndWeekOfYear;
            }
            return (
                //day of the week is the same
                $oldEnd->format('w') === $newEnd->format('w')
                // dates are two weeks apart
                && 2 === $weeksDiff
            );
        }))->once();

        foreach ($course->getSessions() as $session) {
            $newSession = m::mock(SessionInterface::class);
            $newSession->shouldIgnoreMissing();

            foreach ($session->getOfferings() as $offering) {
                $newOffering = m::mock(OfferingInterface::class);
                $newOffering->shouldIgnoreMissing();
                $newOffering->shouldReceive('setStartDate')->with(m::on(function (DateTime $newStart) use ($offering) {
                    $oldStart = $offering->getStartDate();
                    $oldStartWeekOfYear = (int) $oldStart->format('W');
                    $newStartWeekOfYear = (int) $newStart->format('W');
                    $weeksDiff = 0;
                    if ($newStartWeekOfYear > $oldStartWeekOfYear) {
                        $weeksDiff = $newStartWeekOfYear - $oldStartWeekOfYear;
                    } elseif ($newStartWeekOfYear < $oldStartWeekOfYear) {
                        $yearPreviousToNewYear = $newStart->format('Y') - 1;
                        $weeksInPreviousYear
                            = (int) (new DateTime("December 28th, {$yearPreviousToNewYear}"))->format('W');
                        $weeksDiff = ($weeksInPreviousYear - $oldStartWeekOfYear) + $newStartWeekOfYear;
                    }
                    return (
                        //day of the week is the same
                        $oldStart->format('w') === $newStart->format('w') &&
                        //dates are two weeks apart
                        2 === $weeksDiff
                    );
                }))->once();
                $newOffering->shouldReceive('setEndDate')->with(m::on(function (DateTime $newEnd) use ($offering) {
                    $oldEnd = $offering->getEndDate();
                    $oldEndWeekOfYear = (int) $oldEnd->format('W');
                    $newEndWeekOfYear = (int) $newEnd->format('W');
                    $weeksDiff = 0;
                    if ($newEndWeekOfYear > $oldEndWeekOfYear) {
                        $weeksDiff = $newEndWeekOfYear - $oldEndWeekOfYear;
                    } elseif ($newEndWeekOfYear < $oldEndWeekOfYear) {
                        $yearPreviousToNewYear = $newEnd->format('Y') - 1;
                        $weeksInPreviousYear
                            = (int) (new DateTime("December 28th, {$yearPreviousToNewYear}"))->format('W');
                        $weeksDiff = ($weeksInPreviousYear - $oldEndWeekOfYear) + $newEndWeekOfYear;
                    }
                    return (
                        //day of the week is the same
                        $oldEnd->format('w') === $newEnd->format('w')
                        // dates are two weeks apart
                        && 2 === $weeksDiff
                    );
                }))->once();

                $this->offeringRepository->shouldReceive('create')->once()->andReturn($newOffering);
                $this->offeringRepository->shouldReceive('update')->once()->withArgs([$newOffering, false, false]);
            }

            $this->sessionRepository->shouldReceive('create')->once()->andReturn($newSession);
            $this->sessionRepository->shouldReceive('update')->once()->withArgs([$newSession, false, false]);
        }
        $this->service->rolloverCourse($course->getId(), $newYear, ['new-start-date' => $newStartDate->format('c')]);
    }


    public function testRolloverWithInSameYearWithNewStartDate(): void
    {
        $course = $this->createTestCourseWithOfferings();

        $newCourse = m::mock(CourseInterface::class);
        $newCourse->shouldIgnoreMissing();

        $newYear = $course->getYear();
        $newTitle = $course->getTitle();

        $this->courseRepository->shouldReceive('findOneBy')
            ->withArgs([['id' => $course->getId()]])->andReturn($course)->once();
        $this->courseRepository
            ->shouldReceive('findBy')
            ->withArgs([['title' => $newTitle, 'year' => $newYear]])
            ->andReturn([])->once();
        $this->courseRepository->shouldReceive('update')->withArgs([$newCourse, false, false])->once();
        $this->courseRepository
            ->shouldReceive('create')->once()
            ->andReturn($newCourse);
        $this->courseRepository->shouldReceive('flushAndClear')->once();

        $newCourse->shouldReceive('setYear')->with($newYear)->once();
        $newCourse->shouldReceive('setTitle')->with($newTitle)->once();

        $newStartDate = clone $course->getStartDate();
        //start the new course 16 weeks (112 days) later
        $offset = new DateInterval('P112D');
        $newStartDate->add($offset);

        $newCourse
            ->shouldReceive('setStartDate')
            ->with(m::on(function (DateTime $newStart) use ($offset, $course, $newStartDate) {
                $oldStart = $course->getStartDate();
                $expectedStartWeek = (int) (clone $oldStart)->add($offset)->format('W');
                $startWeek = (int) $newStart->format('W');

                return (
                    $newStart->format('c') === $newStartDate->format('c') &&
                    //day of the week is the same
                    $oldStart->format('w') === $newStart->format('w') &&
                    //Week of the year is two weeks later
                    $expectedStartWeek ===  $startWeek
                );
            }))->once();

        $newCourse->shouldReceive('setEndDate')->with(m::on(function (DateTime $newEnd) use ($course, $offset) {
            $oldEnd = $course->getEndDate();
            $expectedEndWeek = (int) (clone $oldEnd)->add($offset)->format('W');
            $endWeek = (int) $newEnd->format('W');

            return (
                //day of the week is the same
                $oldEnd->format('w') === $newEnd->format('w') &&
                //Week of the year is two weeks laters
                $expectedEndWeek === $endWeek
            );
        }))->once();

        foreach ($course->getSessions() as $session) {
            $newSession = m::mock(SessionInterface::class);
            $newSession->shouldIgnoreMissing();

            foreach ($session->getOfferings() as $offering) {
                $newOffering = m::mock(OfferingInterface::class);
                $newOffering->shouldIgnoreMissing();
                $newOffering
                    ->shouldReceive('setStartDate')
                    ->with(m::on(function (DateTime $newStart) use ($offset, $offering) {
                        $oldStart = $offering->getStartDate();
                        $expectedStartWeek = (int) (clone $oldStart)->add($offset)->format('W');
                        $startWeek = (int) $newStart->format('W');

                        return (
                            //day of the week is the same
                            $oldStart->format('w') === $newStart->format('w') &&
                            //Week of the year is the same
                            $expectedStartWeek ===  $startWeek
                        );
                    }))->once();
                $newOffering
                    ->shouldReceive('setEndDate')
                    ->with(m::on(function (DateTime $newEnd) use ($offset, $offering) {
                        $oldEnd = $offering->getEndDate();
                        $expectedEndWeek = (int) (clone $oldEnd)->add($offset)->format('W');
                        $endWeek = (int) $newEnd->format('W');

                        return (
                            //day of the week is the same
                            $oldEnd->format('w') === $newEnd->format('w') &&
                            //Week of the year is the same
                            $expectedEndWeek ===  $endWeek
                        );
                    }))->once();

                $this->offeringRepository->shouldReceive('create')->once()->andReturn($newOffering);
                $this->offeringRepository->shouldReceive('update')->once()->withArgs([$newOffering, false, false]);
            }

            $this->sessionRepository->shouldReceive('create')->once()->andReturn($newSession);
            $this->sessionRepository->shouldReceive('update')->once()->withArgs([$newSession, false, false]);
        }
        $this->service->rolloverCourse($course->getId(), $newYear, ['new-start-date' => $newStartDate->format('c')]);
    }

    public function testRolloverSessionObjectiveWithOrphanedParents(): void
    {
        $course = $this->createTestCourse();
        $course->setSchool(new School());

        $courseXObjective = new CourseObjective();
        $courseXObjective->setId(13);
        $courseXObjective->setTitle('test');
        $course->addCourseObjective($courseXObjective);

        $session = new Session();
        $session->setId(2);
        $session->setSessionType(new SessionType());
        $sessionXObjective = new SessionObjective();
        $sessionXObjective->setId(1);
        $sessionCourseObjective = new CourseObjective();
        $sessionCourseObjective->setId(2);
        $sessionXObjective->addCourseObjective($sessionCourseObjective);
        $sessionXObjective->addCourseObjective($courseXObjective);
        $sessionXObjective->setTitle('test session');
        $session->addSessionObjective($sessionXObjective);
        $course->addSession($session);

        $newCourse = m::mock(CourseInterface::class);
        $newCourse->shouldIgnoreMissing();
        $newYear = $this->setupCourseRepository($course, $newCourse);

        $newSession = m::mock(SessionInterface::class);
        $newSession->shouldIgnoreMissing();
        $newCourseObjective = m::mock(CourseObjectiveInterface::class);
        $newCourseObjective->shouldIgnoreMissing();
        $newSessionObjective = m::mock(SessionObjectiveInterface::class);
        $newSessionObjective->shouldIgnoreMissing();

        //We should end up with 1 parent since the other one is an orphan
        $newSessionObjective->shouldReceive('setCourseObjectives')
            ->with(m::on(function (Collection $collection) use ($newCourseObjective) {
                $this->assertEquals($collection->count(), 1);
                $this->assertEquals($newCourseObjective, $collection->first());
                return count($collection) === 1;
            }));
        $this->sessionRepository->shouldReceive('create')->once()->andReturn($newSession);
        $this->courseObjectiveRepository->shouldReceive('create')->once()->andReturn($newCourseObjective);
        $this->sessionObjectiveRepository->shouldReceive('create')->once()->andReturn($newSessionObjective);

        $this->sessionRepository->shouldIgnoreMissing();
        $this->sessionObjectiveRepository->shouldIgnoreMissing();
        $this->courseObjectiveRepository->shouldIgnoreMissing();

        $newCourse->shouldReceive('getCohorts')->once()->andReturn(new ArrayCollection());
        $rhett = $this->service->rolloverCourse($course->getId(), $newYear, ['']);
        $this->assertSame($newCourse, $rhett);
    }

    public function testRolloverInSameYearKeepsRelationships(): void
    {
        $course = $this->createTestCourseWithAssociations();
        $newCourse = m::mock(CourseInterface::class);
        $newYear = $course->getYear();
        $newTitle = $course->getTitle() . ' again';
        $this->courseRepository->shouldReceive('findOneBy')
            ->withArgs([['id' => $course->getId()]])->andReturn($course)->once();
        $this->courseRepository
            ->shouldReceive('findBy')
            ->withArgs([['title' => $newTitle, 'year' => $newYear]])
            ->andReturn([])->once();
        $this->courseRepository->shouldReceive('update')->withArgs([$newCourse, false, false])->once();

        $this->courseRepository
            ->shouldReceive('create')->once()
            ->andReturn($newCourse);

        $this->courseRepository->shouldReceive('flushAndClear')->once();
        $newCourse->shouldReceive('setCohorts')->with($course->getCohorts());
        $newCourse->shouldReceive('getCohorts')->once()->andReturn($course->getCohorts());
        $newCourse->shouldIgnoreMissing();

        /** @var CourseObjectiveInterface $courseObjective */
        foreach ($course->getCourseObjectives() as $courseObjective) {
            $newCourseObjective = m::mock(CourseObjectiveInterface::class);
            $newCourseObjective->shouldReceive('setCourse')->with($newCourse)->once();
            $newCourseObjective->shouldReceive('setPosition')->with($courseObjective->getPosition())->once();
            $newCourseObjective->shouldReceive('setTitle')->with($courseObjective->getTitle())->once();
            $newCourseObjective->shouldReceive('setAncestor')->with($courseObjective->getAncestorOrSelf())->once();
            $newCourseObjective->shouldReceive('setMeshDescriptors')
                ->with($courseObjective->getMeshDescriptors())->once();
            $newCourseObjective->shouldReceive('setProgramYearObjectives')
                ->with($courseObjective->getProgramYearObjectives());
            $newCourseObjective->shouldReceive('setTerms')->with($courseObjective->getTerms())->once();

            $this->courseObjectiveRepository->shouldReceive('create')->once()->andReturn($newCourseObjective);
            $this->courseObjectiveRepository
                ->shouldReceive('update')->once()->withArgs([$newCourseObjective, false, false]);
        }

        foreach ($course->getLearningMaterials() as $learningMaterial) {
            $newLearningMaterial = m::mock(CourseLearningMaterialInterface::class);
            $newLearningMaterial->shouldIgnoreMissing();
            $this->courseLearningMaterialRepository->shouldReceive('create')->once()->andReturn($newLearningMaterial);
            $this->courseLearningMaterialRepository->shouldIgnoreMissing();
        }

        /** @var SessionInterface $session */
        foreach ($course->getSessions() as $session) {
            $newSession = m::mock(SessionInterface::class);
            $newSession->shouldIgnoreMissing();
            $this->sessionRepository
                ->shouldReceive('create')->once()
                ->andReturn($newSession);
            $this->sessionRepository->shouldReceive('update')->withArgs([$newSession, false, false])->once();

            /** @var SessionObjectiveInterface $sessionObjective */
            foreach ($session->getSessionObjectives() as $sessionObjective) {
                $newSessionObjective = m::mock(SessionObjectiveInterface::class);
                $newSessionObjective->shouldReceive('setSession')->with($newSession)->once();
                $newSessionObjective->shouldReceive('setPosition')->with($sessionObjective->getPosition())->once();
                $newSessionObjective->shouldReceive('setTitle')->with($sessionObjective->getTitle())->once();
                $newSessionObjective->shouldReceive('setMeshDescriptors')
                    ->with($sessionObjective->getMeshDescriptors())->once();
                $newSessionObjective->shouldReceive('setAncestor')
                    ->with($sessionObjective->getAncestorOrSelf())->once();
                $newSessionObjective->shouldReceive('setTerms')->with($sessionObjective->getTerms())->once();

                $newSessionObjective->shouldReceive('setCourseObjectives')->with(m::on(
                    fn(Collection $collection) => count($collection) === count($sessionObjective->getCourseObjectives())
                ))->once();

                $this->sessionObjectiveRepository->shouldReceive('create')->once()->andReturn($newSessionObjective);
                $this->sessionObjectiveRepository->shouldReceive('update')
                    ->withArgs([$newSessionObjective, false, false]);
            }

            foreach ($session->getLearningMaterials() as $learningMaterial) {
                $newLearningMaterial = m::mock(SessionLearningMaterialInterface::class);
                $newLearningMaterial->shouldIgnoreMissing();
                $this->sessionLearningMaterialRepository->shouldReceive('create')->once()
                    ->andReturn($newLearningMaterial);
                $this->sessionLearningMaterialRepository->shouldIgnoreMissing();
            }

            if ($oldIlmSession = $session->getIlmSession()) {
                $newIlmSession = m::mock(IlmSessionInterface::class);
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
                $this->ilmSessionRepository
                    ->shouldReceive('create')->once()
                    ->andReturn($newIlmSession);
                $this->ilmSessionRepository->shouldReceive('update')->once()
                    ->withArgs([$newIlmSession, false, false]);
            }

            foreach ($session->getOfferings() as $offering) {
                $newOffering = m::mock(OfferingInterface::class);
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
                $this->offeringRepository->shouldReceive('create')->once()->andReturn($newOffering);
                $this->offeringRepository->shouldReceive('update')->once()->withArgs([$newOffering, false, false]);
            }
        }
        $rhett = $this->service->rolloverCourse($course->getId(), $newYear, ['new-course-title' => $newTitle]);

        $this->assertSame($newCourse, $rhett);
    }

    public function testRolloverWithEmptyClerkshipType(): void
    {
        $course = $this->createTestCourse();
        $course->setSchool(new School());
        $newCourse = m::mock(CourseInterface::class);
        $newCourse->shouldIgnoreMissing();
        $newCourse->shouldNotReceive('setClerkshipType');
        $newYear = $this->setupCourseRepository($course, $newCourse);

        $this->service->rolloverCourse($course->getId(), $newYear, ['']);
    }

    public function testRolloverWithNewStartDate(): void
    {
        $this->markTestIncomplete();
    }

    public function testRolloverWithoutSessions(): void
    {
        $this->markTestIncomplete();
    }

    public function testRolloverWithoutCourseLearningMaterials(): void
    {
        $course = $this->createTestCourse();
        $course->setSchool(new School());
        $course->addLearningMaterial(new CourseLearningMaterial());
        $newCourse = m::mock(CourseInterface::class);
        $newCourse->shouldIgnoreMissing();
        $newCourse->shouldNotReceive('addLearningMaterial');
        $this->courseLearningMaterialRepository->shouldNotReceive('create');
        $newYear = $this->setupCourseRepository($course, $newCourse);

        $this->service->rolloverCourse($course->getId(), $newYear, ['skip-course-learning-materials' => true]);
    }

    public function testRolloverWithoutCourseObjectives(): void
    {
        $course = $this->createTestCourse();
        $course->setSchool(new School());
        $courseObjective = new CourseObjective();
        $course->addCourseObjective($courseObjective);
        $newCourse = m::mock(CourseInterface::class);
        $newCourse->shouldIgnoreMissing();
        $newCourse->shouldNotReceive('addCourseObjective');
        $this->courseObjectiveRepository->shouldNotReceive('create');
        $newYear = $this->setupCourseRepository($course, $newCourse);

        $this->service->rolloverCourse($course->getId(), $newYear, ['skip-course-objectives' => true]);
    }

    public function testRolloverWithoutCourseTerms(): void
    {
        $course = $this->createTestCourse();
        $course->setSchool(new School());
        $course->addTerm(new Term());
        $newCourse = m::mock(CourseInterface::class);
        $newCourse->shouldIgnoreMissing();
        $newCourse->shouldNotReceive('setTerms');
        $newYear = $this->setupCourseRepository($course, $newCourse);

        $this->service->rolloverCourse($course->getId(), $newYear, ['skip-course-terms' => true]);
    }

    public function testRolloverWithoutCourseMesh(): void
    {
        $course = $this->createTestCourse();
        $course->setSchool(new School());
        $course->addMeshDescriptor(new MeshDescriptor());
        $newCourse = m::mock(CourseInterface::class);
        $newCourse->shouldIgnoreMissing();
        $newCourse->shouldNotReceive('setMeshDescriptors');
        $newYear = $this->setupCourseRepository($course, $newCourse);

        $this->service->rolloverCourse($course->getId(), $newYear, ['skip-course-mesh' => true]);
    }

    public function testRolloverWithoutCourseAncestor(): void
    {
        $course = $this->createTestCourse();
        $course->setSchool(new School());
        $newCourse = m::mock(CourseInterface::class);
        $newCourse->shouldIgnoreMissing();
        $newCourse->shouldReceive('setAncestor')->with($course)->once();
        $newCourse->shouldReceive('getCohorts')->once()->andReturn(new ArrayCollection());
        $newYear = $this->setupCourseRepository($course, $newCourse);

        $this->service->rolloverCourse($course->getId(), $newYear, []);
    }

    public function testRolloverWithoutSessionLearningMaterials(): void
    {
        $this->markTestIncomplete();
    }

    public function testRolloverWithoutSessionObjectives(): void
    {
        $this->markTestIncomplete();
    }

    public function testRolloverWithoutSessionTerms(): void
    {
        $this->markTestIncomplete();
    }

    public function testRolloverWithoutSessionMesh(): void
    {
        $this->markTestIncomplete();
    }

    public function testRolloverWithoutOfferings(): void
    {
        $this->markTestIncomplete();
    }

    public function testRolloverWithoutInstructors(): void
    {
        $this->markTestIncomplete();
    }

    public function testRolloverWithoutInstructorGroups(): void
    {
        $this->markTestIncomplete();
    }

    public function testRolloverWithNewCourseTitle(): void
    {
        $this->markTestIncomplete();
    }

    // @todo test the hell out of this. use a data provider here. [ST 2016/06/17]
    public function testRolloverOffsetCalculation(): void
    {
        $this->markTestIncomplete();
    }

    public function testRolloverFailsOnDuplicate(): void
    {
        $course = $this->createTestCourse();
        $newYear = $course->getYear() + 1;
        $this->courseRepository->shouldReceive('findOneBy')->withArgs([['id' => $course->getId()]])->andReturn($course);
        $this->courseRepository
            ->shouldReceive('findBy')
            ->withArgs([['title' => $course->getTitle(), 'year' => $newYear]])
            ->andReturn([new Course()]);

        $this->expectException(Exception::class);

        $this->service->rolloverCourse($course->getId(), $newYear, ['']);
    }

    public function testRolloverFailsOnYearPast(): void
    {
        $courseId = 10;
        $pastDate = new DateTime();
        $pastDate->add(DateInterval::createFromDateString('-2 year'));
        $year = (int) $pastDate->format('Y');

        $this->expectException(Exception::class);

        $this->service->rolloverCourse($courseId, $year, []);
    }

    public function testRolloverFailsOnMissingCourse(): void
    {
        $courseId = -1;
        $futureDate = new DateTime();
        $futureDate->add(DateInterval::createFromDateString('+2 year'));
        $year = (int) $futureDate->format('Y');
        $this->courseRepository->shouldReceive('findOneBy')->withArgs([['id' => $courseId]])->andReturn(null);

        $this->expectException(Exception::class);

        $this->service->rolloverCourse($courseId, $year, []);
    }

    public function testRolloverFailsOnStartDateOnDifferentDay(): void
    {
        $course = $this->createTestCourse();
        $course->setSchool(new School());

        $newCourse = m::mock(CourseInterface::class);
        $newCourse->shouldIgnoreMissing();
        $newYear = $course->getYear() + 1;
        $this->courseRepository->shouldReceive('findOneBy')
            ->withArgs([['id' => $course->getId()]])->andReturn($course)->once();
        $this->courseRepository
            ->shouldReceive('findBy')
            ->withArgs([['title' => $course->getTitle(), 'year' => $newYear]])
            ->andReturn([])->once();

        $newStartDate = clone $course->getStartDate();
        $newStartDate->add(new DateInterval('P1Y2D'));

        $this->expectException(Exception::class);
        $this->service->rolloverCourse($course->getId(), $newYear, ['new-start-date' => $newStartDate->format('c')]);
    }

    public function testRolloverCohortAndReLinkObjectives(): void
    {
        $course = $this->createTestCourse();
        $course->setSchool(new School());
        $programYear = new ProgramYear();
        $cohort = new Cohort();
        $cohort->setProgramYear($programYear);
        $programYear->setCohort($cohort);
        $pyXObjective = new ProgramYearObjective();
        $pyXObjective->setId(1);
        $pyXObjective->setTitle('test program year objective');
        $programYear->addProgramYearObjective($pyXObjective);

        $newProgramYear = new ProgramYear();
        $newCohort = new Cohort();
        $newCohort->setId(11);
        $newCohort->setProgramYear($newProgramYear);
        $newProgramYear->setCohort($newCohort);
        $newPyXObjective = new ProgramYearObjective();
        $newPyXObjective->setId(1);
        $newPyXObjective->setTitle('test program year objective');
        $newPyXObjective->setAncestor($pyXObjective);

        $newProgramYear->addProgramYearObjective($newPyXObjective);

        $courseXObjective1 = new CourseObjective();
        $courseXObjective1->setId(808);
        $courseXObjective1->setTitle('test course objective1');
        $courseXObjective1->addProgramYearObjective($pyXObjective);
        $course->addCourseObjective($courseXObjective1);

        $newCourse = m::mock(CourseInterface::class);
        $newCourse->shouldIgnoreMissing();
        $newYear = $this->setupCourseRepository($course, $newCourse);

        $newCourse->shouldReceive('setAncestor')->with($course)->once();
        $newCourse->shouldReceive('addCohort')->once()->with($newCohort);
        $newCourse->shouldReceive('getCohorts')->andReturn(new ArrayCollection([$newCohort]));



        $newCourseXObjective = m::mock(CourseObjectiveInterface::class);
        $newCourseXObjective->shouldReceive('setCourse')->with($newCourse)->once();
        $newCourseXObjective->shouldReceive('setTerms')->with($courseXObjective1->getTerms())->once();
        $newCourseXObjective->shouldReceive('setPosition')->with($courseXObjective1->getPosition())->once();
        $newCourseXObjective->shouldReceive('setTitle')->with('test course objective1')->once();
        $newCourseXObjective->shouldReceive('setAncestor')->with($courseXObjective1)->once();
        $newCourseXObjective->shouldReceive('addProgramYearObjective')->with($newPyXObjective)->once();
        $newCourseXObjective->shouldReceive('setMeshDescriptors')
            ->with($courseXObjective1->getMeshDescriptors())->once();

        $this->cohortRepository->shouldReceive('findOneBy')->with(['id' => 11])->andReturn($newCohort);


        $this->courseObjectiveRepository->shouldReceive('create')->andReturn($newCourseXObjective);
        $this->courseObjectiveRepository
            ->shouldReceive('update')->once()->withArgs([$newCourseXObjective, false, false]);

        $rhett = $this->service->rolloverCourse($course->getId(), $newYear, [], [11]);
        $this->assertSame($newCourse, $rhett);
    }

    public function testRolloverLinkedSessions(): void
    {
        $course = $this->createTestCourse();
        $course->setSchool(new School());

        $session1 = new Session();
        $session1->setId(1);
        $session1->setSessionType(new SessionType());
        $course->addSession($session1);

        $session2 = new Session();
        $session2->setId(2);
        $session2->setSessionType(new SessionType());
        $session2->setPostrequisite($session1);

        $course->addSession($session2);

        $newCourse = m::mock(CourseInterface::class);
        $newCourse->shouldIgnoreMissing();
        $newYear = $this->setupCourseRepository($course, $newCourse);

        $firstNewSession = m::mock(SessionInterface::class);
        $firstNewSession->shouldIgnoreMissing();
        $this->sessionRepository
            ->shouldReceive('create')->once()
            ->andReturn($firstNewSession);
        $this->sessionRepository->shouldReceive('update')->withArgs([$firstNewSession, false, false])->once();

        $secondNewSession = m::mock(SessionInterface::class);
        $secondNewSession->shouldIgnoreMissing();
        $this->sessionRepository
            ->shouldReceive('create')->once()
            ->andReturn($secondNewSession);
        $this->sessionRepository->shouldReceive('update')->withArgs([$secondNewSession, false, false])->twice();
        $secondNewSession->shouldReceive('setPostrequisite')->with($firstNewSession)->once();

        $rhett = $this->service->rolloverCourse($course->getId(), $newYear, [], []);
        $this->assertSame($newCourse, $rhett);
    }

    /**
     * Gets a basic filled out course
     */
    protected function createTestCourse(): Course
    {
        $course = new Course();
        $course->setId(10);
        $course->setTitle('test course');
        $course->setLevel(1);
        $now = new DateTime();
        $course->setYear((int) $now->format('Y'));
        // ACHTUNG!
        // If we're on a calendar week 52 or 53, then pick a large-enough offset
        // to force the start date into the new year.
        // This will ensure that we don't run into "week of the year" mismatches when
        // comparing start/end-dates between the original course and its rolled-over counterpart.
        // [ST 2021/01/04, week fifty three of 2020]
        if ($now->format('W') === '53') {
            $course->setStartDate(new DateTime('+8 days'));
            $course->setEndDate(new DateTime('+11 days'));
        } elseif ($now->format('W') === '52') {
            $course->setStartDate(new DateTime('+16 days'));
            $course->setEndDate(new DateTime('+19 days'));
        } else {
            $course->setStartDate(new DateTime('tomorrow'));
            $course->setEndDate(new DateTime('+3 days'));
        }

        $course->setExternalId('I45');
        $course->setLocked(true);
        $course->setArchived(true);
        $course->setPublished(true);
        $course->setPublishedAsTbd(true);

        return $course;
    }

    /**
     * Gets a course with a bunch of relationships attached
     */
    protected function createTestCourseWithAssociations(): Course
    {
        $course = $this->createTestCourse();

        $course->setClerkshipType(new CourseClerkshipType());
        $course->setSchool(new School());

        $objectiveTerm1 = new Term();
        $objectiveTerm2 = new Term();
        $objectiveTerm3 = new Term();

        $ancestorCourse = new Course();
        $ancestorCourse->setId(1);
        $ancestorCourse->setTitle('test ancestor course');
        $course->setAncestor($ancestorCourse);

        $ancestorCourseObjective = new CourseObjective();
        $ancestorCourseObjective->setId(1);
        $ancestorCourseObjective->setTitle('test ancestor objective');

        $courseXObjective1 = new CourseObjective();
        $courseXObjective1->setId(808);
        $courseXObjective1->setTitle('test course objective1');
        $courseXObjective1->addMeshDescriptor(new MeshDescriptor());
        $courseXObjective1->setCourse($course);
        $courseXObjective1->setPosition(10);
        $courseXObjective1->addTerm($objectiveTerm1);
        $courseXObjective1->addTerm($objectiveTerm2);
        $courseXObjective1->addTerm($objectiveTerm3);
        $programYearObjective = new ProgramYearObjective();
        $programYearObjective->setId(13);
        $courseXObjective1->addProgramYearObjective($programYearObjective);

        $course->addCourseObjective($courseXObjective1);

        $courseXObjective2 = new CourseObjective();
        $courseXObjective2->setId(42);
        $courseXObjective2->setTitle('test course objective2');
        $courseXObjective2->setAncestor($ancestorCourseObjective);
        $courseXObjective2->setCourse($course);

        $course->addCourseObjective($courseXObjective2);

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

        $programYear = new ProgramYear();
        $cohort = new Cohort();
        $cohort->setProgramYear($programYear);
        $programYear->setCohort($cohort);
        $course->addCohort($cohort);

        $session1 = new Session();
        $session1->setId(1);
        $session1->setDescription('test description');
        $session1->setSessionType(new SessionType());

        $ancestorSessionObjective = new SessionObjective();
        $ancestorSessionObjective->setId(2);
        $ancestorSessionObjective->setTitle('test session ancestor');

        $sessionXObjective1 = new SessionObjective();
        $sessionXObjective1->setId(99);
        $sessionXObjective1->setTitle('test session objective 1');
        $sessionXObjective1->addMeshDescriptor(new MeshDescriptor());
        $sessionXObjective1->addCourseObjective($courseXObjective1);
        $sessionXObjective1->addCourseObjective($courseXObjective2);
        $sessionXObjective1->addTerm($objectiveTerm1);
        $sessionXObjective1->setSession($session1);
        $sessionXObjective1->setPosition(5);
        $session1->addSessionObjective($sessionXObjective1);

        $sessionXObjective2 = new SessionObjective();
        $sessionXObjective2->setId(9);
        $sessionXObjective2->setTitle('test session objective 2');
        $sessionXObjective2->addMeshDescriptor(new MeshDescriptor());
        $sessionXObjective2->addCourseObjective($courseXObjective1);
        $sessionXObjective2->setAncestor($ancestorSessionObjective);
        $sessionXObjective2->setSession($session1);
        $session1->addSessionObjective($sessionXObjective2);

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
        $session2->setId(2);
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
     */
    protected function createTestCourseWithOfferings(): Course
    {
        $course = $this->createTestCourse();
        $course->setSchool(new School());
        $session = new Session();
        $session->setId(1);
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
     * Set up the course repository mock to do basic stuff we need in most tests
     */
    protected function setupCourseRepository(
        CourseInterface $course,
        CourseInterface $newCourse,
        int $interval = 1
    ): int {
        $newYear = $course->getYear() + $interval;
        $this->courseRepository->shouldReceive('findOneBy')
            ->withArgs([['id' => $course->getId()]])->andReturn($course)->once();
        $this->courseRepository
            ->shouldReceive('findBy')
            ->withArgs([['title' => $course->getTitle(), 'year' => $newYear]])
            ->andReturn([])->once();
        $this->courseRepository->shouldReceive('update')->withArgs([$newCourse, false, false])->once();

        $this->courseRepository
            ->shouldReceive('create')->once()
            ->andReturn($newCourse);

        $this->courseRepository->shouldReceive('flushAndClear')->once();

        return $newYear;
    }
}
