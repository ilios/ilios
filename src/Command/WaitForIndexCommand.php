<?php

declare(strict_types=1);

namespace App\Command;

use App\Service\Index\Manager;
use Exception;
use OpenSearch\Client;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\HttpClient\Exception\TransportException;

use function sleep;

/**
 * Wait for the search index to become available
 * Useful for running before another command like consume messages
 */
#[AsCommand(
    name: 'ilios:wait-for-index',
    description: 'Wait for a connection to index.',
)]
class WaitForIndexCommand extends Command
{
    public function __construct(
        protected ?Client $client,
        protected Manager $indexManager,
    ) {
        parent::__construct();
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        if (!$this->client) {
            throw new Exception("Search is not configured.");
        }
        //start an infinite loop for checking the connection every second
        while (true) {
            sleep(1);
            try {
                // opensearch will throw an exception when this doesn't work so if that doesn't happen we're golden
                $this->client->nodes()->info();

                if ($this->indexManager->hasBeenCreated()) {
                    return Command::SUCCESS;
                }
            } catch (Exception $e) {
                if (!$e instanceof TransportException && !$e->getPrevious() instanceof TransportException) {
                    throw $e;
                }
                // try again;
            }
        }
    }
}
