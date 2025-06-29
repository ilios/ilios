<?php

declare(strict_types=1);

namespace App\Command\Index;

use App\Message\CourseIndexRequest;
use App\Message\LearningMaterialIndexRequest;
use App\Message\MeshDescriptorIndexRequest;
use App\Message\UserIndexRequest;
use App\Service\Index\Manager;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Drop Search Index
 */
#[AsCommand(
    name: 'ilios:index:drop',
    description: 'Drop the search index removing all documents and settings'
)]
class DropCommand extends Command
{
    public function __construct(
        protected Manager $indexManager,
        protected EntityManagerInterface $entityManager,
    ) {
        parent::__construct();
    }

    protected function configure(): void
    {
        $this
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
            return Command::INVALID;
        }
        $this->clearIndexQueue($output);
        $output->writeln("<info>Dropping the index.</info>");
        $this->indexManager->drop();
        $output->writeln("<info>Ok.</info>");

        return Command::SUCCESS;
    }

    /**
     * Clear the indexing related messages from the queue
     */
    protected function clearIndexQueue(OutputInterface $output): void
    {
        $shortNames = array_map(
            fn(string $class) => new \ReflectionClass($class)->getShortName(),
            [
                CourseIndexRequest::class,
                LearningMaterialIndexRequest::class,
                UserIndexRequest::class,
                MeshDescriptorIndexRequest::class,
            ]
        );
        $regex = implode('|', $shortNames);
        $str = "DELETE FROM messenger_messages WHERE body REGEXP '{$regex}'";
        $sql = vsprintf($str, $shortNames);
        $conn = $this->entityManager->getConnection();
        $removed = $conn->executeStatement($sql);

        $output->writeln("<info>Cleared {$removed} existing index messages from queue.</info>");
    }
}
