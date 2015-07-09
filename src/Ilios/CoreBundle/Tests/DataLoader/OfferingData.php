<?php

namespace Ilios\CoreBundle\Tests\DataLoader;

class OfferingData extends AbstractDataLoader
{
    protected function getData()
    {
        $arr = array();

        $arr[] = array(
            'id' => 1,
            'room' => $this->faker->text(10),
            'startDate' => "2014-09-15T15:00:00+00:00",
            'endDate' => "2014-09-15T17:00:00+00:00",
            'deleted' => false,
            'session' => '1',
            'learnerGroups' => [],
            'instructorGroups' => [],
            'learners' => [],
            'instructors' => [],
            'recurringEvents' => []
        );

        $arr[] = array(
            'id' => 2,
            'room' => $this->faker->text(10),
            'startDate' => "2014-09-15T15:00:00+00:00",
            'endDate' => "2014-09-15T17:00:00+00:00",
            'deleted' => false,
            'session' => '1',
            'learnerGroups' => [],
            'instructorGroups' => [],
            'learners' => [],
            'instructors' => [],
            'recurringEvents' => []
        );


        return $arr;
    }

    public function create()
    {
        return array(
            'id' => 3,
            'room' => $this->faker->text(10),
            'startDate' => "2014-09-15T15:00:00+00:00",
            'endDate' => "2014-09-15T17:00:00+00:00",
            'deleted' => false,
            'session' => '1',
            'learnerGroups' => [],
            'instructorGroups' => [],
            'learners' => [],
            'instructors' => [],
            'recurringEvents' => []
        );
    }

    public function createInvalid()
    {
        return [];
    }
}
