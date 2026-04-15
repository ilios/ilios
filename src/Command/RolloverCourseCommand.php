<?php

declare(strict_types=1);

namespace App\Command;

use App\Service\CourseRollover;
use Symfony\Component\Console\Attribute\Argument;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Attribute\Option;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * RolloverCourse Rolls over a course using original course_id and specified year.
 *
 * Class RolloverCourseCommand
 */
#[AsCommand(
    name: 'ilios:rollover-course',
    description: 'Roll over a course to a new year using its course_id',
    aliases: ['ilios:maintenance:rollover-course'],
)]
class RolloverCourseCommand extends Command
{
    public function __construct(protected CourseRollover $service)
    {
        parent::__construct();
    }

    public function __invoke(
        OutputInterface $output,
        #[Argument(
            description: 'The course ID of the course to rollover',
            name: 'courseId'
        )] int $courseId,
        #[Argument(
            description: "The academic start year of the new course formatted as 'YYYY'",
            name: 'newAcademicYear'
        )] int $newAcademicYear,
        #[Option(
            description: "The start date of the new course formatted as 'YYYY-MM-DD'",
            name: 'new-start-date'
        )] ?string $newStartDate = null,
        #[Option(
            description: 'Do not associate course learning materials',
            name: 'skip-course-learning-materials'
        )] bool $skipCourseLearningMaterials = false,
        #[Option(
            description: 'Do not copy/recreate course objectives',
            name: 'skip-course-objectives'
        )] bool $skipCourseObjectives = false,
        #[Option(
            description: 'Do not copy course terms',
            name: 'skip-course-terms'
        )] bool $skipCourseTerms = false,
        #[Option(
            description: 'Do not copy course mesh terms',
            name: 'skip-course-mesh'
        )] bool $skipCourseMesh = false,
        #[Option(
            description: 'Do not copy/recreate the session',
            name: 'skip-sessions'
        )] bool $skipSessions = false,
        #[Option(
            description: 'Do not associate session learning materials',
            name: 'skip-session-learning-materials'
        )] bool $skipSessionLearningMaterials = false,
        #[Option(
            description: 'Do not copy/recreate session objectives',
            name: 'skip-session-objectives'
        )] bool $skipSessionObjectives = false,
        #[Option(
            description: 'Do not copy session terms',
            name: 'skip-session-terms'
        )] bool $skipSessionTerms = false,
        #[Option(
            description: 'Do not copy session mesh terms',
            name: 'skip-session-mesh'
        )] bool $skipSessionMesh = false,
        #[Option(
            description: 'Do not copy/recreate the offerings, (default if --skip-sessions is set)',
            name: 'skip-offerings'
        )] bool $skipOfferings = false,
        #[Option(
            description:
            'Do not copy instructor associations (default if --skip-offerings or --skip-sessions is set)',
            name: 'skip-instructors'
        )] bool $skipInstructors = false,
        #[Option(
            description:
            'Do not copy instructor group associations, (default if --skip-offerings or --skip-instructors are set)',
            name: 'skip-instructor-groups'
        )] bool $skipInstructorGroups = false,
        #[Option(
            description:
            'Optionally enter a new title for the course. (useful for ILM courses being rolled-over to same year)'
        )] ?string $newCourseTitle = null,
    ): int {
        //roll it over to build the newCourse object
        $newCourse = $this->service->rolloverCourse($courseId, $newAcademicYear, [
            'new-start-date' => $newStartDate,
            'skip-course-learning-materials' => $skipCourseLearningMaterials,
            'skip-course-objectives' => $skipCourseObjectives,
            'skip-course-terms' => $skipCourseTerms,
            'skip-course-mesh' => $skipCourseMesh,
            'skip-sessions' => $skipSessions,
            'skip-session-learning-materials' => $skipSessionLearningMaterials,
            'skip-session-objectives' => $skipSessionObjectives,
            'skip-session-terms' => $skipSessionTerms,
            'skip-session-mesh' => $skipSessionMesh,
            'skip-offerings' => $skipOfferings,
            'skip-instructors' => $skipInstructors,
            'skip-instructor-groups' => $skipInstructorGroups,
            'new-course-title' => $newCourseTitle,
        ]);

        //output message with the new courseId on success
        $output->writeln("This course has been rolled over. The new course id is {$newCourse->getId()}.");

        return Command::SUCCESS;
    }
}
