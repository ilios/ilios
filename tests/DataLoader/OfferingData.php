<?php

declare(strict_types=1);

namespace App\Tests\DataLoader;

use App\Entity\DTO\OfferingDTO;

final class OfferingData extends AbstractDataLoader
{
    protected function getData(): array
    {
        $arr = [];

        $arr[] = [
            'id' => 1,
            'room' => 'room 123',
            'site' => 'location A',
            'url' => 'https://example.org',
            'startDate' => "2016-09-08T15:00:00+00:00",
            'endDate' => "2016-09-08T17:00:00+00:00",
            'session' => 1,
            'learnerGroups' => ['1', '5'],
            'instructorGroups' => ['1'],
            'learners' => [],
            'instructors' => [],
        ];

        $arr[] = [
            'id' => 2,
            'room' => 'second room',
            'site' => 'location B',
            'startDate' => $this->getFormattedDate('now'),
            'endDate' => $this->getFormattedDate('+1 hour'),
            'session' => 1,
            'learnerGroups' => ['2', '5'],
            'instructorGroups' => [],
            'learners' => [],
            'instructors' => [],
        ];

        $arr[] = [
            'id' => 3,
            'room' => 'room 3',
            'site' => 'yet another location',
            'startDate' => "2014-10-15T15:00:00+00:00",
            'endDate' => "2014-10-15T17:00:00+00:00",
            'session' => 2,
            'learnerGroups' => [],
            'instructorGroups' => ['2'],
            'learners' => [],
            'instructors' => [],
        ];

        $arr[] = [
            'id' => 4,
            'room' => 'room 77',
            'site' => 'site 4',
            'startDate' => "2014-11-15T15:00:00+00:00",
            'endDate' => "2014-11-15T17:00:00+00:00",
            'session' => 2,
            'learnerGroups' => [],
            'instructorGroups' => [],
            'learners' => ['2'],
            'instructors' => [],
        ];

        $arr[] = [
            'id' => 5,
            'room' => 'conference room 22',
            'site' => 'offsite',
            'url' => 'https://example.com',
            'startDate' => "2014-12-15T15:00:00+00:00",
            'endDate' => "2014-12-15T17:00:00+00:00",
            'session' => 2,
            'learnerGroups' => [],
            'instructorGroups' => [],
            'learners' => [],
            'instructors' => ["2"],
        ];

        $arr[] = [
            'id' => 6,
            'room' => 'lobby',
            'site' => 'library',
            'startDate' => "2015-01-15T15:00:00+00:00",
            'endDate' => "2015-02-15T17:00:00+00:00",
            'session' => 3,
            'learnerGroups' => [],
            'instructorGroups' => [],
            'learners' => [],
            'instructors' => ['1'],
        ];

        $arr[] = [
            'id' => 7,
            'room' => 'vertical shaft',
            'site' => 'mcb',
            'startDate' => "2015-02-15T15:00:00+00:00",
            'endDate' => "2015-02-15T17:00:00+00:00",
            'session' => 3,
            'learnerGroups' => [],
            'instructorGroups' => [],
            'learners' => ['5'],
            'instructors' => [],
        ];

        $arr[] = [
            'id' => 8,
            'room' => 'hub',
            'site' => 'main campus',
            'startDate' => $this->getFormattedDate('now'),
            'endDate' => $this->getFormattedDate('+30 minutes'),
            'session' => 3,
            'learnerGroups' => [],
            'instructorGroups' => [],
            'learners' => [],
            'instructors' => ['1'],
        ];


        return $arr;
    }

    public function create(): array
    {
        return [
            'id' => 9,
            'room' => 'example room',
            'site' => 'other campus',
            'startDate' => "2014-09-15T15:00:00+00:00",
            'endDate' => "2014-09-15T17:00:00+00:00",
            'session' => 1,
            'learnerGroups' => [],
            'instructorGroups' => [],
            'learners' => [],
            'instructors' => [],
        ];
    }

    public function createInvalid(): array
    {
        return [];
    }

    public function getDtoClass(): string
    {
        return OfferingDTO::class;
    }
}
