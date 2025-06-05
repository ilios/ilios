<?php

declare(strict_types=1);

namespace App\Tests\DataLoader;

use App\Entity\DTO\IlmSessionDTO;
use DateTime;

final class IlmSessionData extends AbstractDataLoader
{
    protected function getData(): array
    {
        $arr = [];
        $dt = new DateTime();
        $dt->setTime(0, 0, 0);
        $dt->setDate(2016, 1, 1);
        $arr[] = [
            'id' => 1,
            'hours' => 10.0,
            'dueDate' => $dt->format('c'),
            'learnerGroups' => ['1', '3'],
            'instructorGroups' => ['1'],
            'instructors' => [],
            'learners' => [],
            'session' => '5',
        ];
        $dt->modify('+1 month');
        $arr[] = [
            'id' => 2,
            'hours' => 21.2,
            'dueDate' => $dt->format('c'),
            'learnerGroups' => [],
            'instructorGroups' => ['3'],
            'instructors' => [],
            'learners' => [],
            'session' => '6',
        ];

        $dt->modify('+1 month');
        $arr[] = [
            'id' => 3,
            'hours' => 1.50,
            'dueDate' => $dt->format('c'),
            'learnerGroups' => [],
            'instructorGroups' => [],
            'instructors' => ['2'],
            'learners' => [],
            'session' => '7',
        ];

        $dt->modify('+1 month');
        $arr[] = [
            'id' => 4,
            'hours' => 10.75,
            'dueDate' => $dt->format('c'),
            'learnerGroups' => [],
            'instructorGroups' => [],
            'instructors' => [],
            'learners' => ['2'],
            'session' => '8',
        ];

        return $arr;
    }

    public function create(): array
    {
        $dt = new DateTime();
        $dt->setTime(0, 0, 0);
        return [
            'id' => 5,
            'hours' => 12.5,
            'dueDate' => $dt->format('c'),
            'learnerGroups' => ['1', '2'],
            'instructorGroups' => ['1', '2'],
            'instructors' => ['1', '2'],
            'learners' => ['1', '2'],
            'session' => '1',
        ];
    }

    public function createInvalid(): array
    {
        return [];
    }

    public function getDtoClass(): string
    {
        return IlmSessionDTO::class;
    }
}
