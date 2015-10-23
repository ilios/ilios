<?php

namespace Ilios\CoreBundle\Tests\DataLoader;

class PublishEventData extends AbstractDataLoader
{
    protected function getData()
    {
        $arr = array();

        $arr[] = array(
            'id' => 1,
            'programs' => ['1'],
            'programYears' => [],
            'courses' => [],
            'sessions' => [],
            'offerings' => [],
        );

        $arr[] = array(
            'id' => 2,
            'programs' => [],
            'programYears' => ['1'],
            'courses' => [],
            'sessions' => [],
            'offerings' => [],
        );

        $arr[] = array(
            'id' => 3,
            'programs' => [],
            'programYears' => [],
            'courses' => ['1'],
            'sessions' => [],
            'offerings' => [],

        );

        $arr[] = array(
            'id' => 4,
            'programs' => [],
            'programYears' => [],
            'courses' => [],
            'sessions' => ['1'],
            'offerings' => [],
        );

        $arr[] = array(
            'id' => 5,
            'programs' => [],
            'programYears' => [],
            'courses' => [],
            'sessions' => [],
            'offerings' => ['1'],
        );
        return $arr;
    }

    public function create()
    {
        return [
            'id' => 6,
            'programs' => [],
            'programYears' => [],
            'courses' => [],
            'sessions' => [],
            'offerings' => []
        ];
    }

    public function createInvalid()
    {
        return [
            'programs' => [11],
        ];
    }
}
