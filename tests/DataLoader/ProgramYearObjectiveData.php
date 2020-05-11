<?php

declare(strict_types=1);

namespace App\Tests\DataLoader;

class ProgramYearObjectiveData extends AbstractDataLoader
{
    protected function getData()
    {
        $arr = array();

        $arr[] = array(
            'id' => 1,
            'position' => 0,
            'programYear' => '1',
            'objective' => '1',
            'terms' => ['2', '4'],
        );

        $arr[] = array(
            'id' => 2,
            'position' => 0,
            'programYear' => '1',
            'objective' => '2',
            'terms' => ['2'],
        );

        $arr[] = array(
            'id' => 3,
            'position' => 0,
            'programYear' => '5',
            'objective' => '8',
            'terms' => [],
        );

        return $arr;
    }

    public function create()
    {
        return array(
            'id' => 4,
            'position' => 0,
            'programYear' => '2',
            'objective' => '9',
            'terms' => [],
        );
    }

    public function createInvalid()
    {
        return [];
    }
}
