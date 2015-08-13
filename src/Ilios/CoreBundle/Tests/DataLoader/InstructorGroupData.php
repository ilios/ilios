<?php

namespace Ilios\CoreBundle\Tests\DataLoader;

class InstructorGroupData extends AbstractDataLoader
{
    protected function getData()
    {
        $arr = array();

        $arr[] = array(
            'id' => 1,
            'title' => $this->faker->text(10),
            'school' => '1',
            'learnerGroups' => [],
            'ilmSessions' => [],
            'users' => ['2'],
            'offerings' => ['1']
        );

        $arr[] = array(
            'id' => 2,
            'title' => $this->faker->text(10),
            'school' => '1',
            'learnerGroups' => [],
            'ilmSessions' => [],
            'users' => ['2'],
            'offerings' => ['3']
        );

        $arr[] = array(
            'id' => 3,
            'title' => $this->faker->text(10),
            'school' => '1',
            'learnerGroups' => [],
            'ilmSessions' => ['2'],
            'users' => ['2'],
            'offerings' => []
        );


        return $arr;
    }

    public function create()
    {
        return array(
            'id' => 4,
            'title' => $this->faker->text(10),
            'school' => '1',
            'learnerGroups' => [],
            'ilmSessions' => [],
            'users' => [],
            'offerings' => []
        );
    }

    public function createInvalid()
    {
        return [];
    }
}
