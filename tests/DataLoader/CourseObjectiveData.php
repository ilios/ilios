<?php

declare(strict_types=1);

namespace App\Tests\DataLoader;

class CourseObjectiveData extends AbstractDataLoader
{
    protected function getData()
    {
        $arr = array();

        $arr[] = array(
            'id' => 1,
            'position' => 0,
            'course' => '1',
            'objective' => '2',
            'terms' => ['1', '4'],
        );

        $arr[] = array(
            'id' => 2,
            'position' => 0,
            'course' => '2',
            'objective' => '2',
            'terms' => ['1'],
        );

        $arr[] = array(
            'id' => 3,
            'position' => 0,
            'course' => '4',
            'objective' => '2',
            'terms' => [],
        );

        $arr[] = array(
            'id' => 4,
            'position' => 0,
            'course' => '2',
            'objective' => '4',
            'terms' => [],
        );

        $arr[] = array(
            'id' => 5,
            'position' => 0,
            'course' => '3',
            'objective' => '5',
            'terms' => [],
        );

        return $arr;
    }

    public function create()
    {
        return array(
            'id' => 6,
            'position' => 0,
            'course' => '1',
            'objective' => '10',
            'terms' => [],
        );
    }

    public function createInvalid()
    {
        return [];
    }
}
