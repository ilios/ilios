<?php

namespace Ilios\CoreBundle\Classes;

use Ilios\CoreBundle\Entity\CourseInterface;
use Ilios\CoreBundle\Entity\CourseLearningMaterialInterface;
use Ilios\CoreBundle\Entity\Manager\ManagerInterface;
use Ilios\CoreBundle\Entity\OfferingInterface;
use Ilios\CoreBundle\Entity\SessionInterface;
use Ilios\CoreBundle\Entity\SessionLearningMaterialInterface;
use Ilios\CoreBundle\Entity\ObjectiveInterface;

/**
 * Class CourseRollover
 * @package Ilios\CoreBundle\Classes
 */
class CourseRollover
{

    /**
     * @var ManagerInterface
     */
    protected $courseManager;

    /**
     * @var ManagerInterface
     */
    protected $learningMaterialManager;

    /**
     * @var ManagerInterface
     */
    protected $courseLearningMaterialManager;

    /**
     * @var ManagerInterface
     */
    protected $sessionManager;

    /**
     * @var ManagerInterface;
     */
    protected $sessionLearningMaterialManager;

    /**
     * @var ManagerInterface
     */
    protected $offeringManager;

    /**
     * @var ManagerInterface
     */
    protected $objectiveManager;

    /**
     * CourseRollover constructor.
     * @param ManagerInterface $courseManager
     * @param ManagerInterface $learningMaterialManager
     * @param ManagerInterface $courseLearningMaterialManager
     * @param ManagerInterface $sessionManager
     * @param ManagerInterface $sessionLearningMaterialManager
     * @param ManagerInterface $offeringManager
     * @param ManagerInterface $objectiveManager
     */
    public function __construct(
        ManagerInterface $courseManager,
        ManagerInterface $learningMaterialManager,
        ManagerInterface $courseLearningMaterialManager,
        ManagerInterface $sessionManager,
        ManagerInterface $sessionLearningMaterialManager,
        ManagerInterface $offeringManager,
        ManagerInterface $objectiveManager
    )
    {
        $this->courseManager = $courseManager;
        $this->learningMaterialManager = $learningMaterialManager;
        $this->courseLearningMaterialManager = $courseLearningMaterialManager;
        $this->sessionManager = $sessionManager;
        $this->sessionLearningMaterialManager = $sessionLearningMaterialManager;
        $this->offeringManager = $offeringManager;
        $this->objectiveManager = $objectiveManager;

    }

    /**
     * @param int $courseId
     * @param int $newAcademicYear
     * @param array $options
     * @return CourseInterface the new, rolled-over course.
     * @throws \Exception
     */
    public function rolloverCourse($courseId, $newAcademicYear, $options)
    {
        //now, get/set the required values from the provided arguments
        $originalCourseId = $courseId;
        $newStartDate = (!empty($options['new-start-date'])) ? new \DateTime($options['new-start-date']) : null;

        //make sure that the new course's academic year or new start date year is not in the past
        $this->confirmYearIsNotInPast($newAcademicYear);
        if (!empty($newStartDate)) {
            $this->confirmYearIsNotInPast($newStartDate->format('Y'));
        }

        //get the original course object
        $originalCourse = $this->getOriginalCourse($originalCourseId);

        //if a new title is to be used, update before checking for duplicates
        $newTitle = !empty($options['new-course-title']) ? $options['new-course-title'] : $originalCourse->getTitle();
        //before creating the newCourse object, check for courses with same title & year, so a rollover is not run 2x
        $this->checkForDuplicateRollover($newTitle, $newAcademicYear);

        //get/set the original course's start/end dates for use in calculation of offset
        $originalCourseStartDate = $originalCourse->getStartDate();
        $originalCourseEndDate = $originalCourse->getEndDate();

        //if the new start date is not empty, ensure sure the day of the week matches the original day-of-week
        if (!empty($newStartDate)) {
            $this->compareStartDateDayOfWeek($originalCourseStartDate, $newStartDate);
        }

        //get the difference between the week ordinals the new start date and the original, year is arbitrary here
        $weekOrdinalDifference = $this->calculateWeeksOffset($originalCourseStartDate, $newStartDate);

        //set the offset in weeks using the week ordinal difference
        $newCourseStartDate = $this->getAdjustedDate($originalCourseStartDate, $newAcademicYear, $weekOrdinalDifference);
        $newCourseEndDate = $this->getAdjustedDate($originalCourseEndDate, $newAcademicYear, $weekOrdinalDifference);

        //create the Course
        //if there are not any duplicates, create a new course with the relevant info
        /* @var CourseInterface $newCourse */
        $newCourse = $this->courseManager->create();
        $newCourse->setTitle($newTitle);
        $newCourse->setLevel($originalCourse->getLevel());
        $newCourse->setYear($newAcademicYear);
        $newCourse->setStartDate($newCourseStartDate);
        $newCourse->setEndDate($newCourseEndDate);
        $newCourse->setExternalId($originalCourse->getExternalId());
        if ($clerkshipType = $originalCourse->getClerkshipType()) {
            $newCourse->setClerkshipType($clerkshipType);
        }
        $newCourse->setSchool($originalCourse->getSchool());
        $newCourse->setDirectors($originalCourse->getDirectors());

        if (empty($options['skip-course-terms'])) {
            $newCourse->setTerms($originalCourse->getTerms());
        }
        if (empty($options['skip-course-mesh'])) {
            $newCourse->setMeshDescriptors($originalCourse->getMeshDescriptors());
        }

        //COURSE OBJECTIVES
        $newCourseObjectives = [];
        if (empty($options['skip-course-objectives'])) {
            $newCourseObjectives = $this->rolloverCourseObjectives($newCourse, $originalCourse);
        }

        //persist the newCourse entity
        $this->courseManager->update($newCourse, false, false);

        //now run each of the subcomponents, starting with the course-specific ones
        //COURSE LEARNING MATERIALS
        if (empty($options['skip-course-learning-materials'])) {
            $this->rolloverCourseLearningMaterials($newCourse, $originalCourse);
        }

        //SESSIONS
        if (empty($options['skip-sessions'])) {
            $this->rolloverSessions($newCourse, $originalCourse, $newAcademicYear, $weekOrdinalDifference, $options, $newCourseObjectives);
        }

        //commit EVERYTHING to the database
        $this->courseManager->flushAndClear();

        //return the course
        return $newCourse;
    }

    /**
     * @param CourseInterface $newCourse
     * @param CourseInterface $originalCourse
     */
    protected function rolloverCourseLearningMaterials(CourseInterface $newCourse, CourseInterface $originalCourse)
    {
        /* @var CourseLearningMaterialInterface[] $originalCourseLearningMaterials */
        $originalCourseLearningMaterials = $originalCourse->getLearningMaterials();

        foreach ($originalCourseLearningMaterials as $originalCourseLearningMaterial) {
            /* @var CourseLearningMaterialInterface $newCourseLearningMaterial */
            $newCourseLearningMaterial = $this->courseLearningMaterialManager->create();
            $newCourseLearningMaterial->setNotes($originalCourseLearningMaterial->getNotes());
            $newCourseLearningMaterial->setRequired($originalCourseLearningMaterial->isRequired());
            $newCourseLearningMaterial->setPublicNotes($originalCourseLearningMaterial->hasPublicNotes());
            $newCourseLearningMaterial->setCourse($newCourse);
            $newCourseLearningMaterial->setLearningMaterial($originalCourseLearningMaterial->getLearningMaterial());
            $newCourseLearningMaterial->setMeshDescriptors($originalCourseLearningMaterial->getMeshDescriptors());

            $this->courseLearningMaterialManager->update($newCourseLearningMaterial, false, false);
        }
    }

    /**
     * @param CourseInterface $newCourse
     * @param CourseInterface $originalCourse
     * @param array $options
     * @param string|bool $offsetInWeeks a date modifier string, or FALSE if n/a
     * @param ObjectiveInterface[] $newCourseObjectives
     */
    protected function rolloverSessions(
        CourseInterface $newCourse,
        CourseInterface $originalCourse,
        $newAcademicYear,
        $weekOrdinalDifference,
        $options,
        array $newCourseObjectives
    )
    {
        /* @var SessionInterface[] $originalCourseSessions */
        $originalCourseSessions = $originalCourse->getSessions();

        foreach ($originalCourseSessions as $originalCourseSession) {
            /* @var SessionInterface $newSession */
            $newSession = $this->sessionManager->create();
            $newSession->setCourse($newCourse);
            $newSession->setTitle($originalCourseSession->getTitle());
            $newSession->setAttireRequired($originalCourseSession->isAttireRequired());
            $newSession->setEquipmentRequired($originalCourseSession->isEquipmentRequired());
            $newSession->setSessionType($originalCourseSession->getSessionType());
            $newSession->setSupplemental($originalCourseSession->isSupplemental());
            $newSession->setPublishedAsTbd(0);
            $newSession->setPublished(0);

            //SESSION LEARNING MATERIALS
            if (empty($options['skip-session-learning-materials'])) {
                $this->rolloverSessionLearningMaterials($newSession, $originalCourseSession);
            }

            //SESSION OBJECTIVES
            if (empty($options['skip-session-objectives'])) {
                $this->rolloverSessionObjectives($newSession, $originalCourseSession, $newCourseObjectives);
            }

            //SESSION TERMS
            if (empty($options['skip-session-terms'])) {
                $newSession->setTerms($originalCourseSession->getTerms());
            }

            //SESSION MESH TERMS
            if (empty($options['skip-session-mesh'])) {
                $newSession->setMeshDescriptors($originalCourseSession->getMeshDescriptors());
            }

            //Offerings
            if (empty($options['skip-offerings'])) {
                $this->rolloverOfferings($newSession, $originalCourseSession, $newAcademicYear, $weekOrdinalDifference, $options);
            }

            $this->sessionManager->update($newSession, false, false);
        }
    }


    /**
     * @param SessionInterface $newSession
     * @param SessionInterface $originalCourseSession
     */
    protected function rolloverSessionLearningMaterials(
        SessionInterface $newSession,
        SessionInterface $originalCourseSession
    )
    {
        /* @var SessionLearningMaterialInterface[] $originalSessionLearningMaterials */
        $originalSessionLearningMaterials = $originalCourseSession->getLearningMaterials();

        foreach ($originalSessionLearningMaterials as $originalSessionLearningMaterial) {
            /* @var SessionLearningMaterialInterface $newSessionLearningMaterial */
            $newSessionLearningMaterial = $this->sessionLearningMaterialManager->create();
            $newSessionLearningMaterial->setNotes($originalSessionLearningMaterial->getNotes());
            $newSessionLearningMaterial->setRequired($originalSessionLearningMaterial->isRequired());
            $newSessionLearningMaterial->setSession($newSession);
            $newSessionLearningMaterial->setPublicNotes($originalSessionLearningMaterial->hasPublicNotes());
            $newSessionLearningMaterial->setLearningMaterial($originalSessionLearningMaterial->getLearningMaterial());
            $newSessionLearningMaterial->setMeshDescriptors($originalSessionLearningMaterial->getMeshDescriptors());

            $this->sessionLearningMaterialManager->update($newSessionLearningMaterial, false, false);
        }
    }

    /**
     * @param SessionInterface $newSession
     * @param SessionInterface $originalCourseSession
     * @param array $options
     * @param string|bool $offsetInWeeks a date modifier string, or FALSE if n/a
     */
    protected function rolloverOfferings(
        SessionInterface $newSession,
        SessionInterface $originalCourseSession,
        $newAcademicYear,
        $weeKOrdinalDifference,
        $options
    )
    {

        /* @var OfferingInterface[] $originalSessionOfferings */
        $originalSessionOfferings = $originalCourseSession->getOfferings();

        foreach ($originalSessionOfferings as $originalSessionOffering) {

            $newOfferingStartDate = $this->getAdjustedDate($originalSessionOffering->getStartDate(), $newAcademicYear, $weeKOrdinalDifference);
            $newOfferingEndDate = $this->getAdjustedDate($originalSessionOffering->getEndDate(), $newAcademicYear, $weeKOrdinalDifference);

            /* @var OfferingInterface $newOffering */
            $newOffering = $this->offeringManager->create();
            $newOffering->setRoom($originalSessionOffering->getRoom());
            $newOffering->setSite($originalSessionOffering->getSite());
            $newOffering->setStartDate($newOfferingStartDate);
            $newOffering->setEndDate($newOfferingEndDate);
            $newOffering->setSession($newSession);

            //Instructors
            if (empty($options['skip-instructors'])) {
                $newOffering->setInstructors($originalSessionOffering->getInstructors());
            }

            //Instructor Groups
            if (empty($options['skip-instructor-groups'])) {
                $newOffering->setInstructorGroups($originalSessionOffering->getInstructorGroups());
            }
            $this->offeringManager->update($newOffering, false, false);
        }
    }

    /**
     * @param \DateTime $originalDate
     * @param \DateTime|null $newDate
     * @return int
     */
    private function calculateWeeksOffset(
        $originalDate,
        $newDate = null
    )
    {
        //get the original and new week ordinals
        $originalWeekOrdinal = $originalDate->format('W');
        $newWeekOrdinal = (!empty($newDate)) ? $newDate->format('W') : $originalWeekOrdinal;
        $weekOrdinalDiff = ($originalWeekOrdinal - $newWeekOrdinal);

        return $weekOrdinalDiff;
    }

    /**
     * @param int $newAcademicYear
     * @throws \Exception
     */
    private function confirmYearIsNotInPast($newAcademicYear)
    {
        $currentYear = date('Y');
        if ($newAcademicYear < $currentYear) {
            throw new \Exception(
                "You cannot rollover a course to a new year or start date that is already in the past."
            );
        }
    }

    /**
     * @param string $title
     * @param int $newAcademicYear
     * @throws \Exception
     */
    private function checkForDuplicateRollover($title, $newAcademicYear)
    {
        $duplicateCourses = $this->courseManager->findBy(['title' => $title, 'year' => $newAcademicYear]);
        if (!empty($duplicateCourses)) {
            throw new \Exception(
                "Another course with the same title and academic year already exists."
                . " If the year is correct, consider setting a new course title with '--new-course-title' option."
            );
        }
    }

    /**
     * @param int $originalCourseId
     * @return CourseInterface
     * @throws \Exception
     */
    private function getOriginalCourse($originalCourseId)
    {
        $originalCourse = $this->courseManager->findOneBy(['id' => $originalCourseId]);
        if (empty($originalCourse)) {
            throw new \Exception(
                'There are no courses with courseId ' . $originalCourseId . '.'
            );
        }
        return $originalCourse;
    }

    /**
     * @param CourseInterface $newCourse
     * @param CourseInterface $originalCourse
     *
     * @return array
     */
    protected function rolloverCourseObjectives(
        CourseInterface $newCourse,
        CourseInterface $originalCourse
    )
    {
        $newCourseObjectives = [];
        foreach ($originalCourse->getObjectives() as $objective) {
            /* @var ObjectiveInterface $newObjective */
            $newObjective = $this->objectiveManager->create();
            $newObjective->setTitle($objective->getTitle());
            $newObjective->setMeshDescriptors($objective->getMeshDescriptors());
            $newObjective->addCourse($newCourse);
            $this->objectiveManager->update($newObjective, false, false);
            $newCourseObjectives[$objective->getId()] = $newObjective;
        }

        return $newCourseObjectives;

    }

    /**
     * @param SessionInterface $newSession
     * @param SessionInterface $originalSession
     * @param ObjectiveInterface[] $newCourseObjectives
     */
    protected function rolloverSessionObjectives(
        SessionInterface $newSession,
        SessionInterface $originalSession,
        array $newCourseObjectives
    )
    {
        $originalSession->getObjectives()
            ->map(function (ObjectiveInterface $objective) use ($newSession, $newCourseObjectives) {
                /* @var ObjectiveInterface $newObjective */
                $newObjective = $this->objectiveManager->create();
                $newObjective->setTitle($objective->getTitle());
                $newObjective->setMeshDescriptors($objective->getMeshDescriptors());
                $newObjective->addSession($newSession);
                $newParents = $objective->getParents()
                    ->map(function (ObjectiveInterface $oldParent) use ($newCourseObjectives, $objective) {
                        if (array_key_exists($oldParent->getId(), $newCourseObjectives)) {
                            return $newCourseObjectives[$oldParent->getId()];
                        }

                        return null;

                    })->filter(function ($value) {
                        return !empty($value);
                    });

                $newObjective->setParents($newParents);
                $this->objectiveManager->update($newObjective, false, false);
            });

    }

    /**
     * @param \DateTime $originalCourseStartDate
     * @param \DateTime $newStartDate
     * @throws \Exception
     */
    protected function compareStartDateDayOfWeek($originalCourseStartDate, $newStartDate)
    {
        if ($originalCourseStartDate->format('w') !== $newStartDate->format('w')) {
            throw new \Exception(
                "The new start date must take place on the same day of the week as the original course start date"
                . " ({$originalCourseStartDate->format('l')})."
            );
        }
    }

    /**
     * @param \DateTime $dateToMatch
     * @param \DateTime $dateToAdjust
     * @return \DateTime
     */
    protected function addDaysUntilMatching($dateToMatch, $dateToAdjust)
    {
        //compare the weekdays to make sure they match
        if ($dateToMatch->format('w') !== $dateToAdjust->format('w')) {
            //if they do not match, add another day and check again
            $dateToAdjust->modify('+ 1 days');
            //recursively loop through this function again, adding days until the weekday ordinals match
            $this->addDaysUntilMatching($dateToMatch, $dateToAdjust);
        }

        //when it finally matches, return the new date
        return $dateToAdjust;
    }

    /**
     * @param \DateTime $originalDate
     * @param int $newYear
     * @param int $weekOrdinalDifference
     * @return \DateTime
     */
    protected function getAdjustedDate
    (
        $originalDate,
        $newYear,
        $weekOrdinalDifference
    )
    {
        //get the difference between the academic years of original course and the desired year for the new course
        $yearDifference = ($newYear - $originalDate->format('Y'));

        //get the actual calendar year in which the new date will take place
        $adjustedDateYear = ($originalDate->format('Y') + $yearDifference);

        //get the new course's week ordinal by subtracting it from the original
        $newWeekOrdinal = ($originalDate->format('W') - $weekOrdinalDifference);

        //create a new date object to operate on
        $dateToAdjust = new \DateTime();

        //create a new date during the same week ordinal as the original
        $dateToAdjust->setISODate($adjustedDateYear, $newWeekOrdinal);

        //make sure the new date's day-of-week matches the original
        $adjustedDateTime = $this->addDaysUntilMatching($originalDate, $dateToAdjust);

        //get the times of the original dateTime object for use in the offering timeslots
        $newTimeHour = $originalDate->format('H');
        $newTimeMinutes = $originalDate->format('i');
        $newTimeSeconds = $originalDate->format('s');

        //now set the time on the new object
        $adjustedDateTime->setTime($newTimeHour, $newTimeMinutes, $newTimeSeconds);

        return $adjustedDateTime;
    }

}

