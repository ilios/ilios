<?php

declare(strict_types=1);

namespace App\Tests\DataLoader;

use App\Entity\DTO\AlertDTO;

class AlertData extends AbstractDataLoader
{
    protected function getData()
    {
        $arr = [];
        $arr[] = [
            'id' => 1,
            'tableRowId' => 1,
            'tableName' => 'course',
            'dispatched' => true,
            'changeTypes' => ['1'],
            'instigators' => ['1', '2'],
            'recipients' => ['1', '2']
        ];
        $arr[] = [
            'id' => 2,
            'tableRowId' => 2,
            'tableName' => 'course',
            'additionalText' => 'second text',
            'dispatched' => true,
            'changeTypes' => ['1'],
            'instigators' => ['2'],
            'recipients' => []
        ];
        $arr[] = [
            'id' => 3,
            'tableRowId' => 1,
            'tableName' => 'session',
            'dispatched' => false,
            'changeTypes' => [],
            'instigators' => [],
            'recipients' => ['2']
        ];

        return $arr;
    }

    public function create()
    {
        return [
            'id' => 4,
            'tableRowId' => $this->faker->randomDigit(),
            'tableName' => 'course',
            'dispatched' => true,
            'changeTypes' => ['1'],
            'instigators' => ['1'],
            'recipients' => ['1']
        ];
    }

    public function createInvalid()
    {
        return [
            'id' => 'string',
            'changeTypes' => [232452],
            'instigators' => [3234],
            'recipients' => [32434]
        ];
    }

    public function getDtoClass(): string
    {
        return AlertDTO::class;
    }
}
