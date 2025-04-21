<?php

declare(strict_types=1);

namespace App\Command\Index;

use App\Repository\CourseRepository;
use App\Repository\LearningMaterialRepository;
use App\Repository\SessionRepository;
use App\Service\Index\Curriculum;
use App\Service\Index\LearningMaterials;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

#[AsCommand(
    name: 'ilios:index:detect-missing',
    description: 'Find items that are missing in the index'
)]
class DetectMissingCommand extends Command
{
    public function __construct(
        protected LearningMaterialRepository $learningMaterialRepository,
        protected CourseRepository $courseRepository,
        protected LearningMaterials $materialIndex,
        protected Curriculum $curriculumIndex,
        protected SessionRepository $sessionRepository,
    ) {
        parent::__construct();
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        if (!$this->materialIndex->isEnabled()) {
            $output->writeln("<comment>Indexing is not currently configured.</comment>");
            return Command::FAILURE;
        }
        $materialsResult = $this->checkMaterials($output);
        $coursesResult = $this->checkCourses($output);
        $sessionsResult = $this->checkSessions($output);
        if (
            !$materialsResult ||
            !$coursesResult ||
            !$sessionsResult
        ) {
            return Command::FAILURE;
        }

        return Command::SUCCESS;
    }

    protected function checkMaterials(OutputInterface $output): bool
    {
        $materialsInIndex = $this->materialIndex->getAllIds();
        $allIds = $this->learningMaterialRepository->getFileLearningMaterialIds();
        $missing = array_diff($allIds, $materialsInIndex);
        $count = count($missing);
        if ($count) {
            $list = implode(', ', $missing);
            $output->writeln("<error>{$count} materials are missing from the index.</error>");
            $output->writeln("<comment>Materials: {$list}</comment>");
            return false;
        }

        $output->writeln("<info>All materials are indexed.</info>");
        return true;
    }

    protected function checkCourses(OutputInterface $output): bool
    {
        $coursesInIndex = $this->curriculumIndex->getAllCourseIds();
        $allIds = $this->courseRepository->getIdsForCoursesWithSessions();
        $missing = array_diff($allIds, $coursesInIndex);
        $count = count($missing);
        if ($count) {
            $list = implode(', ', $missing);
            $output->writeln("<error>{$count} courses are missing from the index.</error>");
            $output->writeln("<comment>Courses: {$list}</comment>");
            return false;
        }

        $output->writeln("<info>All courses are indexed.</info>");
        return true;
    }

    protected function checkSessions(OutputInterface $output): bool
    {
        $sessionsInIndex = $this->curriculumIndex->getAllSessionIds();
        $allIds = $this->sessionRepository->getIds();
        $missing = array_diff($allIds, $sessionsInIndex);
        $count = count($missing);
        if ($count) {
            $list = implode(', ', $missing);
            $output->writeln("<error>{$count} sessions are missing from the index.</error>");
            $output->writeln("<comment>Sessions: {$list}</comment>");
            return false;
        }

        $output->writeln("<info>All sessions are indexed.</info>");
        return true;
    }
}
