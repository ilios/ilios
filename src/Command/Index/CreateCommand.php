<?php

declare(strict_types=1);

namespace App\Command\Index;

use App\Service\Index\Manager;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Create Search Index
 */
#[AsCommand(
    name: 'ilios:index:create',
    description: 'Create and empty search index'
)]
class CreateCommand extends Command
{
    public function __construct(
        protected Manager $indexManager
    ) {
        parent::__construct();
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $output->writeln("<info>Creating the search index.</info>");
        if (!$this->indexManager->isEnabled()) {
            $output->writeln("<comment>Indexing is not currently configured.</comment>");
        } else {
            $this->indexManager->create();
            $output->writeln("<info>Done.</info>");
        }

        return Command::SUCCESS;
    }
}
