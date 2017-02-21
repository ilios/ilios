<?php

namespace Tests\CoreBundle\DataLoader;

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
            'children' => ['4'],
            'ilmSessions' => ['1'],
            'offerings' => ['1'],
            'instructorGroups' => ['1'],
            'users' => ['2', '5'],
            'instructors' => ['1']
        );

        $arr[] = array(
            'id' => 2,
            'title' =>$this->faker->text(25),
            'cohort' => '2',
            'children' => [],
            'ilmSessions' => [],
            'offerings' => ['2'],
            'instructorGroups' => [],
            'users' => ['2'],
            'instructors' => []
        );

        $arr[] = array(
            'id' => 3,
            'title' =>'third learner group',
            'cohort' => '1',
            'children' => [],
            'ilmSessions' => ['1'],
            'offerings' => [],
            'instructorGroups' => [],
            'users' => ['2'],
            'instructors' => ['1']
        );


        $arr[] = array(
            'id' => 4,
            'title' =>$this->faker->text(25),
            'location' => 'fourth location',
            'cohort' => '1',
            'children' => [],
            'parent' => '1',
            'ilmSessions' => [],
            'offerings' => [],
            'instructorGroups' => [],
            'users' => [],
            'instructors' => []
        );


        $arr[] = array(
            'id' => 5,
            'title' =>$this->faker->text(25),
            'cohort' => '1',
            'children' => [],
            'ilmSessions' => [],
            'offerings' => ['1', '2'],
            'instructorGroups' => [],
            'users' => ['5'],
            'instructors' => []
        );


        return $arr;
    }

    public function create()
    {
        return array(
            'id' => 6,
            'title' => $this->faker->text(25),
            'cohort' => "1",
            'children' => [],
            'ilmSessions' => ['1'],
            'offerings' => ['1'],
            'instructorGroups' => [],
            'users' => [],
            'instructors' => []
        );
    }

    public function createInvalid()
    {
        return [];
    }
}
