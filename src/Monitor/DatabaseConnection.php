<?php

declare(strict_types=1);

namespace App\Monitor;

use Doctrine\DBAL\Connection;
use Doctrine\DBAL\Exception;
use Laminas\Diagnostics\Check\CheckInterface;
use Laminas\Diagnostics\Result\Failure;
use Laminas\Diagnostics\Result\ResultInterface;
use Laminas\Diagnostics\Result\Success;

class DatabaseConnection implements CheckInterface
{
    public function __construct(protected Connection $connection)
    {
    }

    public function check(): ResultInterface
    {
        // inspired by https://github.com/liip/LiipMonitorBundle/blob/2.x/Check/DoctrineDbal.php
        try {
            $query = $this->connection->getDatabasePlatform()->getDummySelectSQL();
            $this->connection->fetchOne($query);
            return new Success('Database connection is up.');
        } catch (Exception $e) {
            return new Failure('Database connection is down.');
        }
    }

    public function getLabel(): string
    {
        return 'Database connection';
    }
}
