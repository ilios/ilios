<?php

namespace Ilios\CoreBundle\Tests\DataLoader;

class PublishEventData extends AbstractDataLoader
{
    protected function getData()
    {
        $arr = array();

        $arr[] = array(
            'id' => 1,
            'sessions' => ['1'],
            'courses' => ['1'],
            'programs' => ['1'],
            'programYears' => ['1'],
        );

        $arr[] = array(
            'id' => 2,
            'sessions' => [],
            'courses' => [],
            'programs' => [],
            'programYears' => [],
        );


        return $arr;
    }

    public function create()
    {
        return [
            'id' => 3,
            'courses' => ['1'],
            'sessions' => ['1'],
            'programs' => ['1'],
            'programYears' => ['1'],
        ];
    }

    public function createInvalid()
    {
        return [];
    }
}
