<?php

namespace Tests\CoreBundle\DataLoader;

class InstructorGroupData extends AbstractDataLoader
{
    protected function getData()
    {
        $arr = array();

        $arr[] = array(
            'id' => 1,
            'title' => $this->faker->text(10),
            'school' => '1',
            'learnerGroups' => ['1'],
            'ilmSessions' => ['1'],
            'users' => ['2'],
            'offerings' => ['1']
        );

        $arr[] = array(
            'id' => 2,
            'title' => 'second instructor group',
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

        $arr[] = array(
            'id' => 4,
            'title' => $this->faker->text(10),
            'school' => '2',
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
            'id' => 5,
            'title' => $this->faker->text(10),
            'school' => '1',
            'learnerGroups' => ['1'],
            'ilmSessions' => ['1'],
            'users' => [],
            'offerings' => []
        );
    }

    public function createInvalid()
    {
        return [];
    }
}
