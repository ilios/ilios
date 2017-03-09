<?php

namespace Tests\CoreBundle\DataLoader;

class ObjectiveData extends AbstractDataLoader
{
    protected function getData()
    {
        $arr = array();

        $arr[] = array(
            'id' => 1,
            'title' => $this->faker->text,
            'position' => 0,
            'competency' => '3',
            'courses' => [],
            'programYears' => ['1'],
            'sessions' => [],
            'parents' => [],
            'children' => ['2'],
            'meshDescriptors' => [],
            'descendants' => []
        );

        $arr[] = array(
            'id' => 2,
            'title' => 'second objective',
            'position' => 0,
            'courses' => ['1', '2', '4'],
            'programYears' => ['1'],
            'sessions' => ['1'],
            'parents' => ['1'],
            'children' => ['3', '6'],
            'meshDescriptors' => ['abc1'],
            'descendants' => ['3']
        );

        $arr[] = array(
            'id' => 3,
            'title' => $this->faker->text,
            'position' => 0,
            'courses' => [],
            'programYears' => [],
            'sessions' => ['1'],
            'parents' => ['2'],
            'children' => [],
            'meshDescriptors' => [],
            'ancestor' => '2',
            'descendants' => []
        );

        $arr[] = array(
            'id' => 4,
            'title' => $this->faker->text,
            'position' => 0,
            'courses' => ["2"],
            'programYears' => [],
            'sessions' => [],
            'parents' => [],
            'children' => [],
            'meshDescriptors' => [],
            'descendants' => []
        );

        $arr[] = array(
            'id' => 5,
            'title' => $this->faker->text,
            'position' => 0,
            'courses' => ["3"],
            'programYears' => [],
            'sessions' => [],
            'parents' => [],
            'children' => [],
            'meshDescriptors' => ["abc1"],
            'descendants' => []
        );

        $arr[] = array(
            'id' => 6,
            'title' => $this->faker->text,
            'position' => 0,
            'courses' => [],
            'programYears' => [],
            'sessions' => ["4"],
            'parents' => ['2'],
            'children' => [],
            'meshDescriptors' => ["abc1"],
            'descendants' => ['7']
        );

        $arr[] = array(
            'id' => 7,
            'title' => $this->faker->text,
            'position' => 0,
            'courses' => [],
            'programYears' => [],
            'sessions' => ["4"],
            'parents' => [],
            'children' => [],
            'meshDescriptors' => ["abc3"],
            'ancestor' => '6',
            'descendants' => []
        );

        return $arr;
    }

    public function create()
    {
        return array(
            'id' => 8,
            'title' => $this->faker->text,
            'position' => 0,
            'competency' => "1",
            'courses' => ['1'],
            'programYears' => ['2'],
            'sessions' => ['1'],
            'parents' => ['1'],
            'children' => [],
            'meshDescriptors' => [],
            'descendants' => []

        );
    }

    public function createInvalid()
    {
        return [];
    }
}
