<?php

declare(strict_types=1);

namespace App\Tests\DataLoader;

use App\Entity\DTO\SessionDescriptionDTO;

class SessionDescriptionData extends AbstractDataLoader
{
    protected function getData()
    {
        $arr = [];
        $arr[] = [
            'id' => 1,
            'session' => '1',
            'description' => $this->faker->text
        ];
        $arr[] = [
            'id' => 2,
            'session' => '2',
            'description' => 'second description'
        ];

        return $arr;
    }

    public function create()
    {
        return [
            'id' => 3,
            'session' => 3,
            'description' => $this->faker->text
        ];
    }

    public function createInvalid()
    {
        return [
            'session' => 11
        ];
    }

    public function getDtoClass(): string
    {
        return SessionDescriptionDTO::class;
    }
}
