<?php

declare(strict_types=1);

namespace App\Tests\DataLoader;

use DateInterval;
use DateTime;
use Exception;

class ServiceTokenData extends AbstractDataLoader
{
    public const int ENABLED_SERVICE_TOKEN_ID = 1;
    public const int EXPIRED_SERVICE_TOKEN_ID = 2;
    public const int DISABLED_SERVICE_TOKEN_ID = 3;

    protected function getData(): array
    {
        $arr = [];
        $arr[] = [
            'id' => self::ENABLED_SERVICE_TOKEN_ID,
            'description' => 'This is an enabled, un-expired service token.',
            'enabled' => true,
            'createdAt' => new DateTime(),
            'expiresAt' => (new DateTime())->add(new DateInterval('PT8H')),
        ];
        $arr[] = [
            'id' => self::EXPIRED_SERVICE_TOKEN_ID,
            'description' => 'This is an enabled, expired service token.',
            'enabled' => true,
            'createdAt' => new DateTime(),
            'expiresAt' => (new DateTime())->sub(new DateInterval('PT8H')),
        ];
        $arr[] = [
            'id' => self::DISABLED_SERVICE_TOKEN_ID,
            'description' => 'This is a disabled, un-expired service token.',
            'enabled' => false,
            'createdAt' => new DateTime(),
            'expiresAt' => (new DateTime())->add(new DateInterval('PT8H')),
        ];

        return $arr;
    }

    public function create(): array
    {
        throw new Exception('not implemented');
    }

    public function createInvalid(): array
    {
        return [
            'id' => 'bad',
        ];
    }

    public function getDtoClass(): string
    {
        throw new Exception('not implemented');
    }
}
