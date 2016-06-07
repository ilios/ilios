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
                'Do not copy/recreate the offerings, (default if --skip-sessions is set)'
            )
            ->AddOption(
                'skip-instructors',
                null,
                InputOption::VALUE_NONE,
                'Do not copy instructor associations (default if --skip-offerings or --skip-sessions is set)'
            )
            ->AddOption(
                'skip-instructor-groups',
                null,
                InputOption::VALUE_NONE,
                'Do not copy instructor group associations, (default if --skip-offerings or --skip-instructors are set)'
            );
    }


    /**
     * @param InputInterface $input
     * @param OutputInterface $output
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {

        //access the CourseRollover class as a service
        $service = $this->getContainer()->get('ilioscore.courserollover');

        //roll it over to build the newCourse object
        $newCourse = $service->rolloverCourse($input->getArguments(), $input->getOptions());

        //output message with the new courseId on success
        $output->writeln("This course has been rolled over.  The new course url is -> https://curriculum.ucsf.edu/courses/" . $newCourse);

    }

}
