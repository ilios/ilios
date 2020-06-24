<?php

declare(strict_types=1);

namespace App\Service;

use App\Entity\CohortInterface;
use App\Entity\CourseInterface;
use App\Entity\CourseLearningMaterialInterface;
use App\Entity\CourseObjectiveInterface;
use App\Entity\IlmSessionInterface;
use App\Entity\Manager\BaseManager;
use App\Entity\Manager\CohortManager;
use App\Entity\Manager\CourseLearningMaterialManager;
use App\Entity\Manager\CourseManager;
use App\Entity\Manager\IlmSessionManager;
use App\Entity\Manager\LearningMaterialManager;
use App\Entity\Manager\OfferingManager;
use App\Entity\Manager\SessionLearningMaterialManager;
use App\Entity\Manager\SessionManager;
use App\Entity\OfferingInterface;
use App\Entity\ProgramYearObjectiveInterface;
use App\Entity\SessionInterface;
use App\Entity\Manager\SessionDescriptionManager;
use App\Entity\SessionDescriptionInterface;
use App\Entity\SessionLearningMaterialInterface;
use App\Entity\ObjectiveInterface;
use App\Entity\SessionObjectiveInterface;
use DateInterval;
use DateTime;
use Exception;

/**
 * CourseRollover Rolls over an existing course and its components to a new Academic Year
 *
 * @category Service
 */
class CourseRollover
{

    /**
     * @var CourseManager
     */
    protected $courseManager;

    /**
     * @var LearningMaterialManager
     */
    protected $learningMaterialManager;

    /**
     * @var CourseLearningMaterialManager
     */
    protected $courseLearningMaterialManager;

    /**
     * @var SessionManager
     */
    protected $sessionManager;

    /**
     * @var SessionDescriptionManager
     */
    protected $sessionDescriptionManager;

    /**
     * @var SessionLearningMaterialManager;
     */
    protected $sessionLearningMaterialManager;

    /**
     * @var BaseManager $courseObjectiveManager
     */
    protected $courseObjectiveManager;

    /**
     * @var BaseManager $sessionObjectiveManager
     */
    protected $sessionObjectiveManager;

    /**
     * @var OfferingManager
     */
    protected $offeringManager;

    /**
     * @var IlmSessionManager
     */
    protected $ilmSessionManager;

    /**
     * @var CohortManager
     */
    private $cohortManager;

    /**
     * CourseRollover constructor.
     *
     * @param CourseManager $courseManager
     * @param LearningMaterialManager $learningMaterialManager
     * @param CourseLearningMaterialManager $courseLearningMaterialManager
     * @param SessionManager $sessionManager
     * @param SessionDescriptionManager $sessionDescriptionManager
     * @param SessionLearningMaterialManager $sessionLearningMaterialManager
     * @param OfferingManager $offeringManager
     * @param IlmSessionManager $ilmSessionManager
     * @param CohortManager $cohortManager
     * @param BaseManager $courseObjectiveManager
     * @param BaseManager $sessionObjectiveManager
     */
    public function __construct(
        CourseManager $courseManager,
        LearningMaterialManager $learningMaterialManager,
        CourseLearningMaterialManager $courseLearningMaterialManager,
        SessionManager $sessionManager,
        SessionDescriptionManager $sessionDescriptionManager,
        SessionLearningMaterialManager $sessionLearningMaterialManager,
        OfferingManager $offeringManager,
        IlmSessionManager $ilmSessionManager,
        CohortManager $cohortManager,
        BaseManager $courseObjectiveManager,
        BaseManager $sessionObjectiveManager
    ) {
        $this->courseManager = $courseManager;
        $this->learningMaterialManager = $learningMaterialManager;
        $this->courseLearningMaterialManager = $courseLearningMaterialManager;
        $this->sessionManager = $sessionManager;
        $this->sessionDescriptionManager = $sessionDescriptionManager;
        $this->sessionLearningMaterialManager = $sessionLearningMaterialManager;
        $this->offeringManager = $offeringManager;
        $this->ilmSessionManager = $ilmSessionManager;
        $this->cohortManager = $cohortManager;
        $this->courseObjectiveManager = $courseObjectiveManager;
        $this->sessionObjectiveManager = $sessionObjectiveManager;
    }

    /**
     * @param int $courseId
     * @param int $newAcademicYear
     * @param array $options
     * @param array $newCohortIds
     * @return CourseInterface the new, rolled-over course.
     * @throws Exception
     */
    public function rolloverCourse(int $courseId, int $newAcademicYear, array $options, array $newCohortIds = [])
    {
        //now, get/set the required values from the provided arguments
        $origCourseId = $courseId;
        $newStartDate = (!empty($options['new-start-date'])) ? new DateTime($options['new-start-date']) : null;

        //make sure that the new course's academic year or new start date year is not in the past
        $this->confirmYearIsValid($newAcademicYear);
        if (!empty($newStartDate)) {
            $this->confirmYearIsValid($newStartDate->format('Y'));
        }

        //get the original course object
        $origCourse = $this->getOriginalCourse($origCourseId);

        //if a new title is to be used, update before checking for duplicates
        $newTitle = !empty($options['new-course-title']) ? $options['new-course-title'] : $origCourse->getTitle();
        //before creating the newCourse object, check for courses with same title & year, so a rollover is not run 2x
        $this->checkForDuplicateRollover($newTitle, $newAcademicYear);

        //get/set the original course's start/end dates for use in calculation of offset
        $origCourseStartDate = $origCourse->getStartDate();
        $origCourseEndDate = $origCourse->getEndDate();

        //if the new start date is not empty, ensure sure the day of the week matches the original day-of-week
        if (!empty($newStartDate)) {
            $this->compareStartDateDayOfWeek($origCourseStartDate, $newStartDate);
        }

        $daysOffset = $this->calculateDaysOffset(
            $origCourseStartDate,
            $origCourse->getYear(),
            $newAcademicYear,
            $newStartDate
        );
        //set the offset in weeks using the week ordinal difference
        $newCourseStartDate = $this->getAdjustedDate($origCourseStartDate, $daysOffset);
        $newCourseEndDate = $this->getAdjustedDate($origCourseEndDate, $daysOffset);

        //create the Course
        //if there are not any duplicates, create a new course with the relevant info
        /* @var CourseInterface $newCourse */
        $newCourse = $this->courseManager->create();
        $newCourse->setTitle($newTitle);
        $newCourse->setLevel($origCourse->getLevel());
        $newCourse->setYear($newAcademicYear);
        $newCourse->setStartDate($newCourseStartDate);
        $newCourse->setEndDate($newCourseEndDate);
        $newCourse->setExternalId($origCourse->getExternalId());
        $newCourse->setAncestor($origCourse->getAncestorOrSelf());
        if ($clerkshipType = $origCourse->getClerkshipType()) {
            $newCourse->setClerkshipType($clerkshipType);
        }
        $newCourse->setSchool($origCourse->getSchool());
        $newCourse->setDirectors($origCourse->getDirectors());
        $newCourse->setAdministrators($origCourse->getAdministrators());

        foreach ($newCohortIds as $id) {
            /** @var CohortInterface $cohort */
            $cohort = $this->cohortManager->findOneBy(['id' => $id]);
            if (!$cohort) {
                throw new Exception("There are no cohorts with id ${id}.");
            }
            $newCourse->addCohort($cohort);
        }

        if (empty($options['skip-course-terms'])) {
            $newCourse->setTerms($origCourse->getTerms());
        }
        if (empty($options['skip-course-mesh'])) {
            $newCourse->setMeshDescriptors($origCourse->getMeshDescriptors());
        }

        //COURSE OBJECTIVES
        $newCourseObjectives = [];
        if (empty($options['skip-course-objectives'])) {
            $newCourseObjectives = $this->rolloverCourseObjectives($newCourse, $origCourse);
        }

        //persist the newCourse entity
        $this->courseManager->update($newCourse, false, false);

        //now run each of the subcomponents, starting with the course-specific ones
        //COURSE LEARNING MATERIALS
        if (empty($options['skip-course-learning-materials'])) {
            $this->rolloverCourseLearningMaterials($newCourse, $origCourse);
        }

        //SESSIONS
        if (empty($options['skip-sessions'])) {
            $this->rolloverSessions(
                $newCourse,
                $origCourse,
                $daysOffset,
                $options,
                $newCourseObjectives
            );
        }

        //commit EVERYTHING to the database
        $this->courseManager->flushAndClear();

        //return the course
        return $newCourse;
    }

    /**
     * @param CourseInterface $newCourse
     * @param CourseInterface $origCourse
     */
    protected function rolloverCourseLearningMaterials(CourseInterface $newCourse, CourseInterface $origCourse)
    {
        /* @var CourseLearningMaterialInterface[] $origCourseLearningMaterials */
        $origCourseLearningMaterials = $origCourse->getLearningMaterials();

        foreach ($origCourseLearningMaterials as $origCourseLearningMaterial) {
            /* @var CourseLearningMaterialInterface $newCourseLearningMaterial */
            $newCourseLearningMaterial = $this->courseLearningMaterialManager->create();
            $newCourseLearningMaterial->setNotes($origCourseLearningMaterial->getNotes());
            $newCourseLearningMaterial->setRequired($origCourseLearningMaterial->isRequired());
            $newCourseLearningMaterial->setPublicNotes($origCourseLearningMaterial->hasPublicNotes());
            $newCourseLearningMaterial->setCourse($newCourse);
            $newCourseLearningMaterial->setLearningMaterial($origCourseLearningMaterial->getLearningMaterial());
            $newCourseLearningMaterial->setMeshDescriptors($origCourseLearningMaterial->getMeshDescriptors());
            $newCourseLearningMaterial->setPosition($origCourseLearningMaterial->getPosition());

            $this->courseLearningMaterialManager->update($newCourseLearningMaterial, false, false);
        }
    }

    /**
     * @param CourseInterface $newCourse
     * @param CourseInterface $origCourse
     * @param int $daysOffset
     * @param array $options
     * @param ObjectiveInterface[] $newCourseObjectives
     * @throws Exception
     */
    protected function rolloverSessions(
        CourseInterface $newCourse,
        CourseInterface $origCourse,
        $daysOffset,
        $options,
        array $newCourseObjectives
    ) {
        /* @var SessionInterface[] $origCourseSessions */
        $origCourseSessions = $origCourse->getSessions();

        $sessionMap = [];

        foreach ($origCourseSessions as $origCourseSession) {
            /* @var SessionInterface $newSession */
            $newSession = $this->sessionManager->create();
            $newSession->setCourse($newCourse);
            $newSession->setTitle($origCourseSession->getTitle());
            $newSession->setAttireRequired($origCourseSession->isAttireRequired());
            $newSession->setEquipmentRequired($origCourseSession->isEquipmentRequired());
            $newSession->setSessionType($origCourseSession->getSessionType());
            $newSession->setSupplemental($origCourseSession->isSupplemental());
            $newSession->setPublishedAsTbd(0);
            $newSession->setPublished(0);
            $newSession->setInstructionalNotes($origCourseSession->getInstructionalNotes());

            //now check for a session description and, if there is one, set it...
            $origSessionDescription = $origCourseSession->getSessionDescription();

            if (!empty($origSessionDescription)) {
                /* @var SessionDescriptionInterface $newSessionDescription */
                $newSessionDescription = $this->sessionDescriptionManager->create();

                $newSessionDescriptionText = $origSessionDescription->getDescription();
                $newSessionDescription->setDescription($newSessionDescriptionText);
                $newSession->setSessionDescription($newSessionDescription);
                $this->sessionDescriptionManager->update($newSessionDescription, false, false);
            }

            //SESSION LEARNING MATERIALS
            if (empty($options['skip-session-learning-materials'])) {
                $this->rolloverSessionLearningMaterials($newSession, $origCourseSession);
            }

            //SESSION OBJECTIVES
            if (empty($options['skip-session-objectives'])) {
                $this->rolloverSessionObjectives($newSession, $origCourseSession, $newCourseObjectives);
            }

            //SESSION TERMS
            if (empty($options['skip-session-terms'])) {
                $newSession->setTerms($origCourseSession->getTerms());
            }

            //SESSION MESH TERMS
            if (empty($options['skip-session-mesh'])) {
                $newSession->setMeshDescriptors($origCourseSession->getMeshDescriptors());
            }

            //Offerings
            if (empty($options['skip-offerings'])) {
                $this->rolloverOfferings($newSession, $origCourseSession, $daysOffset, $options);
            }

            //ILMSessions
            $this->rolloverIlmSession($newSession, $origCourseSession, $daysOffset);

            $this->sessionManager->update($newSession, false, false);
            $sessionMap[$origCourseSession->getId()] = $newSession;
        }

        // Handle postrequisites once all sessions have been rolled over
        // This was we can be sure we have the new session to refer to
        foreach ($origCourseSessions as $origCourseSession) {
            $originalPostrequisite = $origCourseSession->getPostrequisite();
            if ($originalPostrequisite && array_key_exists($originalPostrequisite->getId(), $sessionMap)) {
                $newSession = $sessionMap[$origCourseSession->getId()];
                $newSession->setPostrequisite($sessionMap[$originalPostrequisite->getId()]);
                $this->sessionManager->update($newSession, false, false);
            }
        }
    }


    /**
     * @param SessionInterface $newSession
     * @param SessionInterface $origCourseSession
     */
    protected function rolloverSessionLearningMaterials(
        SessionInterface $newSession,
        SessionInterface $origCourseSession
    ) {
        /* @var SessionLearningMaterialInterface[] $origSessionLearningMaterials */
        $origSessionLearningMaterials = $origCourseSession->getLearningMaterials();

        foreach ($origSessionLearningMaterials as $origSessionLearningMaterial) {
            /* @var SessionLearningMaterialInterface $newSessionLearningMaterial */
            $newSessionLearningMaterial = $this->sessionLearningMaterialManager->create();
            $newSessionLearningMaterial->setNotes($origSessionLearningMaterial->getNotes());
            $newSessionLearningMaterial->setRequired($origSessionLearningMaterial->isRequired());
            $newSessionLearningMaterial->setSession($newSession);
            $newSessionLearningMaterial->setPublicNotes($origSessionLearningMaterial->hasPublicNotes());
            $newSessionLearningMaterial->setLearningMaterial($origSessionLearningMaterial->getLearningMaterial());
            $newSessionLearningMaterial->setMeshDescriptors($origSessionLearningMaterial->getMeshDescriptors());
            $newSessionLearningMaterial->setPosition($origSessionLearningMaterial->getPosition());

            $this->sessionLearningMaterialManager->update($newSessionLearningMaterial, false, false);
        }
    }

    /**
     * @param SessionInterface $newSession
     * @param SessionInterface $origCourseSession
     * @param $daysOffset
     * @param $options
     * @throws Exception
     */
    protected function rolloverOfferings(
        SessionInterface $newSession,
        SessionInterface $origCourseSession,
        $daysOffset,
        $options
    ) {

        /* @var OfferingInterface[] $origSessionOfferings */
        $origSessionOfferings = $origCourseSession->getOfferings();

        foreach ($origSessionOfferings as $origSessionOffering) {
            $newOfferingStartDate = $this->getAdjustedDate(
                $origSessionOffering->getStartDate(),
                $daysOffset
            );
            $newOfferingEndDate = $this->getAdjustedDate(
                $origSessionOffering->getEndDate(),
                $daysOffset
            );

            /* @var OfferingInterface $newOffering */
            $newOffering = $this->offeringManager->create();
            $newOffering->setRoom($origSessionOffering->getRoom());
            $newOffering->setSite($origSessionOffering->getSite());
            $newOffering->setStartDate($newOfferingStartDate);
            $newOffering->setEndDate($newOfferingEndDate);
            $newOffering->setSession($newSession);

            //Instructors
            if (empty($options['skip-instructors'])) {
                $newOffering->setInstructors($origSessionOffering->getInstructors());
            }

            //Instructor Groups
            if (empty($options['skip-instructor-groups'])) {
                $newOffering->setInstructorGroups($origSessionOffering->getInstructorGroups());
            }
            $this->offeringManager->update($newOffering, false, false);
        }
    }

    /**
     * @param DateTime $origCourseStartDate
     * @param int $origAcademicYear
     * @param int $newAcademicYear
     * @param DateTime|null $newCourseStartDate
     * @return int
     * @throws Exception
     */
    private function calculateDaysOffset(
        DateTime $origCourseStartDate,
        int $origAcademicYear,
        int $newAcademicYear,
        DateTime $newCourseStartDate = null
    ) {
        if (!$newCourseStartDate) {
            $isoWeekOrdinal = (int) $origCourseStartDate->format('W');
            $isoDayOrdinal = (int) $origCourseStartDate->format('N');
            $yearDiff = (int) $origCourseStartDate->format('Y') - $origAcademicYear;

            $diffedYear = $newAcademicYear + $yearDiff;
            $newCourseStartDate = new DateTime();
            $newCourseStartDate->setISODate($diffedYear, $isoWeekOrdinal, $isoDayOrdinal);
        }

        return $newCourseStartDate->diff($origCourseStartDate)->days;
    }

    /**
     * @param int $newAcademicYear
     * @throws Exception
     */
    private function confirmYearIsValid($newAcademicYear)
    {
        $lastYear = date('Y') - 1;
        if ($newAcademicYear < $lastYear) {
            throw new Exception(
                "Courses cannot be rolled over to a new year before {$lastYear}."
            );
        }
    }

    /**
     * @param string $title
     * @param int    $newAcademicYear
     * @throws Exception
     */
    private function checkForDuplicateRollover($title, $newAcademicYear)
    {
        $duplicateCourses = $this->courseManager->findBy(['title' => $title, 'year' => $newAcademicYear]);
        if (!empty($duplicateCourses)) {
            throw new Exception(
                "Another course with the same title and academic year already exists."
                . " If the year is correct, consider setting a new course title with '--new-course-title' option."
            );
        }
    }

    /**
     * @param int $origCourseId
     * @return CourseInterface
     * @throws Exception
     */
    private function getOriginalCourse($origCourseId)
    {
        /* @var CourseInterface $origCourse */
        $origCourse = $this->courseManager->findOneBy(['id' => $origCourseId]);
        if (empty($origCourse)) {
            throw new Exception(
                'There are no courses with courseId ' . $origCourseId . '.'
            );
        }
        return $origCourse;
    }

    /**
     * @param CourseInterface $newCourse
     * @param CourseInterface $origCourse
     * @return CourseObjectiveInterface[]
     */
    protected function rolloverCourseObjectives(
        CourseInterface $newCourse,
        CourseInterface $origCourse
    ): array {
        $newCourseObjectives = [];
        $cohorts = $newCourse->getCohorts();
        foreach ($origCourse->getCourseObjectives() as $courseObjective) {
            /* @var CourseObjectiveInterface $newCourseObjective */
            $newCourseObjective = $this->courseObjectiveManager->create();
            $newCourseObjective->setCourse($newCourse);
            $newCourseObjective->setTerms($courseObjective->getTerms());
            $newCourseObjective->setPosition($courseObjective->getPosition());
            $newCourseObjective->setTitle($courseObjective->getTitle());
            $newCourseObjective->setMeshDescriptors($courseObjective->getMeshDescriptors());
            $newCourseObjective->setAncestor($courseObjective->getAncestorOrSelf());
            foreach ($cohorts as $cohort) {
                $this->reLinkCourseObjectiveToProgramYearObjectives($courseObjective, $newCourseObjective, $cohort);
            }

            $this->courseObjectiveManager->update($newCourseObjective, false, false);

            $newCourseObjectives[$courseObjective->getId()] = $newCourseObjective;
        }

        return $newCourseObjectives;
    }

    /**
     * @param CourseObjectiveInterface $courseObjective
     * @param CourseObjectiveInterface $newCourseObjective
     * @param CohortInterface $cohort
     */
    protected function reLinkCourseObjectiveToProgramYearObjectives(
        CourseObjectiveInterface $courseObjective,
        CourseObjectiveInterface $newCourseObjective,
        CohortInterface $cohort
    ) {
        $programYear = $cohort->getProgramYear();
        $programYearObjectives = $programYear->getProgramYearObjectives();

        /** @var ObjectiveInterface $parent */
        foreach ($courseObjective->getProgramYearObjectives() as $programYearObjective) {
            $ancestorId = $programYearObjective->getAncestorOrSelf()->getId();
            $newProgramYearObjectives = $programYearObjectives->filter(
                function (ProgramYearObjectiveInterface $py) use ($ancestorId) {
                    return $py->getAncestorOrSelf()->getId() === $ancestorId;
                }
            );
            if ($newProgramYearObjectives->count() > 0) {
                $newCourseObjective->addProgramYearObjective($newProgramYearObjectives->first());
            }
        }
    }

    /**
     * @param SessionInterface $newSession
     * @param SessionInterface $origSession
     * @param ObjectiveInterface[] $newCourseObjectives
     */
    protected function rolloverSessionObjectives(
        SessionInterface $newSession,
        SessionInterface $origSession,
        array $newCourseObjectives
    ): void {
        $origSession->getSessionObjectives()
            ->map(
                function (SessionObjectiveInterface $sessionObjective) use ($newSession, $newCourseObjectives) {
                    /** @var SessionObjectiveInterface $newSessionObjective */
                    $newSessionObjective = $this->sessionObjectiveManager->create();
                    $newSessionObjective->setSession($newSession);
                    $newSessionObjective->setTerms($sessionObjective->getTerms());
                    $newSessionObjective->setPosition($sessionObjective->getPosition());
                    $newSessionObjective->setTitle($sessionObjective->getTitle());
                    $newSessionObjective->setMeshDescriptors($sessionObjective->getMeshDescriptors());
                    $newSessionObjective->setAncestor($sessionObjective->getAncestorOrSelf());
                    $courseObjectives = $sessionObjective->getCourseObjectives()
                        ->map(
                            function (CourseObjectiveInterface $oldCourseObjective) use (
                                $newCourseObjectives,
                                $sessionObjective
                            ) {
                                if (array_key_exists($oldCourseObjective->getId(), $newCourseObjectives)) {
                                    return $newCourseObjectives[$oldCourseObjective->getId()];
                                }
                                return null;
                            }
                        )->filter(function ($value) {
                            return !empty($value);
                        });

                    $newSessionObjective->setCourseObjectives($courseObjectives);
                    $this->sessionObjectiveManager->update($newSessionObjective, false, false);
                }
            );
    }

    /**
     * @param SessionInterface $newSession
     * @param SessionInterface $origSession
     * @param $daysOffset
     * @throws Exception
     */
    protected function rolloverIlmSession(
        SessionInterface $newSession,
        SessionInterface $origSession,
        $daysOffset
    ) {
        if ($origIlmSession = $origSession->getIlmSession()) {
            /* @var IlmSessionInterface $newIlmSession */
            $newIlmSession = $this->ilmSessionManager->create();
            $newIlmSession->setHours($origIlmSession->getHours());
            $newSession->setIlmSession($newIlmSession);
            $newDueDate = $this->getAdjustedDate(
                $origIlmSession->getDueDate(),
                $daysOffset
            );
            $newIlmSession->setDueDate($newDueDate);

            $this->ilmSessionManager->update($newIlmSession, false, false);
        }
    }

    /**
     * @param DateTime $origCourseStartDate
     * @param DateTime $newStartDate
     * @throws Exception
     */
    protected function compareStartDateDayOfWeek($origCourseStartDate, $newStartDate)
    {
        if ($origCourseStartDate->format('w') !== $newStartDate->format('w')) {
            throw new Exception(
                "The new start date must take place on the same day of the week as the original course start date"
                . " ({$origCourseStartDate->format('l')})."
            );
        }
    }

    /**
     * @param DateTime $origDate
     * @param int $daysOffset
     * @return DateTime
     * @throws Exception
     */
    protected function getAdjustedDate(
        $origDate,
        $daysOffset
    ) {
        $newDate = clone $origDate;
        $newInterval = 'P' . $daysOffset . 'D';
        $newDate->add(new DateInterval($newInterval));

        return $newDate;
    }
}
