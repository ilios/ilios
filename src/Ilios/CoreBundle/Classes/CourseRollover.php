<?php

namespace Ilios\CoreBundle\Classes;

use Doctrine\ORM\EntityManagerInterface;
use Ilios\CoreBundle\Entity\Manager\CourseManagerInterface;
use Ilios\CoreBundle\Entity\Manager\LearningMaterialManagerInterface;
use Ilios\CoreBundle\Entity\Manager\CourseLearningMaterialManagerInterface;
use Ilios\CoreBundle\Entity\Manager\SessionManagerInterface;
use Ilios\CoreBundle\Entity\Manager\SessionLearningMaterialManagerInterface;
use Ilios\CoreBundle\Entity\Manager\OfferingManagerInterface;

//use Ilios\CoreBundle\Entity\Course;

/**
 * Class CourseRollover
 * @package Ilios\CoreBundle\Classes
 */
class CourseRollover {

    /**
     * @var EntityManagerInterface
     */
    protected $entityManager;

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
     * @param EntityManagerInterface $entityManager
     * @param CourseManagerInterface $courseManager
     * @param LearningMaterialManagerInterface $learningMaterialManager
     * @param CourseLearningMaterialManagerInterface $courseLearningMaterialManager
     * @param SessionManagerInterface $sessionManager
     * @param SessionLearningMaterialManagerInterface $sessionLearningMaterialManager
     * @param OfferingManagerInterface $offeringManager
     */
    public function __construct(
        EntityManagerInterface $entityManager,
        CourseManagerInterface $courseManager,
        LearningMaterialManagerInterface $learningMaterialManager,
        CourseLearningMaterialManagerInterface $courseLearningMaterialManager,
        SessionManagerInterface $sessionManager,
        SessionLearningMaterialManagerInterface $sessionLearningMaterialManager,
        OfferingManagerInterface $offeringManager

    ) {
        $this->em = $entityManager;
        $this->courseManager = $courseManager;
        $this->learningMaterialManager = $learningMaterialManager;
        $this->courseLearningMaterialManager = $courseLearningMaterialManager;
        $this->sessionManager = $sessionManager;
        $this->sessionLearningMaterialManager = $sessionLearningMaterialManager;
        $this->offeringManager = $offeringManager;


    }

    /**
     * @param $args
     * @param $options
     * @return Course
     * @throws \Exception
     */
    public function rolloverCourse($args, $options)
    {

        //set the arguments and options of the command to be globally-accessible by the class
        $this->args = $args;
        $this->options = $options;

        //now, get/set the required values from the provided arguments
        $originalCourseId = $args['courseId'];
        $newAcademicYear = $args['newAcademicYear'];
        $newStartDate = (!empty($options['new-start-date'])) ? new \DateTime($options['new-start-date']) : null;

        //make sure that the new course's academic year or new start date year is not in the past
        $this->confirmYearIsNotInPast($newAcademicYear);
        if(!empty($newStartDate)) $this->confirmYearIsNotInPast($newStartDate->format('Y'));

        //get the original course object
        $originalCourse = $this->courseManager->findCourseBy(['id'=>$originalCourseId]);

        //before creating the newCourse object, check for courses with same title & year, so a rollover is not run 2x
        $this->checkForDuplicateRollover($originalCourse->getTitle(), $args['newAcademicYear']);

        //get/set the original course's start/end dates for use in calculation of offset
        $originalCourseStartDate = $originalCourse->getStartDate();
        $originalCourseEndDate = $originalCourse->getEndDate();

        $offsetInWeeks = $this->calculateRolloverOffsetInWeeks($originalCourse, $newAcademicYear, $newStartDate);

        //set up the $newCourse values that need some pre-processing
        $newCourseStartDate = $originalCourseStartDate->modify('+ ' . $offsetInWeeks . ' weeks');
        $newCourseEndDate = $originalCourseEndDate->modify('+ ' . $offsetInWeeks . ' weeks');

        //create the Course
        //if there are not any duplicates, create a new course with the relevant info
        $newCourse = $this->courseManager->createCourse();
        $newCourse->setTitle($originalCourse->getTitle());
        $newCourse->setLevel($originalCourse->getLevel());
        $newCourse->setYear($args['newAcademicYear']);
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
        $this->em->persist($newCourse);

        //now run each of the subcomponents, starting with the course-specific ones

        //COURSE LEARNING MATERIALS
        if(empty($this->options['skip-course-learning-materials'])) {
            $this->rolloverCourseLearningMaterials($newCourse, $originalCourse);
        }

        //SESSIONS
        if(empty($this->options['skip-sessions'])) {
            $this->rolloverSessions($newCourse, $originalCourse);
        }

        //commit EVERYTHING to the database
        $this->em->flush($newCourse);

        //return the new courseId
        return $newCourse->getId();

    }

    /**
     * @param Course $newCourse
     * @param Course $originalCourse
     */
    protected function rolloverCourseLearningMaterials($newCourse, $originalCourse) {

        $originalCourseLearningMaterials = $this->courseLearningMaterialManager->findCourseLearningMaterialsBy(['course'=>$originalCourse]);
        foreach($originalCourseLearningMaterials as $originalCourseLearningMaterial) {

            $newCourseLearningMaterial = $this->courseLearningMaterialManager->createCourseLearningMaterial();
            $newCourseLearningMaterial->setNotes($originalCourseLearningMaterial->getNotes());
            $newCourseLearningMaterial->setRequired($originalCourseLearningMaterial->isRequired());
            $newCourseLearningMaterial->setPublicNotes($originalCourseLearningMaterial->hasPublicNotes());
            $newCourseLearningMaterial->setCourse($newCourse);
            $newCourseLearningMaterial->setLearningMaterial($originalCourseLearningMaterial->getLearningMaterial());
            $newCourseLearningMaterial->setMeshDescriptors($originalCourseLearningMaterial->getMeshDescriptors());
            
            $this->em->persist($newCourseLearningMaterial);
        }

    }

    /**
     * @param $newCourse
     * @param $originalCourse
     */
    protected function rolloverSessions($newCourse, $originalCourse) {

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
            if(empty($this->options['skip-session-learning-materials'])) {
                $this->rolloverSessionLearningMaterials($newSession, $originalCourseSession);
            }

            //SESSION OBJECTIVES
            if(empty($this->options['skip-session-objectives'])) {
                $newSession->setObjectives($originalCourseSession->getObjectives());
            }

            //SESSION TOPICS
            if(empty($this->options['skip-session-topics'])) {
                $newSession->setTerms($originalCourseSession->getTerms());
            }

            //SESSION MESH TERMS
            if(empty($this->options['skip-session-mesh'])) {
                $newSession->setMeshDescriptors($originalCourseSession->getMeshDescriptors());
            }

            $this->em->persist($newSession);
        }
    }


    /**
     * @param $newSession
     * @param $originalCourseSession
     */
    protected function rolloverSessionLearningMaterials($newSession, $originalCourseSession) {

        $originalSessionLearningMaterials = $this->sessionLearningMaterialManager->findSessionLearningMaterialsBy(['session'=>$originalCourseSession]);

        foreach($originalSessionLearningMaterials as $originalSessionLearningMaterial) {

            $newSessionLearningMaterial = $this->sessionLearningMaterialManager->createSessionLearningMaterial();
            $newSessionLearningMaterial->setNotes($originalSessionLearningMaterial->getNotes());
            $newSessionLearningMaterial->setRequired($originalSessionLearningMaterial->isRequired());
            $newSessionLearningMaterial->setSession($newSession);
            $newSessionLearningMaterial->setPublicNotes($originalSessionLearningMaterial->hasPublicNotes());
            $newSessionLearningMaterial->setLearningMaterial($originalSessionLearningMaterial->getLearningMaterial());
            $newSessionLearningMaterial->setMeshDescriptors($originalSessionLearningMaterial->getMeshDescriptors());

            $this->em->persist($newSessionLearningMaterial);
        }
    }

    /**
     * @param Course $originalCourse
     * @param \DateTime $newAcademicYear
     * @param \DateTime|null $newStartDate
     * @return int|null
     */
    private function calculateRolloverOffsetInWeeks ($originalCourse, $newAcademicYear, $newStartDate = null) {


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
        $weeksBetweenTwoDates = ($weeksUntilNewYear + $newStartWeekOrdinal);

        switch($academicYearDifference) {
            //if the year diff is 0, it is the same year,
            //so just take the difference between the two weeks
            case 0:
                $weeksToAdd = ($newStartWeekOrdinal - $originalStartWeekOrdinal);
                break;
            //if there is only 1 year difference, get the weeks left of the first year
            //and add them to the week ordinal of the new start date
            case 1:
                $weeksToAdd = $weeksBetweenTwoDates;
                break;
            //if the difference is greater than 1 year, multiply each ADDITIONAL year (after the 1st year)
            //by 52 weeks, and add this to the total weeks between the two dates
            default:
                $weekYearMultiplier = (52 * ($academicYearDifference - 1));
                $weeksToAdd = ($weeksBetweenTwoDates + $weekYearMultiplier);
        }

        return $weeksToAdd;
    }

    /**
     * @param $newAcademicYear
     * @throws \Exception
     */
    private function confirmYearIsNotInPast($newAcademicYear) {

        $currentYear = date('Y');
        if ($newAcademicYear < $currentYear) {
            throw new \Exception(
                "You cannot rollover a course to a new year or start date that is already in the past."
            );
        }
    }

    /**
     * @param $title
     * @param $newAcademicYear
     * @throws \Exception
     */
    private function checkForDuplicateRollover($title, $newAcademicYear) {

        $duplicateCourses = $this->courseManager->findCoursesBy(['title'=>$title, 'year'=>$newAcademicYear]);
        if (count($duplicateCourses) > 0) {
            throw new \Exception(
                "Another course with the same title and academic year already exists."
            );
        }
    }

}
