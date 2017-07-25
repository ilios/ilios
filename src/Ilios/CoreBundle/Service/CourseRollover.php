<?php

namespace Ilios\CoreBundle\Service;

use Ilios\CoreBundle\Entity\CourseInterface;
use Ilios\CoreBundle\Entity\CourseLearningMaterialInterface;
use Ilios\CoreBundle\Entity\IlmSessionInterface;
use Ilios\CoreBundle\Entity\Manager\CourseLearningMaterialManager;
use Ilios\CoreBundle\Entity\Manager\CourseManager;
use Ilios\CoreBundle\Entity\Manager\IlmSessionManager;
use Ilios\CoreBundle\Entity\Manager\LearningMaterialManager;
use Ilios\CoreBundle\Entity\Manager\ObjectiveManager;
use Ilios\CoreBundle\Entity\Manager\OfferingManager;
use Ilios\CoreBundle\Entity\Manager\SessionLearningMaterialManager;
use Ilios\CoreBundle\Entity\Manager\SessionManager;
use Ilios\CoreBundle\Entity\OfferingInterface;
use Ilios\CoreBundle\Entity\SessionInterface;
use Ilios\CoreBundle\Entity\Manager\SessionDescriptionManager;
use Ilios\CoreBundle\Entity\SessionDescriptionInterface;
use Ilios\CoreBundle\Entity\SessionLearningMaterialInterface;
use Ilios\CoreBundle\Entity\ObjectiveInterface;

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
     * @var OfferingManager
     */
    protected $offeringManager;

    /**
     * @var ObjectiveManager
     */
    protected $objectiveManager;

    /**
     * @var IlmSessionManager
     */
    protected $ilmSessionManager;

    /**
     * CourseRollover constructor.
     *
     * @param CourseManager $courseManager,
     * @param LearningMaterialManager $learningMaterialManager,
     * @param CourseLearningMaterialManager $courseLearningMaterialManager,
     * @param SessionManager $sessionManager,
     * @param SessionDescriptionManager $sessionDescriptionManager,
     * @param SessionLearningMaterialManager $sessionLearningMaterialManager,
     * @param OfferingManager $offeringManager,
     * @param ObjectiveManager $objectiveManager,
     * @param IlmSessionManager $ilmSessionManager
     */
    public function __construct(
        CourseManager $courseManager,
        LearningMaterialManager $learningMaterialManager,
        CourseLearningMaterialManager $courseLearningMaterialManager,
        SessionManager $sessionManager,
        SessionDescriptionManager $sessionDescriptionManager,
        SessionLearningMaterialManager $sessionLearningMaterialManager,
        OfferingManager $offeringManager,
        ObjectiveManager $objectiveManager,
        IlmSessionManager $ilmSessionManager
    ) {
        $this->courseManager = $courseManager;
        $this->learningMaterialManager = $learningMaterialManager;
        $this->courseLearningMaterialManager = $courseLearningMaterialManager;
        $this->sessionManager = $sessionManager;
        $this->sessionDescriptionManager = $sessionDescriptionManager;
        $this->sessionLearningMaterialManager = $sessionLearningMaterialManager;
        $this->offeringManager = $offeringManager;
        $this->objectiveManager = $objectiveManager;
        $this->ilmSessionManager = $ilmSessionManager;
    }

    /**
     * @param int   $courseId
     * @param int   $newAcademicYear
     * @param array $options
     * @return CourseInterface the new, rolled-over course.
     * @throws \Exception
     */
    public function rolloverCourse($courseId, $newAcademicYear, $options)
    {
        //now, get/set the required values from the provided arguments
        $origCourseId = $courseId;
        $newStartDate = (!empty($options['new-start-date'])) ? new \DateTime($options['new-start-date']) : null;

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

            $this->courseLearningMaterialManager->update($newCourseLearningMaterial, false, false);
        }
    }

    /**
     * @param CourseInterface $newCourse
     * @param CourseInterface $origCourse
     * @param int $daysOffset
     * @param array $options
     * @param array $newCourseObjectives
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

            $this->sessionLearningMaterialManager->update($newSessionLearningMaterial, false, false);
        }
    }

    /**
     * @param SessionInterface $newSession
     * @param SessionInterface $origCourseSession
     * @param $daysOffset
     * @param $options
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
     * @param \DateTime      $origCourseStartDate
     * @param integer        $origAcademicYear
     * @param integer        $newAcademicYear
     * @param \DateTime|null $newCourseStartDate
     * @return int
     */
    private function calculateDaysOffset(
        $origCourseStartDate,
        $origAcademicYear,
        $newAcademicYear,
        $newCourseStartDate
    ) {
        if (!$newCourseStartDate) {
            $isoWeekOrdinal = $origCourseStartDate->format('W');
            $isoDayOrdinal = $origCourseStartDate->format('N');
            $yearDiff = (int) $origCourseStartDate->format('Y') - $origAcademicYear;

            $diffedYear = $newAcademicYear + $yearDiff;
            $newCourseStartDate = new \DateTime();
            $newCourseStartDate->setISODate($diffedYear, $isoWeekOrdinal, $isoDayOrdinal);
        }

        return $newCourseStartDate->diff($origCourseStartDate)->days;
    }

    /**
     * @param int $newAcademicYear
     * @throws \Exception
     */
    private function confirmYearIsValid($newAcademicYear)
    {
        $lastYear = date('Y') - 1;
        if ($newAcademicYear < $lastYear) {
            throw new \Exception(
                "Courses cannot be rolled over to a new year before {$lastYear}."
            );
        }
    }

    /**
     * @param string $title
     * @param int    $newAcademicYear
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
     * @param int $origCourseId
     * @return CourseInterface
     * @throws \Exception
     */
    private function getOriginalCourse($origCourseId)
    {
        $origCourse = $this->courseManager->findOneBy(['id' => $origCourseId]);
        if (empty($origCourse)) {
            throw new \Exception(
                'There are no courses with courseId ' . $origCourseId . '.'
            );
        }
        return $origCourse;
    }

    /**
     * @param CourseInterface $newCourse
     * @param CourseInterface $origCourse
     *
     * @return array
     */
    protected function rolloverCourseObjectives(
        CourseInterface $newCourse,
        CourseInterface $origCourse
    ) {
        $newCourseObjectives = [];
        foreach ($origCourse->getObjectives() as $objective) {
            /* @var ObjectiveInterface $newObjective */
            $newObjective = $this->objectiveManager->create();
            $newObjective->setTitle($objective->getTitle());
            $newObjective->setMeshDescriptors($objective->getMeshDescriptors());
            $newObjective->addCourse($newCourse);
            $newObjective->setAncestor($objective->getAncestorOrSelf());
            $this->objectiveManager->update($newObjective, false, false);
            $newCourseObjectives[$objective->getId()] = $newObjective;
        }

        return $newCourseObjectives;
    }

    /**
     * @param SessionInterface     $newSession
     * @param SessionInterface     $origSession
     * @param ObjectiveInterface[] $newCourseObjectives
     */
    protected function rolloverSessionObjectives(
        SessionInterface $newSession,
        SessionInterface $origSession,
        array $newCourseObjectives
    ) {
        $origSession->getObjectives()
            ->map(
                function (ObjectiveInterface $objective) use ($newSession, $newCourseObjectives) {
                    /* @var ObjectiveInterface $newObjective */
                    $newObjective = $this->objectiveManager->create();
                    $newObjective->setTitle($objective->getTitle());
                    $newObjective->setMeshDescriptors($objective->getMeshDescriptors());
                    $newObjective->addSession($newSession);
                    $newObjective->setAncestor($objective->getAncestorOrSelf());
                    $newParents = $objective->getParents()
                        ->map(
                            function (ObjectiveInterface $oldParent) use ($newCourseObjectives, $objective) {
                                if (array_key_exists($oldParent->getId(), $newCourseObjectives)) {
                                    return $newCourseObjectives[$oldParent->getId()];
                                }

                                return null;
                            }
                        )->filter(
                            function ($value) {
                                return !empty($value);
                            }
                        );

                    $newObjective->setParents($newParents);
                    $this->objectiveManager->update($newObjective, false, false);
                }
            );
    }

    /**
     * @param SessionInterface     $newSession
     * @param SessionInterface     $origSession
     * @param $daysOffset
     */
    protected function rolloverIlmSession(
        SessionInterface $newSession,
        SessionInterface $origSession,
        $daysOffset
    ) {
        /* @var IlmSessionInterface $origIlmSession */
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
     * @param \DateTime $origCourseStartDate
     * @param \DateTime $newStartDate
     * @throws \Exception
     */
    protected function compareStartDateDayOfWeek($origCourseStartDate, $newStartDate)
    {
        if ($origCourseStartDate->format('w') !== $newStartDate->format('w')) {
            throw new \Exception(
                "The new start date must take place on the same day of the week as the original course start date"
                . " ({$origCourseStartDate->format('l')})."
            );
        }
    }

    /**
     * @param \DateTime $origDate
     * @param int       $daysOffset
     * @return \DateTime
     */
    protected function getAdjustedDate(
        $origDate,
        $daysOffset
    ) {
        $newDate = clone $origDate;
        $newInterval = 'P' . $daysOffset . 'D';
        $newDate->add(new \DateInterval($newInterval));

        return $newDate;
    }
}
