<?php

namespace Ilios\CoreBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;


class CourseRolloverController extends Controller
{

    public function indexAction($name)
    {
        //return $this->render('', array('name' => $name));
        $originalCourse = $this->courseManager->findCourseBy(['id' => 791]);


    }





    /**
     * @param int $newCourseAcademicYear
     * @param string $newCourseStartDate
     */
    protected function rolloverCourse($newCourseAcademicYear, $newCourseStartDate = null)
    {
        
        $courseManager = $this->container->get('ilioscore.course.manager');
        $sessionManager = $this->container->get('ilioscore.session.manager');
        $offeringManager = $this->container->get('ilioscore.offering.manager');
        
        
        //get the necessary attributes
        $originalCourseTitle = $this->getTitle();
        $originalCourseAcademicYear = $this->getYear();
        $originalCourseStartDate = $this->getStartDate();
        $originalCourseEndDate = $this->getEndDate();

        //get the week number of the original start date and the new one
        $originalStartWeekOrdinal = $originalCourseStartDate->format("W");
        $newStartWeekOrdinal = (!empty($newCourseStartDate)) ? date('W',strtotime($newCourseStartDate)) : null;

        $academicYearDifference = ($newCourseAcademicYear - $originalCourseAcademicYear);
        $offsetInWeeks = $this->calculateRolloverOffsetInWeeks($academicYearDifference, $originalStartWeekOrdinal, $newStartWeekOrdinal);

        //create the Course
        //if there are not any duplicates, create a new course with the relevant info
        $newCourse = new Course();
        $newCourse->setTitle($originalCourseTitle);
        $newCourse->setYear($newCourseAcademicYear);
        $newCourse->setLevel($this->getLevel());
        $newCourseStartDate = date_create($originalCourseStartDate->format('Y-m-d'));
        $newCourseStartDate->modify('+ ' . $offsetInWeeks . ' weeks');
        $newCourse->setStartDate($newCourseStartDate);
        $newCourseEndDate = date_create($originalCourseEndDate->format('Y-m-d'));
        $newCourseEndDate->modify('+ ' . $offsetInWeeks . ' weeks');
        $newCourse->setEndDate($newCourseEndDate);
        $newCourse->setPublishedAsTbd($this->isPublishedAsTbd());
        $newCourse->setLocked(0);
        $newCourse->setArchived(0);
        $newCourse->setSchool($this->getSchool());
        $newCourse->setClerkshipType($this->getClerkshipType());
        $newCourse->setLearningMaterials($this->getLearningMaterials());
        $newCourse->setDirectors($this->getDirectors());
        $newCourse->setTerms($this->getTerms());
        $newCourse->setObjectives($this->getObjectives());
        $newCourse->setMeshDescriptors($this->getMeshDescriptors());
        //$em->persist($newCourse);
        //$em->flush($newCourse);

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
     * @param int $academicYearDifference
     * @param int $originalStartWeekOrdinal
     * @param int $newStartWeekOrdinal
     * @return int $weeksToAdd
     */
    protected function calculateRolloverOffsetInWeeks($academicYearDifference, $originalStartWeekOrdinal, $newStartWeekOrdinal = null){

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
    
    
    
    
}
