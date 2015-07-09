<?php

namespace Ilios\CoreBundle\Tests\DataLoader;

class RecurringEventData extends AbstractDataLoader
{
    protected function getData()
    {
        $arr = array();
        
        $arr[] = array(
            'id' => 1,
            'offerings' => []
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
