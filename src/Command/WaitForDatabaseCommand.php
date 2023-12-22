<?php

declare(strict_types=1);

namespace App\Command;

use Doctrine\DBAL\Exception\ConnectionException;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

use function sleep;

/**
 * Wait for the database to become available
 * Useful for running before another command like migrate
 */
#[AsCommand(
    name: 'ilios:wait-for-database',
    description: 'Wait for a database connection.'
)]
class WaitForDatabaseCommand extends Command
{
    public function __construct(
        protected EntityManagerInterface $entityManager,
    ) {
        parent::__construct();
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        //start an infinite loop for checking the connection every second
        while (true) {
            sleep(1);
            try {
                // doctrine will throw an exception when this doesn't work so if that doesn't happen we're golden
                $this->entityManager->getConnection()->executeQuery('Select 1');

                return Command::SUCCESS;
            } catch (ConnectionException) {
                // try again;
            }
        }
    }
}
