<?php

namespace Tests\CoreBundle\DataLoader;

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
            'learnerGroups' => ['1', '3', '4', '5'],
            'users' => ['1', '2']
        );

        $arr[] = array(
            'id' => 2,
            'title' => "Class of 2018",
            'programYear' => '2',
            'courses' => ["3"],
            'learnerGroups' => ['2'],
            'users' => []
        );

        $arr[] = array(
            'id' => 3,
            'title' => "Class of 2019",
            'programYear' => '3',
            'courses' => ["4", "5"],
            'learnerGroups' => [],
            'users' => []
        );

        $arr[] = array(
            'id' => 4,
            'title' => "Class of 2020",
            'programYear' => '4',
            'courses' => [],
            'learnerGroups' => [],
            'users' => []
        );


        return $arr;
    }

    public function create()
    {
        return [
            'id' => 5,
            'title' => $this->faker->text(15),
            'programYear' => "5",
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
