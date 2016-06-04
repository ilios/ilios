<?php

namespace Ilios\CoreBundle\Classes;


use Ilios\CoreBundle\Entity\Manager\CourseManagerInterface;
use Ilios\CoreBundle\Entity\Manager\SessionManagerInterface;
use Ilios\CoreBundle\Entity\Manager\OfferingManagerInterface;

use Ilios\CoreBundle\Entity\Course;
use Ilios\CoreBundle\Entity\Session;
use Ilios\CoreBundle\Entity\Offering;


class CourseRollover {

    /**
     * @var CourseManagerInterface
     */
    protected $courseManager;

    /**
     * @var SessionManagerInterface
     */
    protected $sessionManager;

    /**
     * @var OfferingManagerInterface
     */
    protected $offeringManager;

    /**
     * @param CourseManagerInterface $courseManager
     * @param SessionManagerInterface $sessionManager
     * @param OfferingManagerInterface $offeringManager
     */
    public function __construct(
        CourseManagerInterface $courseManager,
        SessionManagerInterface $sessionManager,
        OfferingManagerInterface $offeringManager
    ) {
        $this->courseManager = $courseManager;
        $this->sessionManager = $sessionManager;
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

        //get/set the required values from the provided arguments
        $originalCourseId = $args['courseId'];
        $newAcademicYear = $args['newAcademicYear'];
        $newStartDate = (!empty($args['new-start-date'])) ? new DateTime($args['new-start-date']) : null;

        //make sure that the new course's academic year is not in the past
        $this->confirmYearIsNotInPast($newAcademicYear);

        //get the original course object
        $originalCourse = $this->courseManager->findCourseBy(['id'=>$originalCourseId]);

        //get/set the original course's start date and year for use in calculation of offset
        $originalCourseStartDate = $originalCourse->getStartDate();

        //before creating the course object, check for courses with same title and year, so rollover is not run twice
        $this->checkForDuplicateRollover($originalCourse->getTitle(), $args['newAcademicYear']);

        //$offsetInWeeks = calculateRolloverOffsetInWeeks($originalCourseAcademicYear, $originalCourseStartDate, $newCourseAcademicYear, $newCourseStartDate);
        $offsetInWeeks = $this->calculateRolloverOffsetInWeeks($originalCourse, $newAcademicYear);

        //set up the $newCourse values that need some pre-processing
        $newCourseStartDate = date_create($originalCourseStartDate->format('Y-m-d'));
        $newCourseStartDate->modify('+ ' . $offsetInWeeks . ' weeks');
        $newCourseEndDate = date_create($originalCourse->getEndDate()->format('Y-m-d'));
        $newCourseEndDate->modify('+ ' . $offsetInWeeks . ' weeks');

        //create the Course
        //if there are not any duplicates, create a new course with the relevant info
        $newCourse = new Course();
        $newCourse->setTitle($originalCourse->getTitle());
        $newCourse->setYear($args['newAcademicYear']);
        $newCourse->setLevel($originalCourse->getLevel());
        $newCourse->setStartDate($newCourseStartDate);
        $newCourse->setEndDate($newCourseEndDate);
        $newCourse->setPublishedAsTbd(0);
        $newCourse->setLocked(0);
        $newCourse->setArchived(0);
        $newCourse->setSchool($originalCourse->getSchool());
        $newCourse->setClerkshipType($originalCourse->getClerkshipType());
        $newCourse->setLearningMaterials($originalCourse->getLearningMaterials());
        $newCourse->setDirectors($originalCourse->getDirectors());
        $newCourse->setTerms($originalCourse->getTerms());
        $newCourse->setObjectives($originalCourse->getObjectives());
        $newCourse->setMeshDescriptors($originalCourse->getMeshDescriptors());

        return $newCourse;

        /********* TESTING *********/
        //Now, operate on the course sessions
        $sessions = $this->getSessions();

        foreach($sessions as $session) {
            $newSession = new Session();
            $newSession->setCourse($newCourse);
            $newSession->setTitle($session->getTitle());
            $newSession->setSessionType($session->getSessionType());
            $newSession->setLearningMaterials($session->getLearningMaterials());
            $newSession->setAttireRequired($session->isAttireRequired());

            //TODO: find out why this says 'must implement interface...'
            //$newSessionDescription = new SessionDescriptionInterface();
            //$newSession->setSessionDescription($session->getSessionDescription());

            //$em->persist($newSession);
            //$em->flush($newSession);

            $sessionOfferings = $session->getOfferings();

            foreach($sessionOfferings as $sessionOffering) {
                $newSessionOffering = new Offering();
                $newSessionOffering->setStartDate();

                //$em->persist($newSessionOffering);
                //$em->flush($newSessionOffering);
            }

        }

    }

    /**
     * @param $originalYear
     * @param $newYear
     * @param null $newStartDate
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
                "You cannot rollover a course to a year that is already in the past."
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
                "Another course with the title and year already exists."
            );
        }
    }

}