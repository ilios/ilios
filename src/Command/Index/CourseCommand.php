<?php

declare(strict_types=1);

namespace App\Command\Index;

use App\Service\Index\Curriculum;
use DateTime;
use Symfony\Component\Console\Attribute\Argument;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Output\OutputInterface;

#[AsCommand(
    name: 'ilios:index:course',
    description: 'Extract and index a course by id'
)]
class CourseCommand extends Command
{
    public function __construct(
        protected Curriculum $index,
    ) {
        parent::__construct();
    }

    public function __invoke(
        OutputInterface $output,
        #[Argument(description: 'The ID of the course to index.', name: 'courseId')] int $id,
    ): int {
        if (!$this->index->isEnabled()) {
            $output->writeln("<comment>Indexing is not currently configured.</comment>");
            return Command::FAILURE;
        }
        $this->index->index([$id], new DateTime());

        return Command::SUCCESS;
    }
}
