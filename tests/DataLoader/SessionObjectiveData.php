<?php

declare(strict_types=1);

namespace App\Tests\DataLoader;

class SessionObjectiveData extends AbstractDataLoader
{
    protected function getData()
    {
        $arr = array();

        $arr[] = array(
            'id' => 1,
            'position' => 0,
            'session' => '1',
            'objective' => '3',
            'terms' => ['3', '4'],
        );

        $arr[] = array(
            'id' => 2,
            'position' => 0,
            'session' => '4',
            'objective' => '6',
            'terms' => ['3'],
        );

        $arr[] = array(
            'id' => 3,
            'position' => 0,
            'session' => '4',
            'objective' => '7',
            'terms' => [],
        );

        return $arr;
    }

    public function create()
    {
        return array(
            'id' => 4,
            'position' => 0,
            'session' => '1',
            'objective' => '11',
            'terms' => [],
        );
    }

    public function createInvalid()
    {
        return [];
    }
}
