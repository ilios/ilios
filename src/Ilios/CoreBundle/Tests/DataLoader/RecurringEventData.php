<?php

namespace Ilios\CoreBundle\Tests\DataLoader;

class RecurringEventData extends AbstractDataLoader
{
    protected function getData()
    {
        $arr = array();
        
        $arr[] = array(
            'id' => 1,
            'offerings' => ['1'],
            'endDate' => "2013-12-14T00:00:00+00:00",
            'onSunday' => false,
            'onMonday' => false,
            'onTuesday' => false,
            'onWednesday' => false,
            'onThursday' => false,
            'onFriday' => false,
            'onSaturday' => false,
        );
        
        return $arr;
    }

    public function create()
    {
        return array(
            'id' => 2,
            'offerings' => ['2'],
            'endDate' => "2013-12-14T00:00:00+00:00",
            'onSunday' => false,
            'onMonday' => false,
            'onTuesday' => false,
            'onWednesday' => false,
            'onThursday' => false,
            'onFriday' => false,
            'onSaturday' => false,
        );
    }

    public function createInvalid()
    {
        return [
            'id' => 'foobar'
        ];
    }
}
