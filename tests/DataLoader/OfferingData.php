<?php

declare(strict_types=1);

namespace App\Tests\DataLoader;

use App\Entity\DTO\OfferingDTO;

class OfferingData extends AbstractDataLoader
{
    protected function getData(): array
    {
        $arr = [];

        $arr[] = [
            'id' => 1,
            'room' => $this->faker->text(10),
            'site' => $this->faker->text(10),
            'url' => $this->faker->url(),
            'startDate' => "2016-09-08T15:00:00+00:00",
            'endDate' => "2016-09-08T17:00:00+00:00",
            'session' => 1,
            'learnerGroups' => ['1', '5'],
            'instructorGroups' => ['1'],
            'learners' => [],
            'instructors' => []
        ];

        $arr[] = [
            'id' => 2,
            'room' => $this->faker->text(10),
            'site' => $this->faker->text(10),
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
            'site' => $this->faker->text(10),
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
            'room' => $this->faker->text(10),
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
            'room' => $this->faker->text(10),
            'site' => $this->faker->text(10),
            'url' => 'http://example.com',
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
            'room' => $this->faker->text(10),
            'site' => $this->faker->text(10),
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
            'room' => $this->faker->text(10),
            'site' => $this->faker->text(10),
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
            'room' => $this->faker->text(10),
            'site' => $this->faker->text(10),
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
            'room' => $this->faker->text(10),
            'site' => $this->faker->text(10),
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
