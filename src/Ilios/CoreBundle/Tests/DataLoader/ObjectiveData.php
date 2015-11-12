<?php

namespace Ilios\CoreBundle\Tests\DataLoader;

class ObjectiveData extends AbstractDataLoader
{
    protected function getData()
    {
        $arr = array();

        $arr[] = array(
            'id' => 1,
            'title' => $this->faker->text,
            'competency' => "1",
            'courses' => ['1', '2', '4'],
            'programYears' => [],
            'sessions' => ['1'],
            'parents' => [],
            'children' => ['2'],
            'meshDescriptors' => []
        );

        $arr[] = array(
            'id' => 2,
            'title' => $this->faker->text,
            'competency' => "1",
            'courses' => [],
            'programYears' => [],
            'sessions' => ['1'],
            'parents' => ['1'],
            'children' => [],
            'meshDescriptors' => []
        );

        $arr[] = array(
            'id' => 3,
            'title' => $this->faker->text,
            'competency' => "2",
            'courses' => ["2"],
            'programYears' => [],
            'sessions' => [],
            'parents' => [],
            'children' => [],
            'meshDescriptors' => []
        );


        return $arr;
    }

    public function create()
    {
        return array(
            'id' => 4,
            'title' => $this->faker->text,
            'competency' => "1",
            'courses' => ['1'],
            'programYears' => ['2'],
            'sessions' => ['1'],
            'parents' => ['1'],
            'children' => [],
            'meshDescriptors' => []
        );
    }

    public function createInvalid()
    {
        return [];
    }
}
