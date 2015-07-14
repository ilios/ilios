<?php

namespace Ilios\CoreBundle\Tests\DataLoader;

class LearnerGroupData extends AbstractDataLoader
{
    protected function getData()
    {
        $arr = array();

        $arr[] = array(
            'id' => 1,
            'title' => $this->faker->text(25),
            'location' => $this->faker->text(25),
            'cohort' => '1',
            'children' => [],
            'ilmSessions' => ['1'],
            'offerings' => ['1'],
            'instructorGroups' => [],
            'users' => [],
            'instructorUsers' => []
        );

        $arr[] = array(
            'id' => 2,
            'title' =>$this->faker->text(25),
            'cohort' => '1',
            'children' => [],
            'ilmSessions' => [],
            'offerings' => [],
            'instructorGroups' => [],
            'users' => [],
            'instructorUsers' => []
        );


        return $arr;
    }

    public function create()
    {
        return array(
            'id' => 3,
            'title' => $this->faker->text(25),
            'cohort' => "1",
            'children' => [],
            'ilmSessions' => [],
            'offerings' => [],
            'instructorGroups' => [],
            'users' => [],
            'instructorUsers' => []
        );
    }

    public function createInvalid()
    {
        return [];
    }
}
