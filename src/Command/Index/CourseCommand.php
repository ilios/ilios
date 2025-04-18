<?php

declare(strict_types=1);

namespace App\Command\Index;

use App\Repository\CourseRepository;
use App\Repository\LearningMaterialRepository;
use App\Service\Index\Curriculum;
use App\Service\Index\LearningMaterials;
use Composer\Console\Input\InputArgument;
use DateTime;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

#[AsCommand(
    name: 'ilios:index:course',
    description: 'Extract and index a course by id'
)]
class CourseCommand extends Command
{
    public function __construct(
        protected CourseRepository $courseRepository,
        protected Curriculum $index,
    ) {
        parent::__construct();
    }

    protected function configure(): void
    {
        $this
            ->addArgument(
                'courseId',
                InputArgument::REQUIRED,
                'The ID of the course to index.'
            );
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        if (!$this->index->isEnabled()) {
            $output->writeln("<comment>Indexing is not currently configured.</comment>");
            return Command::FAILURE;
        }
        $id = $input->getArgument('courseId');
        $indexes = $this->courseRepository->getCourseIndexesFor([$id]);
        $this->index->index($indexes, new DateTime());

        return Command::SUCCESS;
    }
}
