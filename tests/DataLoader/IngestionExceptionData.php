<?php

declare(strict_types=1);

namespace App\Tests\DataLoader;

use App\Entity\DTO\IngestionExceptionDTO;
use Exception;

final class IngestionExceptionData extends AbstractDataLoader
{
    protected function getData(): array
    {
        $arr = [];
        $arr[] = [
            'id' => 1,
            'uid' => 'first exception',
            'user' => '1',
        ];
        $arr[] = [
            'id' => 2,
            'uid' => 'second exception',
            'user' => '2',
        ];

        return $arr;
    }

    public function create(): array
    {
        throw new Exception('Not implemented.');
    }

    public function createInvalid(): array
    {
        throw new Exception('Not implemented.');
    }

    public function getDtoClass(): string
    {
        return IngestionExceptionDTO::class;
    }

    public function createJsonApi(array $arr): object
    {
        throw new Exception('Not implemented');
    }
}
