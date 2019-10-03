<?php

namespace App\Tests\DataLoader;

class ObjectiveData extends AbstractDataLoader
{
    protected function getData()
    {
        $arr = array();

        $arr[] = array(
            'id' => 1,
            'title' => 'first objective',
            'position' => 0,
            'active' => true,
            'competency' => '3',
            'courses' => [],
            'programYears' => ['1'],
            'sessions' => [],
            'parents' => [],
            'children' => ['2'],
            'meshDescriptors' => [],
            'descendants' => ['8'],
            'terms' => ['1', '4'],
        );

        $arr[] = array(
            'id' => 2,
            'title' => 'second objective',
            'position' => 0,
            'active' => true,
            'courses' => ['1', '2', '4'],
            'programYears' => ['1'],
            'sessions' => [],
            'parents' => ['1'],
            'children' => ['3', '6'],
            'meshDescriptors' => ['abc1'],
            'descendants' => ['3'],
            'terms' => ['2', '3'],
        );

        $arr[] = array(
            'id' => 3,
            'title' => 'third objective',
            'position' => 0,
            'active' => true,
            'courses' => [],
            'programYears' => [],
            'sessions' => ['1'],
            'parents' => ['2'],
            'children' => [],
            'meshDescriptors' => [],
            'ancestor' => '2',
            'descendants' => [],
            'terms' => [],
        );

        $arr[] = array(
            'id' => 4,
            'title' => 'fourth objective',
            'position' => 0,
            'active' => true,
            'courses' => ["2"],
            'programYears' => [],
            'sessions' => [],
            'parents' => [],
            'children' => [],
            'meshDescriptors' => [],
            'descendants' => [],
            'terms' => ['1'],
        );

        $arr[] = array(
            'id' => 5,
            'title' => 'fifth objective',
            'position' => 0,
            'active' => false,
            'courses' => ["3"],
            'programYears' => [],
            'sessions' => [],
            'parents' => [],
            'children' => [],
            'meshDescriptors' => ["abc1"],
            'descendants' => [],
            'terms' => ['4'],
        );

        $arr[] = array(
            'id' => 6,
            'title' =>'sixth objective',
            'position' => 0,
            'active' => true,
            'courses' => [],
            'programYears' => [],
            'sessions' => ["4"],
            'parents' => ['2'],
            'children' => [],
            'meshDescriptors' => ["abc1"],
            'descendants' => ['7'],
            'terms' => [],
        );

        $arr[] = array(
            'id' => 7,
            'title' => 'seventh objective',
            'position' => 0,
            'active' => false,
            'courses' => [],
            'programYears' => [],
            'sessions' => ["4"],
            'parents' => [],
            'children' => [],
            'meshDescriptors' => ["abc3"],
            'ancestor' => '6',
            'descendants' => [],
            'terms' => [],
        );
        $arr[] = array(
            'id' => 8,
            'title' => 'eighth objective',
            'position' => 0,
            'active' => true,
            'courses' => [],
            'programYears' => ['5'],
            'sessions' => [],
            'parents' => [],
            'children' => [],
            'meshDescriptors' => [],
            'ancestor' => '1',
            'descendants' => [],
            'terms' => [],
        );

        return $arr;
    }

    public function create()
    {
        return array(
            'id' => 9,
            'title' => $this->faker->text,
            'position' => 0,
            'active' => true,
            'competency' => "1",
            'courses' => ['1'],
            'programYears' => ['2'],
            'sessions' => ['1'],
            'parents' => ['1'],
            'children' => [],
            'meshDescriptors' => [],
            'descendants' => [],
            'terms' => [],

        );
    }

    public function createInvalid()
    {
        return [];
    }
}
