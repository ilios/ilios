<?php

declare(strict_types=1);

namespace App\Tests\DataLoader;

class IngestionExceptionData extends AbstractDataLoader
{
    protected function getData()
    {
        $arr = [];
        $arr[] = [
            'id' => 1,
            'uid' => $this->faker->text(12),
            'user' => '1'
        ];
        $arr[] = [
            'id' => 2,
            'uid' => 'second exception',
            'user' => '2'
        ];

        return $arr;
    }

    public function create()
    {
        throw new \Exception('Not implemented.');
    }

    public function createInvalid()
    {
        throw new \Exception('Not implemented.');
    }
}
