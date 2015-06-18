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
            'lastUpdatedOn' => "2015-01-12T04:11:12+00:00",
            'session' => "1",
            'learnerGroups' => [],
            'publishEvent' => "",
            'instructorGroups' => [],
            'learners' => [],
            'instructors' => [],
            'recurringEvents' => []
        );


        return $arr;
    }

    public function create()
    {
        return [];
    }

    public function createInvalid()
    {
        return [];
    }
}
