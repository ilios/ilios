<?php

namespace App\Command;

use App\Service\IliosFileSystem;
use Doctrine\Bundle\MigrationsBundle\Command\MigrationsMigrateDoctrineCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Uses a lock to ensure that only one migration can run
 * This allows migrations to be done when containers boot without
 * worrying about too many running at the same time.
 *
 * Class LockingMigrationCommand
 */
class LockingMigrationCommand extends MigrationsMigrateDoctrineCommand
{
    const LOCK_NAME = 'database-migration.lock';

    /**
     * @var IliosFileSystem
     */
    private $fileSystem;

    public function __construct(IliosFileSystem $fileSystem)
    {
        parent::__construct();
        $this->fileSystem = $fileSystem;
    }

    protected function configure()
    {
        parent::configure();

        $this->setName('ilios:migrate-database');
    }

    public function execute(InputInterface $input, OutputInterface $output) : ?int
    {
        try {
            $this->fileSystem->waitForLock(self::LOCK_NAME);

            return parent::execute($input, $output);
        } finally {
            $this->fileSystem->releaseLock(self::LOCK_NAME);
        }
    }
}
