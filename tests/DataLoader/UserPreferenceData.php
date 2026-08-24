<?php

declare(strict_types=1);

namespace App\Tests\DataLoader;

use Exception;

final class UserPreferenceData extends AbstractDataLoader
{
    protected function getData(): array
    {
        $arr = [];
        $arr[] = [
            'user' => 1,
            'json' => '{"theme":"dark","locale":"en"}',
        ];
        return $arr;
    }

    public function create(): array
    {
        throw new Exception('not implemented');
    }

    public function createInvalid(): array
    {
        throw new Exception('not implemented');
    }

    public function getDtoClass(): string
    {
        throw new Exception('not implemented');
    }
}
