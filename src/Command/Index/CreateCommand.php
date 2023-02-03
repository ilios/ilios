<?php

declare(strict_types=1);

namespace App\Command\Index;

use App\Service\Index\Manager;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Create Search Index
 */
class CreateCommand extends Command
{
    public const COMMAND_NAME = 'ilios:index:create';

    public function __construct(
        protected Manager $indexManager
    ) {
        parent::__construct();
    }

    protected function configure()
    {
        $this
            ->setName(self::COMMAND_NAME)
            ->setDescription('Create and empty search index');
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

        return 0;
    }
}
