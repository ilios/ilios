<?php

namespace Ilios\CoreBundle\Classes;

use Ilios\CoreBundle\Entity\CourseInterface;
use Ilios\CoreBundle\Entity\Manager\CourseManagerInterface;
use Ilios\CoreBundle\Entity\Manager\LearningMaterialManagerInterface;
use Ilios\CoreBundle\Entity\Manager\CourseLearningMaterialManagerInterface;
use Ilios\CoreBundle\Entity\Manager\SessionManagerInterface;
use Ilios\CoreBundle\Entity\Manager\SessionLearningMaterialManagerInterface;
use Ilios\CoreBundle\Entity\Manager\OfferingManagerInterface;
use Ilios\CoreBundle\Entity\SessionInterface;

/**
 * Class CourseRollover
 * @package Ilios\CoreBundle\Classes
 */
class CourseRollover {

    /**
     * @var CourseManagerInterface
     */
    protected $courseManager;

    /**
     * @var LearningMaterialManagerInterface
     */
    protected $learningMaterialManager;

    /**
     * @var CourseLearningMaterialManagerInterface;
     */
    protected $courseLearningMaterialManager;

    /**
     * @var SessionManagerInterface
     */
    protected $sessionManager;

    /**
     * @var SessionLearningMaterialManagerInterface;
     */
    protected $sessionLearningMaterialManager;

    /**
     * @var OfferingManagerInterface
     */
    protected $offeringManager;

    /**
     * CourseRollover constructor.
     * @param CourseManagerInterface $courseManager
     * @param LearningMaterialManagerInterface $learningMaterialManager
     * @param CourseLearningMaterialManagerInterface $courseLearningMaterialManager
     * @param SessionManagerInterface $sessionManager
     * @param SessionLearningMaterialManagerInterface $sessionLearningMaterialManager
     * @param OfferingManagerInterface $offeringManager
     */
    public function __construct(
        CourseManagerInterface $courseManager,
        LearningMaterialManagerInterface $learningMaterialManager,
        CourseLearningMaterialManagerInterface $courseLearningMaterialManager,
        SessionManagerInterface $sessionManager,
        SessionLearningMaterialManagerInterface $sessionLearningMaterialManager,
        OfferingManagerInterface $offeringManager

    ) {
        $this->courseManager = $courseManager;
        $this->learningMaterialManager = $learningMaterialManager;
        $this->courseLearningMaterialManager = $courseLearningMaterialManager;
        $this->sessionManager = $sessionManager;
        $this->sessionLearningMaterialManager = $sessionLearningMaterialManager;
        $this->offeringManager = $offeringManager;

    }

    /**
     * @param int $courseId
     * @param int $newAcademicYear
     * @param array $options
     * @return mixed
     * @throws \Exception
     */
    public function rolloverCourse($courseId, $newAcademicYear, $options)
    {
        //now, get/set the required values from the provided arguments
        $originalCourseId = $courseId;
        $newStartDate = (!empty($options['new-start-date'])) ? new \DateTime($options['new-start-date']) : null;

        //make sure that the new course's academic year or new start date year is not in the past
        $this->confirmYearIsNotInPast($newAcademicYear);
        if(!empty($newStartDate)){
            $this->confirmYearIsNotInPast($newStartDate->format('Y'));
        }

        //get the original course object
        $originalCourse = $this->getOriginalCourse($originalCourseId);

        //if a new title is to be used, update before checking for duplicates
        if(!empty($options['new-course-title'])){
            $originalCourse->setTitle($options['new-course-title']);
        }

        //before creating the newCourse object, check for courses with same title & year, so a rollover is not run 2x
        $this->checkForDuplicateRollover($originalCourse->getTitle(), $newAcademicYear);

        //get/set the original course's start/end dates for use in calculation of offset
        $originalCourseStartDate = $originalCourse->getStartDate();
        $originalCourseEndDate = $originalCourse->getEndDate();

        //get/set the offset in weeks and its +/- sign and set it as globally-accessible string in the class
        $weeksOffset = $this->calculateRolloverOffsetInWeeks($originalCourse, $newAcademicYear, $newStartDate);
        $weeksOffsetModifier = gmp_sign($weeksOffset);
        $offsetInWeeks = (($weeksOffsetModifier < 0) ? '-' : '+') . ' ' . abs($weeksOffset) . ' weeks';

        //create/modify the newCourse start and end dates based on the original dates and the offset in weeks
        $newCourseStartDate = clone $originalCourseStartDate;
        $newCourseStartDate->modify($offsetInWeeks);
        $newCourseEndDate = clone $originalCourseEndDate;
        $newCourseEndDate->modify($offsetInWeeks);

        //create the Course
        //if there are not any duplicates, create a new course with the relevant info
        $newCourse = $this->courseManager->createCourse();
        $newCourse->setTitle($originalCourse->getTitle());
        $newCourse->setLevel($originalCourse->getLevel());
        $newCourse->setYear($newAcademicYear);
        $newCourse->setStartDate($newCourseStartDate);
        $newCourse->setEndDate($newCourseEndDate);
        $newCourse->setExternalId($originalCourse->getExternalId());
        $newCourse->setLocked(0);
        $newCourse->setArchived(0);
        $newCourse->setPublishedAsTbd(0);
        $newCourse->setPublished(0);
        $newCourse->setClerkshipType($originalCourse->getClerkshipType());
        $newCourse->setSchool($originalCourse->getSchool());
        $newCourse->setDirectors($originalCourse->getDirectors());
        $newCourse->setTerms($originalCourse->getTerms());
        $newCourse->setObjectives($originalCourse->getObjectives());
        $newCourse->setMeshDescriptors($originalCourse->getMeshDescriptors());

        //persist the newCourse entity
        $this->courseManager->updateCourse($newCourse, false, false);

        //now run each of the subcomponents, starting with the course-specific ones
        //COURSE LEARNING MATERIALS
        if(empty($options['skip-course-learning-materials'])) {
            $this->rolloverCourseLearningMaterials($newCourse, $originalCourse);
        }

        //SESSIONS
        if(empty($options['skip-sessions'])) {
            $this->rolloverSessions($newCourse, $originalCourse, $options, $offsetInWeeks);
        }

        //commit EVERYTHING to the database
        $this->courseManager->flushAndClear();

        //return the new courseId
        return $newCourse->getId();
    }

    /**
     * @param CourseInterface $newCourse
     * @param CourseInterface $originalCourse
     */
    protected function rolloverCourseLearningMaterials(CourseInterface $newCourse, CourseInterface $originalCourse)
    {
        $originalCourseLearningMaterials
            = $this->courseLearningMaterialManager->findCourseLearningMaterialsBy(['course'=>$originalCourse]);

        foreach($originalCourseLearningMaterials as $originalCourseLearningMaterial) {
            $newCourseLearningMaterial = $this->courseLearningMaterialManager->createCourseLearningMaterial();
            $newCourseLearningMaterial->setNotes($originalCourseLearningMaterial->getNotes());
            $newCourseLearningMaterial->setRequired($originalCourseLearningMaterial->isRequired());
            $newCourseLearningMaterial->setPublicNotes($originalCourseLearningMaterial->hasPublicNotes());
            $newCourseLearningMaterial->setCourse($newCourse);
            $newCourseLearningMaterial->setLearningMaterial($originalCourseLearningMaterial->getLearningMaterial());
            $newCourseLearningMaterial->setMeshDescriptors($originalCourseLearningMaterial->getMeshDescriptors());
            
            $this->courseLearningMaterialManager->updateCourseLearningMaterial($newCourseLearningMaterial, false, false);
        }
    }

    /**
     * @param CourseInterface $newCourse
     * @param CourseInterface $originalCourse
     * @param array $options
     * @param int $offsetInWeeks
     */
    protected function rolloverSessions(
        CourseInterface $newCourse,
        CourseInterface $originalCourse,
        $options,
        $offsetInWeeks
    ) {
        $originalCourseSessions = $this->sessionManager->findSessionsBy(['course'=>$originalCourse]);

        foreach ($originalCourseSessions as $originalCourseSession) {

            $newSession = $this->sessionManager->createSession();
            $newSession->setCourse($newCourse);
            $newSession->setTitle($originalCourseSession->getTitle());
            $newSession->setAttireRequired($originalCourseSession->isAttireRequired());
            $newSession->setEquipmentRequired($originalCourseSession->isEquipmentRequired());
            $newSession->setSessionType($originalCourseSession->getSessionType());
            $newSession->setSupplemental($originalCourseSession->isSupplemental());
            $newSession->setPublishedAsTbd(0);
            $newSession->setPublished(0);

            //SESSION LEARNING MATERIALS
            if(empty($options['skip-session-learning-materials'])) {
                $this->rolloverSessionLearningMaterials($newSession, $originalCourseSession);
            }

            //SESSION OBJECTIVES
            if(empty($options['skip-session-objectives'])) {
                $newSession->setObjectives($originalCourseSession->getObjectives());
            }

            //SESSION TOPICS
            if(empty($options['skip-session-topics'])) {
                $newSession->setTerms($originalCourseSession->getTerms());
            }

            //SESSION MESH TERMS
            if(empty($options['skip-session-mesh'])) {
                $newSession->setMeshDescriptors($originalCourseSession->getMeshDescriptors());
            }

            //Offerings
            if(empty($options['skip-offerings'])) {
                $this->rolloverOfferings($newSession, $originalCourseSession, $options, $offsetInWeeks);
            }

            $this->sessionManager->updateSession($newSession, false, false);
        }
    }


    /**
     * @param $newSession
     * @param $originalCourseSession
     */
    protected function rolloverSessionLearningMaterials($newSession, $originalCourseSession)
    {
        $originalSessionLearningMaterials = $this->sessionLearningMaterialManager
                                                ->findSessionLearningMaterialsBy(['session'=>$originalCourseSession]);

        foreach($originalSessionLearningMaterials as $originalSessionLearningMaterial) {

            $newSessionLearningMaterial = $this->sessionLearningMaterialManager->createSessionLearningMaterial();
            $newSessionLearningMaterial->setNotes($originalSessionLearningMaterial->getNotes());
            $newSessionLearningMaterial->setRequired($originalSessionLearningMaterial->isRequired());
            $newSessionLearningMaterial->setSession($newSession);
            $newSessionLearningMaterial->setPublicNotes($originalSessionLearningMaterial->hasPublicNotes());
            $newSessionLearningMaterial->setLearningMaterial($originalSessionLearningMaterial->getLearningMaterial());
            $newSessionLearningMaterial->setMeshDescriptors($originalSessionLearningMaterial->getMeshDescriptors());

            $this->sessionLearningMaterialManager->updateSessionLearningMaterial(
                $newSessionLearningMaterial,
                false,
                false
            );
        }
    }

    /**
     * @param SessionInterface $newSession
     * @param SessionInterface $originalCourseSession
     * @param array $options
     * @param int $offsetInWeeks
     */
    protected function rolloverOfferings(
        SessionInterface $newSession,
        SessionInterface $originalCourseSession,
        $options,
        $offsetInWeeks
    ) {
        $originalSessionOfferings = $this->offeringManager->findOfferingsBy(['session'=>$originalCourseSession]);

        foreach($originalSessionOfferings as $originalSessionOffering) {

            //preprocess the offering start/end dates
            $newOfferingStartDate = ($originalSessionOffering->getStartDate()->modify($offsetInWeeks));
            $newOfferingEndDate = ($originalSessionOffering->getEndDate()->modify($offsetInWeeks));

            $newOffering = $this->offeringManager->createOffering();
            $newOffering->setRoom($originalSessionOffering->getRoom());
            $newOffering->setStartDate($newOfferingStartDate);
            $newOffering->setEndDate($newOfferingEndDate);
            $newOffering->setUpdatedAt(new \DateTime);
            $newOffering->setSession($newSession);

            //Instructors
            if(empty($options['skip-instructors'])) {
                $newOffering->setInstructors($originalSessionOffering->getInstructors());
            }

            //Instructor Groups
            if(empty($options['skip-instructor-groups'])) {
                $newOffering->setInstructorGroups($originalSessionOffering->getInstructorGroups());
            }
            $this->offeringManager->updateOffering($newOffering, false, false);
        }
    }

    /**
     * @param CourseInterface $originalCourse
     * @param int $newAcademicYear
     * @param \DateTime|null $newStartDate
     * @return int|null
     */
    private function calculateRolloverOffsetInWeeks(
        CourseInterface $originalCourse,
        $newAcademicYear,
        $newStartDate = null
    ) {
        //get the difference between the academic years of each course.
        $academicYearDifference = ($newAcademicYear - $originalCourse->getYear());
        $originalStartWeekOrdinal = $originalCourse->getStartDate()->format('W');
        $newStartWeekOrdinal = (!empty($newStartDate)) ? $newStartDate->format('W') : null;

        //if no start week is given, then multiply the academicYearDifference by 52 weeks for each year
        if(empty($newStartWeekOrdinal)) {
            return ($academicYearDifference * 52);
        }

        //get the remaining number of weeks remaining in the year from the orig start date
        $weeksUntilNewYear = (52 - $originalStartWeekOrdinal);

        //get the number of weeks between two dates within one year cycle
        //if year diff is 0, but the two dates are in same calendar year, don't factor in a year change
        if($originalCourse->getStartDate()->format('Y') === $newStartDate->format('Y')) {
            $weeksBetweenTwoDates = ($newStartWeekOrdinal - $originalStartWeekOrdinal);
        } else {
            //if the academic year is the same, but calendar years are different, calculate for year change
            $weeksBetweenTwoDates = ($weeksUntilNewYear + $newStartWeekOrdinal);
        }

        //if the difference in Academic years is greater than 1, calculate for multiple years
        if($academicYearDifference > 1) {
            //don't count the current year for the weekYear multiplication
            $weekYearMultiplier = (52 * ($academicYearDifference - 1));
            //instead, calculate the proper shift between the dates and then add the additional weekYears
            $weeksOffset = ($weeksBetweenTwoDates + $weekYearMultiplier);
        } else {
            //if the difference is 0 years or 1 year, just use the $weeksBetweenTwoDates result from above
            $weeksOffset = $weeksBetweenTwoDates;
        }

        return $weeksOffset;
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
        $duplicateCourses = $this->courseManager->findCoursesBy(['title'=>$title, 'year'=>$newAcademicYear]);
        if (!empty($duplicateCourses)) {
            throw new \Exception(
                "Another course with the same title and academic year already exists."
                . " If the year is correct, consider setting a new course title with '--new-course-title' option."
            );
        }
    }

    /**
     * @param int $originalCourseId
     * @return \Ilios\CoreBundle\Entity\CourseInterface
     * @throws \Exception
     */
    private function getOriginalCourse($originalCourseId)
    {
        $originalCourse = $this->courseManager->findCourseBy(['id'=>$originalCourseId]);
        if(empty($originalCourse)) {
            throw new \Exception(
                'There are no courses with courseId ' . $originalCourseId . '.'
            );
        }
        return $originalCourse;
    }

}
