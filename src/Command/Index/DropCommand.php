<?php

declare(strict_types=1);

namespace App\Command\Index;

use App\Service\Index\Manager;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Drop Search Index
 */
class DropCommand extends Command
{
    public const COMMAND_NAME = 'ilios:index:drop';

    public function __construct(
        protected Manager $indexManager
    ) {
        parent::__construct();
    }

    protected function configure()
    {
        $this
            ->setName(self::COMMAND_NAME)
            ->setDescription('Drop the search index removing all documents and settings')
            ->addOption(
                'force',
                null,
                InputOption::VALUE_NONE,
                'Set this parameter to execute this action'
            );
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        if (! $input->getOption('force')) {
            $output->writeln(
                '<error>ATTENTION:</error> This operation will remove all indexed data ' .
                    'and require a period of re-indexing in order to restart search functionality.'
            );
            $output->writeln('');
            $output->writeln('Please run the operation with --force to execute');
            $output->writeln('<error>All data will be lost!</error>');
            return 2;
        }
        $output->writeln("<info>Dropping the index.</info>");
        $this->indexManager->drop();
        $output->writeln("<info>Ok.</info>");

        return 0;
    }
}
