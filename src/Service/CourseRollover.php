<?php

declare(strict_types=1);

namespace App\Service;

use App\Entity\CohortInterface;
use App\Entity\CourseInterface;
use App\Entity\CourseLearningMaterialInterface;
use App\Entity\CourseObjectiveInterface;
use App\Entity\IlmSessionInterface;
use App\Entity\OfferingInterface;
use App\Entity\ProgramYearObjectiveInterface;
use App\Entity\SessionInterface;
use App\Entity\SessionLearningMaterialInterface;
use App\Entity\SessionObjectiveInterface;
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
use DateInterval;
use DateTime;
use Exception;

/**
 * CourseRollover Rolls over an existing course and its components to a new Academic Year
 */
class CourseRollover
{
    public function __construct(
        protected CourseRepository $courseRepository,
        protected LearningMaterialRepository $learningMaterialRepository,
        protected CourseLearningMaterialRepository $courseLearningMaterialRepository,
        protected SessionRepository $sessionRepository,
        protected SessionLearningMaterialRepository $sessionLearningMaterialRepository,
        protected OfferingRepository $offeringRepository,
        protected IlmSessionRepository $ilmSessionRepository,
        private CohortRepository $cohortRepository,
        protected CourseObjectiveRepository $courseObjectiveRepository,
        protected SessionObjectiveRepository $sessionObjectiveRepository
    ) {
    }

    /**
     * Rollover a course
     */
    public function rolloverCourse(
        int $courseId,
        int $newAcademicYear,
        array $options,
        array $newCohortIds = []
    ): CourseInterface {
        //now, get/set the required values from the provided arguments
        $origCourseId = $courseId;
        $newStartDate = (!empty($options['new-start-date'])) ? new DateTime($options['new-start-date']) : null;

        //make sure that the new course's academic year or new start date year is not in the past
        $this->confirmYearIsValid($newAcademicYear);
        if (!empty($newStartDate)) {
            $this->confirmYearIsValid((int)$newStartDate->format('Y'));
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
        /** @var CourseInterface $newCourse */
        $newCourse = $this->courseRepository->create();
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
            $cohort = $this->cohortRepository->findOneBy(['id' => $id]);
            if (!$cohort) {
                throw new Exception("There are no cohorts with id {$id}.");
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
        $this->courseRepository->update($newCourse, false, false);

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
        $this->courseRepository->flushAndClear();

        //return the course
        return $newCourse;
    }

    protected function rolloverCourseLearningMaterials(CourseInterface $newCourse, CourseInterface $origCourse): void
    {
        $origCourseLearningMaterials = $origCourse->getLearningMaterials();

        foreach ($origCourseLearningMaterials as $origCourseLearningMaterial) {
            $newCourseLearningMaterial = $this->courseLearningMaterialRepository->create();
            $newCourseLearningMaterial->setNotes($origCourseLearningMaterial->getNotes());
            $newCourseLearningMaterial->setRequired($origCourseLearningMaterial->isRequired());
            $newCourseLearningMaterial->setPublicNotes($origCourseLearningMaterial->hasPublicNotes());
            $newCourseLearningMaterial->setCourse($newCourse);
            $newCourseLearningMaterial->setLearningMaterial($origCourseLearningMaterial->getLearningMaterial());
            $newCourseLearningMaterial->setMeshDescriptors($origCourseLearningMaterial->getMeshDescriptors());
            $newCourseLearningMaterial->setPosition($origCourseLearningMaterial->getPosition());

            $this->courseLearningMaterialRepository->update($newCourseLearningMaterial, false, false);
        }
    }

    /**
     * @param CourseObjectiveInterface[] $newCourseObjectives
     * @throws Exception
     */
    protected function rolloverSessions(
        CourseInterface $newCourse,
        CourseInterface $origCourse,
        int $daysOffset,
        array $options,
        array $newCourseObjectives
    ): void {
        $origCourseSessions = $origCourse->getSessions();

        $sessionMap = [];

        foreach ($origCourseSessions as $origCourseSession) {
            $newSession = $this->sessionRepository->create();
            $newSession->setCourse($newCourse);
            $newSession->setTitle($origCourseSession->getTitle());
            $newSession->setAttireRequired($origCourseSession->isAttireRequired());
            $newSession->setEquipmentRequired($origCourseSession->isEquipmentRequired());
            $newSession->setSessionType($origCourseSession->getSessionType());
            $newSession->setSupplemental($origCourseSession->isSupplemental());
            $newSession->setPublishedAsTbd(false);
            $newSession->setPublished(false);
            $newSession->setInstructionalNotes($origCourseSession->getInstructionalNotes());
            $newSession->setDescription($origCourseSession->getDescription());

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

            $this->sessionRepository->update($newSession, false, false);
            $sessionMap[$origCourseSession->getId()] = $newSession;
        }

        // Handle postrequisites once all sessions have been rolled over
        // This was we can be sure we have the new session to refer to
        foreach ($origCourseSessions as $origCourseSession) {
            $originalPostrequisite = $origCourseSession->getPostrequisite();
            if ($originalPostrequisite && array_key_exists($originalPostrequisite->getId(), $sessionMap)) {
                $newSession = $sessionMap[$origCourseSession->getId()];
                $newSession->setPostrequisite($sessionMap[$originalPostrequisite->getId()]);
                $this->sessionRepository->update($newSession, false, false);
            }
        }
    }


    protected function rolloverSessionLearningMaterials(
        SessionInterface $newSession,
        SessionInterface $origCourseSession
    ): void {
        $origSessionLearningMaterials = $origCourseSession->getLearningMaterials();

        foreach ($origSessionLearningMaterials as $origSessionLearningMaterial) {
            $newSessionLearningMaterial = $this->sessionLearningMaterialRepository->create();
            $newSessionLearningMaterial->setNotes($origSessionLearningMaterial->getNotes());
            $newSessionLearningMaterial->setRequired($origSessionLearningMaterial->isRequired());
            $newSessionLearningMaterial->setSession($newSession);
            $newSessionLearningMaterial->setPublicNotes($origSessionLearningMaterial->hasPublicNotes());
            $newSessionLearningMaterial->setLearningMaterial($origSessionLearningMaterial->getLearningMaterial());
            $newSessionLearningMaterial->setMeshDescriptors($origSessionLearningMaterial->getMeshDescriptors());
            $newSessionLearningMaterial->setPosition($origSessionLearningMaterial->getPosition());

            $this->sessionLearningMaterialRepository->update($newSessionLearningMaterial, false, false);
        }
    }

    /**
     * @throws Exception
     */
    protected function rolloverOfferings(
        SessionInterface $newSession,
        SessionInterface $origCourseSession,
        int $daysOffset,
        array $options
    ): void {
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

            /** @var OfferingInterface $newOffering */
            $newOffering = $this->offeringRepository->create();
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
            $this->offeringRepository->update($newOffering, false, false);
        }
    }

    private function calculateDaysOffset(
        DateTime $origCourseStartDate,
        int $origAcademicYear,
        int $newAcademicYear,
        ?DateTime $newCourseStartDate = null
    ): int {
        if (!$newCourseStartDate) {
            $isoWeekOrdinal = (int) $origCourseStartDate->format('W');
            $isoDayOrdinal = (int) $origCourseStartDate->format('N');
            $yearDiff = (int) $origCourseStartDate->format('o') - $origAcademicYear;

            $diffedYear = $newAcademicYear + $yearDiff;
            $newCourseStartDate = new DateTime();
            $newCourseStartDate->setISODate($diffedYear, $isoWeekOrdinal, $isoDayOrdinal);
        }

        return $newCourseStartDate->diff($origCourseStartDate)->days;
    }

    /**
     * @throws Exception
     */
    private function confirmYearIsValid(int $newAcademicYear): void
    {
        $lastYear = date('Y') - 1;
        if ($newAcademicYear < $lastYear) {
            throw new Exception(
                "Courses cannot be rolled over to a new year before {$lastYear}."
            );
        }
    }

    /**
     * @throws Exception
     */
    private function checkForDuplicateRollover(string $title, int $newAcademicYear): void
    {
        $duplicateCourses = $this->courseRepository->findBy(['title' => $title, 'year' => $newAcademicYear]);
        if (!empty($duplicateCourses)) {
            throw new Exception(
                "Another course with the same title and academic year already exists."
                . " If the year is correct, consider setting a new course title with '--new-course-title' option."
            );
        }
    }

    /**
     * @throws Exception
     */
    private function getOriginalCourse(int $origCourseId): CourseInterface
    {
        /** @var ?CourseInterface $origCourse */
        $origCourse = $this->courseRepository->findOneBy(['id' => $origCourseId]);
        if (empty($origCourse)) {
            throw new Exception(
                'There are no courses with courseId ' . $origCourseId . '.'
            );
        }
        return $origCourse;
    }

    protected function rolloverCourseObjectives(
        CourseInterface $newCourse,
        CourseInterface $origCourse
    ): array {
        $newCourseObjectives = [];
        $cohorts = $newCourse->getCohorts();
        foreach ($origCourse->getCourseObjectives() as $courseObjective) {
            /** @var CourseObjectiveInterface $newCourseObjective */
            $newCourseObjective = $this->courseObjectiveRepository->create();
            $newCourseObjective->setCourse($newCourse);
            $newCourseObjective->setTerms($courseObjective->getTerms());
            $newCourseObjective->setPosition($courseObjective->getPosition());
            $newCourseObjective->setTitle($courseObjective->getTitle());
            $newCourseObjective->setMeshDescriptors($courseObjective->getMeshDescriptors());
            $newCourseObjective->setAncestor($courseObjective->getAncestorOrSelf());
            foreach ($cohorts as $cohort) {
                $this->reLinkCourseObjectiveToProgramYearObjectives($courseObjective, $newCourseObjective, $cohort);
            }

            $this->courseObjectiveRepository->update($newCourseObjective, false, false);

            $newCourseObjectives[$courseObjective->getId()] = $newCourseObjective;
        }

        return $newCourseObjectives;
    }

    protected function reLinkCourseObjectiveToProgramYearObjectives(
        CourseObjectiveInterface $courseObjective,
        CourseObjectiveInterface $newCourseObjective,
        CohortInterface $cohort
    ): void {
        $programYear = $cohort->getProgramYear();
        $programYearObjectives = $programYear->getProgramYearObjectives();

        /** @var ProgramYearObjectiveInterface $programYearObjective */
        foreach ($courseObjective->getProgramYearObjectives() as $programYearObjective) {
            $ancestorId = $programYearObjective->getAncestorOrSelf()->getId();
            $newProgramYearObjectives = $programYearObjectives->filter(
                fn(ProgramYearObjectiveInterface $py) => $py->getAncestorOrSelf()->getId() === $ancestorId
            );
            if ($newProgramYearObjectives->count() > 0) {
                $newCourseObjective->addProgramYearObjective($newProgramYearObjectives->first());
            }
        }
    }

    /**
     * @param CourseObjectiveInterface[] $newCourseObjectives
     */
    protected function rolloverSessionObjectives(
        SessionInterface $newSession,
        SessionInterface $origSession,
        array $newCourseObjectives
    ): void {
        $origSession->getSessionObjectives()
            ->map(
                function (SessionObjectiveInterface $sessionObjective) use ($newSession, $newCourseObjectives): void {
                    /** @var SessionObjectiveInterface $newSessionObjective */
                    $newSessionObjective = $this->sessionObjectiveRepository->create();
                    $newSessionObjective->setSession($newSession);
                    $newSessionObjective->setTerms($sessionObjective->getTerms());
                    $newSessionObjective->setPosition($sessionObjective->getPosition());
                    $newSessionObjective->setTitle($sessionObjective->getTitle());
                    $newSessionObjective->setMeshDescriptors($sessionObjective->getMeshDescriptors());
                    $newSessionObjective->setAncestor($sessionObjective->getAncestorOrSelf());
                    $courseObjectives = $sessionObjective->getCourseObjectives()
                        ->map(
                            function (CourseObjectiveInterface $oldCourseObjective) use (
                                $newCourseObjectives
                            ) {
                                if (array_key_exists($oldCourseObjective->getId(), $newCourseObjectives)) {
                                    return $newCourseObjectives[$oldCourseObjective->getId()];
                                }
                                return null;
                            }
                        )->filter(fn($value) => !empty($value));

                    $newSessionObjective->setCourseObjectives($courseObjectives);
                    $this->sessionObjectiveRepository->update($newSessionObjective, false, false);
                }
            );
    }

    /**
     * @throws Exception
     */
    protected function rolloverIlmSession(
        SessionInterface $newSession,
        SessionInterface $origSession,
        int $daysOffset
    ): void {
        if ($origIlmSession = $origSession->getIlmSession()) {
            /** @var IlmSessionInterface $newIlmSession */
            $newIlmSession = $this->ilmSessionRepository->create();
            $newIlmSession->setHours($origIlmSession->getHours());
            $newSession->setIlmSession($newIlmSession);
            $newDueDate = $this->getAdjustedDate(
                $origIlmSession->getDueDate(),
                $daysOffset
            );
            $newIlmSession->setDueDate($newDueDate);

            $this->ilmSessionRepository->update($newIlmSession, false, false);
        }
    }

    /**
     * @throws Exception
     */
    protected function compareStartDateDayOfWeek(DateTime $origCourseStartDate, DateTime $newStartDate): void
    {
        if ($origCourseStartDate->format('w') !== $newStartDate->format('w')) {
            throw new Exception(
                "The new start date must take place on the same day of the week as the original course start date"
                . " ({$origCourseStartDate->format('l')})."
            );
        }
    }

    protected function getAdjustedDate(
        DateTime $origDate,
        int $daysOffset
    ): DateTime {
        $newDate = clone $origDate;
        $newInterval = 'P' . $daysOffset . 'D';
        $newDate->add(new DateInterval($newInterval));

        return $newDate;
    }
}
