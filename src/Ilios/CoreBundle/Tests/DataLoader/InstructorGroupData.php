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
            'ilmSessions' => ['1'],
            'users' => ['1'],
            'offerings' => []
        );

        $arr[] = array(
            'id' => 2,
            'title' => $this->faker->text(10),
            'school' => '1',
            'learnerGroups' => [],
            'ilmSessions' => [],
            'users' => [],
            'offerings' => []
        );


        return $arr;
    }

    public function create()
    {
        return array(
            'id' => 3,
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
