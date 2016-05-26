<?php

namespace Ilios\CliBundle\Command;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;

use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Input\InputArgument;


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

        $service = $this->getContainer()->get('ilioscore.courserollover');
        $service->foo();
        exit;


        //$em = $this->getContainer()->get('doctrine.orm.entity_manager');
        //$courses = $em->getRepository('IliosCoreBundle:Course');

        //set the values from the input arguments
        $originalCourseId = $input->getArgument('courseId');
        $newCourseAcademicYear = $input->getArgument('newAcademicYear');
        $newStartDate = $input->getOption('new-start-date');

        //get the original course object by its course id
        $originalCourse = $this->courseManager->findCourseBy(['id' => $originalCourseId]);

        $originalCourse->rolloverCourse($this,$newCourseAcademicYear,$newStartDate);


        \Doctrine\Common\Util\Debug::dump($originalCourse);

        //check to see if the title and the new course year already exist
        //$qb = $em->createQueryBuilder();
        /*$qb->select('c.id')
            ->from('IliosCoreBundle:Course', 'c')
            ->where($qb->expr()->andX(
                $qb->expr()->eq('c.year', '?1'),
                $qb->expr()->eq('c.title', '?2')
            ))
            ->setParameter(1, $newCourseAcademicYear)
            ->setParameter(2, $originalCourse->getTitle());
        $query = $qb->getQuery();
        $results = $query->getResult();*/

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

        //create the rollover
        //$originalCourse->rolloverCourse($em, $newCourseAcademicYear, $newStartDate);

        //output for debug
        //\Doctrine\Common\Util\Debug::dump($newStartDate);

    }

}
