<?php

declare(strict_types=1);

namespace App\Monitor;

use Doctrine\Migrations\DependencyFactory;
use Laminas\Diagnostics\Check\CheckInterface;
use Laminas\Diagnostics\Result\Failure;
use Laminas\Diagnostics\Result\ResultInterface;
use Laminas\Diagnostics\Result\Success;

class Migrations implements CheckInterface
{
    public function __construct(protected DependencyFactory $dependencyFactory) {}

    public function check(): ResultInterface
    {
        // Functionality pilfered and stripped down from the `doctrine:migrations:up-to-date` command.
        // Source: https://github.com/doctrine/migrations/blob/3.9.x/src/Tools/Console/Command/UpToDateCommand.php
        $statusCalculator = $this->dependencyFactory->getMigrationStatusCalculator();
        $executedUnavailableMigrations  = $statusCalculator->getExecutedUnavailableMigrations();
        $newMigrations = $statusCalculator->getNewMigrations();
        $newMigrationsCount = count($newMigrations);
        $executedUnavailableMigrationsCount = count($executedUnavailableMigrations);

        if ($newMigrationsCount === 0 && $executedUnavailableMigrationsCount === 0) {
            return new Success('Up-to-date! No migrations to execute.');
        }

        if ($newMigrationsCount > 0) {
            return new Failure('Out-of-date! New migrations available to execute.');
        }

        return new Failure('You have previously executed migrations in the database that are not registered migrations.');
    }

    public function getLabel(): string
    {
        return 'Database migrations';
    }
}
