<?php

namespace Ilios\CliBundle\Command;

use Ilios\CoreBundle\Service\CourseRollover;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Input\InputArgument;

/**
 * RolloverCourse Rolls over a course using original course_id and specified year.
 *
 * Class RolloverCourseCommand
 */
class RolloverCourseCommand extends Command
{
    /**
     * @var CourseRollover
     */
    protected $service;

    /**
     * RolloverCourseCommand constructor.
     * @param CourseRollover $service
     */
    public function __construct(CourseRollover $service)
    {
        $this->service = $service;
        parent::__construct();
    }

    /**
     * @inheritdoc
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
            ->addOption(
                'skip-course-objectives',
                null,
                InputOption::VALUE_NONE,
                'Do not copy/recreate course objectives'
            )
            ->addOption(
                'skip-course-terms',
                null,
                InputOption::VALUE_NONE,
                'Do not copy course terms'
            )
            ->addOption(
                'skip-course-mesh',
                null,
                InputOption::VALUE_NONE,
                'Do not copy course mesh terms'
            )
            ->addOption(
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
            ->addOption(
                'skip-session-objectives',
                null,
                InputOption::VALUE_NONE,
                'Do not copy/recreate session objectives'
            )
            ->addOption(
                'skip-session-terms',
                null,
                InputOption::VALUE_NONE,
                'Do not copy session terms'
            )
            ->addOption(
                'skip-session-mesh',
                null,
                InputOption::VALUE_NONE,
                'Do not copy session mesh terms'
            )
            ->addOption(
                'skip-offerings',
                null,
                InputOption::VALUE_NONE,
                'Do not copy/recreate the offerings, (default if --skip-sessions is set)'
            )
            ->addOption(
                'skip-instructors',
                null,
                InputOption::VALUE_NONE,
                'Do not copy instructor associations (default if --skip-offerings or --skip-sessions is set)'
            )
            ->addOption(
                'skip-instructor-groups',
                null,
                InputOption::VALUE_NONE,
                'Do not copy instructor group associations, (default if --skip-offerings or --skip-instructors are set)'
            )
            ->addOption(
                'new-course-title',
                null,
                InputOption::VALUE_REQUIRED,
                'Optionally enter a new title for the course. (useful for ILM courses being rolled-over to same year)'
            );
    }

    /**
     * @inheritdoc
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        //get/set the courseId and newAcademicYear arguments
        $courseId = $input->getArgument('courseId');
        $newAcademicYear = $input->getArgument('newAcademicYear');

        //roll it over to build the newCourse object
        $newCourse = $this->service->rolloverCourse($courseId, $newAcademicYear, $input->getOptions());

        //output message with the new courseId on success
        $output->writeln("This course has been rolled over. The new course id is {$newCourse->getId()}.");
    }
}
