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
        );

        $arr[] = array(
            'id' => 2,
            'programs' => [],
            'programYears' => ['1'],
            'courses' => [],
            'sessions' => [],
        );

        $arr[] = array(
            'id' => 3,
            'programs' => [],
            'programYears' => [],
            'courses' => ['1'],
            'sessions' => [],

        );

        $arr[] = array(
            'id' => 4,
            'programs' => [],
            'programYears' => [],
            'courses' => [],
            'sessions' => ['1'],
        );
        return $arr;
    }

    public function create()
    {
        return [
            'id' => 5,
            'programs' => [],
            'programYears' => [],
            'courses' => ['1'],
            'sessions' => [],
        ];
    }

    public function createInvalid()
    {
        return [
            'programs' => [11],
        ];
    }
}
