<?php

declare(strict_types=1);

namespace App\Tests\DataLoader;

use App\Entity\DTO\ReportDTO;

final class ReportData extends AbstractDataLoader
{
    protected function getData(): array
    {
        $arr = [];

        $arr[] = [
            'id' => 1,
            'title' => 'first report',
            'subject' => 'lorem',
            'user' => 2,
            'school' => null,
        ];

        $arr[] = [
            'id' => 2,
            'title' => 'second report',
            'subject' => 'ipsum',
            'prepositionalObject' => 'some thing',
            'prepositionalObjectTableRowId' => '14',
            'user' => 2,
            'school' => null,
        ];

        $arr[] = [
            'id' => 3,
            'title' => 'third report',
            'subject' => 'subject3',
            'prepositionalObject' => 'object3',
            'prepositionalObjectTableRowId' => '23',
            'user' => 2,
            'school' => 1,
        ];

        return $arr;
    }

    public function create(): array
    {
        return [
            'id' => 4,
            'title' => 'fourth program',
            'subject' => 'subject four',
            'prepositionalObject' => 'object',
            'prepositionalObjectTableRowId' => '22',
            'user' => 2,
            'school' => null,
        ];
    }

    public function createInvalid(): array
    {
        return [];
    }

    public function getDtoClass(): string
    {
        return ReportDTO::class;
    }
}
