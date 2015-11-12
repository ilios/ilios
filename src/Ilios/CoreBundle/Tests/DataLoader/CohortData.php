<?php

namespace Ilios\CoreBundle\Tests\DataLoader;

class CohortData extends AbstractDataLoader
{
    protected function getData()
    {
        $arr = array();

        $arr[] = array(
            'id' => 1,
            'title' => "Class of 2017",
            'programYear' => '1',
            'courses' => ['1', '2'],
            'learnerGroups' => ['1', '2', '3'],
            'users' => ['1', '2']
        );

        $arr[] = array(
            'id' => 2,
            'title' => "Class of 2018",
            'programYear' => '2',
            'courses' => ["3"],
            'learnerGroups' => [],
            'users' => []
        );

        $arr[] = array(
            'id' => 3,
            'title' => "Class of 2019",
            'programYear' => '3',
            'courses' => ["4"],
            'learnerGroups' => [],
            'users' => []
        );


        return $arr;
    }

    public function create()
    {
        return [
            'id' => 4,
            'title' => $this->faker->text(15),
            'programYear' => "4",
            'courses' => ['1'],
            'learnerGroups' => [],
            'users' => ['1']
        ];
    }

    public function createInvalid()
    {
        return [
            'id' => 'string',
            'title' => null,
            'programYear' => null
        ];
    }
}
