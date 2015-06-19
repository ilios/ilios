<?php

namespace Ilios\CoreBundle\Tests\DataLoader;

class PublishEventData extends AbstractDataLoader
{
    protected function getData()
    {
        $arr = array();

        // $arr[] = array(
        //     'id' => 1,
        //     'sessions' => [],
        //     'courses' => []
        // );


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
