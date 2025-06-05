<?php

declare(strict_types=1);

namespace App\Tests\DataLoader;

use DateTime;
use DateTimeZone;
use Exception;

/**
 * Class AuditLogData
 */
final class AuditLogData extends AbstractDataLoader
{
    protected function getData(): array
    {
        $arr[] = [
            'createdAt' => new DateTime('1 day ago', new DateTimeZone('UTC')),
            // bogus class name, we'll use this to peel entries out of the command output by this.
            'objectClass' => 'YesterdaysEvent',
            'action' => 'update',
            'valuesChanged' => 'lorem ipsum',
            'objectId' => 1,
        ];

        $arr[] = [
            'createdAt' => new DateTime('1 year ago', new DateTimeZone('UTC')),
            'objectClass' => 'LastYearsEvent',
            'action' => 'insert',
            'valuesChanged' => 'dev null',
            'objectId' => 2,
        ];

        $arr[] = [
            'createdAt' => new DateTime('midnight today', new DateTimeZone('UTC')),
            'objectClass' => 'TodaysEvent',
            'action' => 'delete',
            'valuesChanged' => 'something else',
            'objectId' => 4,
        ];

        return $arr;
    }

    /**
     * Not implemented.
     *
     * @throws Exception
     */
    public function create(): array
    {
        throw new Exception('Not implemented');
    }

    /**
     * Not implemented.
     *
     * @throws Exception
     */
    public function createInvalid(): array
    {
        throw new Exception('Not implemented');
    }

    public function getDtoClass(): string
    {
        throw new Exception('Not implemented');
    }

    public function createJsonApi(array $arr): object
    {
        throw new Exception('Not implemented');
    }
}
