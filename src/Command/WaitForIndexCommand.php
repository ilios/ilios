<?php

declare(strict_types=1);

namespace App\Command;

use App\Service\Index\Manager;
use Elasticsearch\Client;
use Elasticsearch\Common\Exceptions\NoNodesAvailableException;
use Exception;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

use function sleep;

/**
 * Wait for the search index to become available
 * Useful for running before another command like consume messages
 */
class WaitForIndexCommand extends Command
{
    public const COMMAND_NAME = 'ilios:wait-for-index';

    public function __construct(
        protected ?Client $client,
        protected Manager $indexManager,
    ) {
        parent::__construct();
    }

    protected function configure()
    {
        $this
            ->setName(self::COMMAND_NAME)
            ->setDescription('Wait for a connection to index.');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        if (!$this->client) {
            throw new Exception("Elasticsearch is not configured.");
        }
        //start an infinite loop for checking the connection every second
        while (true) {
            sleep(1);
            try {
                // elasticsearch will throw an exception when this doesn't work so if that doesn't happen we're golden
                $this->client->nodes()->info();

                if ($this->indexManager->hasBeenCreated()) {
                    return Command::SUCCESS;
                }
            } catch (NoNodesAvailableException) {
                // try again;
            }
        }
    }
}
