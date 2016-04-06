<?php

namespace Ilios\CliBundle\Command;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;

use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Input\InputArgument;

//get the course entities
use Ilios\CoreBundle\Entity\Course;
use Ilios\CoreBundle\Entity\CourseLearningMaterial;

//sessions
use Ilios\CoreBundle\Entity\Session;
use Ilios\CoreBundle\Entity\SessionLearningMaterial;

//offerings
use Ilios\CoreBundle\Entity\Offering;

//and the rest
use Ilios\CoreBundle\Entity\Objective;
use Ilios\CoreBundle\Entity\Term;

/**
 * RolloverCourse Rolls over a course using original course_id and specified year.
 *
 * Class RolloverCourseCommand
 * @package Ilios\CoreBundle\Command
 */
class RolloverCourseCommand extends ContainerAwareCommand
{
    /**
     * {@inheritdoc}
     */
    protected function configure()
    {
        $this
            ->setName('ilios:maintenance:rollover-course')
            ->setDescription('Roll over a course to a new year using its course_id')
            //required arguments
            ->addArgument(
                'courseId',
                InputArgument::REQUIRED,
                'The course_id value of the course to rollover'
            )
            ->addArgument(
                'newAcademicYear',
                InputArgument::REQUIRED,
                'The academic start year of the new course formatted as \'YYYY\''
            )
            //optional flags
            ->addOption(
                'new-start-date',
                null,
                InputOption::VALUE_REQUIRED,
                'The start date of the new course formatted as \'YYYY-MM-DD\''
            )
            ->addOption(
                'skip-course-learning-materials',
                null,
                InputOption::VALUE_NONE,
                'Do not associate course learning materials'
            )
            ->AddOption(
                'skip-course-objectives',
                null,
                InputOption::VALUE_NONE,
                'Do not copy/recreate course objectives'
            )
            ->addOption(
                'skip-course-topics',
                null,
                InputOption::VALUE_NONE,
                'Do not copy course topics'
            )
            ->addOption(
                'skip-course-mesh',
                null,
                InputOption::VALUE_NONE,
                'Do not copy course mesh terms'
            )
            ->AddOption(
                'skip-sessions',
                null,
                InputOption::VALUE_NONE,
                'Do not copy/recreate the sessions'
            )
            ->addOption(
                'skip-session-learning-materials',
                null,
                InputOption::VALUE_NONE,
                'Do not associate session learning materials'
            )
            ->AddOption(
                'skip-session-objectives',
                null,
                InputOption::VALUE_NONE,
                'Do not copy/recreate session objectives'
            )
            ->addOption(
                'skip-session-topics',
                null,
                InputOption::VALUE_NONE,
                'Do not copy session topics'
            )
            ->addOption(
                'skip-session-mesh',
                null,
                InputOption::VALUE_NONE,
                'Do not copy session mesh terms'
            )
            ->AddOption(
                'skip-offerings',
                null,
                InputOption::VALUE_NONE,
                'Do not copy/recreate the offerings'
            )
            ->AddOption(
                'skip-instructors',
                null,
                InputOption::VALUE_NONE,
                'Do not copy instructor associations (default if --skip-offerings is set)'
            )
            ->AddOption(
                'skip-instructor-groups',
                null,
                InputOption::VALUE_NONE,
                'Do not copy instructor group associations, (default if --skip-offerings or --skip-instructors are set)'
            );
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {

        $em = $this->getContainer()->get('doctrine.orm.entity_manager');
        $courses = $em->getRepository('IliosCoreBundle:Course');

        //set the values from the input arguments
        $originalCourseId = $input->getArgument('courseId');
        $newCourseAcademicYear = $input->getArgument('newAcademicYear');
        $newStartDate = $input->getOption('new-start-date');

        //get the course object by its course id
        $originalCourse = $courses->find($originalCourseId);

        //get the necessary attributes
        $originalCourseTitle = $originalCourse->getTitle();
        $originalCourseAcademicYear = $originalCourse->getYear();
        $originalCourseStartDate = $originalCourse->getStartDate()->format('Y-m-d');

        //get the week number of the original start date and the new one
        $originalStartWeekOrdinal = date('W',strtotime($originalCourseStartDate));
        $newStartWeekOrdinal = (!empty($newStartDate)) ? date('W',strtotime($newStartDate)) : null;

        $academicYearDifference = ($newCourseAcademicYear - $originalCourseAcademicYear);
        $offsetInWeeks = $this->calculateRolloverOffsetInWeeks($academicYearDifference, $originalStartWeekOrdinal,$newStartWeekOrdinal);


        //check to see if the title and the new course year already exist
        $dql = 'SELECT c.id FROM IliosCoreBundle:Course c WHERE c.year = ?1 AND c.title = ?2';
        $query = $em->createQuery($dql);
        $query->setParameter(1, $newCourseAcademicYear);
        $query->setParameter(2, $originalCourseTitle);
        $results = $query->getResult();


        /***** UNCOMMENT THIS FOR PRODUCTION *****/
        //if the title and requested year already exist, warn and exit
        /*if(!empty($results)) {

            $totalResults = count($results);
            $existingCourseIdArray = array();
            foreach ($results as $result) {
                $existingCourseIdArray[] = $result['id'];
            }
            $existingCourseIdString = implode(',',$existingCourseIdArray);
            $error_string = ($totalResults > 1) ? ' courses already exist' : ' course already exists';
            exit('Please check your requirements: ' . $totalResults  . $error_string . ' with that year and title (' . $existingCourseIdString . ').' . "\n");
        }*/

        //create the Course
        //if there are not any duplicates, create a new course with the relevant info
        $newCourse = new Course();
        $newCourse->setTitle($originalCourseTitle);
        $newCourse->setYear($newCourseAcademicYear);
        $newCourse->setLevel($originalCourse->getLevel());
        $newCourseStartDate = date_create(strtotime('+' . $offsetInWeeks . ' weeks', strtotime($originalCourseStartDate)));
        $newCourse->setStartDate($newCourseStartDate);
        $newCourse->setEndDate($originalCourse->getEndDate());

        $em->persist($newCourse);
        $em->flush($newCourse);



        //while creating the course, loop through and get the original course session info
        $dql = 'SELECT s FROM IliosCoreBundle:Session s JOIN IliosCoreBundle:Course c WITH s.course = c.id WHERE s.course = ?1';
        $query = $em->createQuery($dql);
        $query->setParameter(1, $originalCourseId);
        $sessions = $query->getResult();

        //create new sessions for all
        foreach ($sessions as $session) {
            $newSession = new Session();
            $newSession->setTitle($session->getTitle());
            $newSession->setCourse($newCourse);
            $newSession->setSessionType($session->getSessionType());
            //\Doctrine\Common\Util\Debug::dump($newSession);
            $em->persist($newSession);
            $em->flush($newSession);
        }




        //$newSession = new Session();
        //$newSession->setTitle('test');
        //$newCourse->addSession($newSession);

        //output for debug
        //\Doctrine\Common\Util\Debug::dump($newStartDate);
        //\Doctrine\Common\Util\Debug::dump($originalCourse);
        //\Doctrine\Common\Util\Debug::dump($originalCourseStartDate);
        //\Doctrine\Common\Util\Debug::dump($newCourse);
        //\Doctrine\Common\Util\Debug::dump($results);

    }

    protected function calculateRolloverOffsetInWeeks($academicYearDifference, $originalStartWeekOrdinal, $newStartWeekOrdinal = null){

        //if no start week is given, then multiply the academicYearDifference by 52 weeks for each year
        if(empty($newStartWeekOrdinal)) {
            $weeksToAdd = ($academicYearDifference * 52);
        } else {
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
                    $weeksToAdd = $weeksBetweenTwoDates + $weekYearMultiplier;
            }
        }

        return $weeksToAdd;
    }

}


