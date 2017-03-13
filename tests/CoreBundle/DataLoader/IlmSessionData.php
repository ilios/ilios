<?php

namespace Tests\CoreBundle\DataLoader;

class IlmSessionData extends AbstractDataLoader
{
    protected function getData()
    {
        $arr = array();
        $dt = $this->faker->dateTime;
        $dt->setTime(0, 0, 0);
        $dt->setDate(2016, 1, 1);
        $arr[] = array(
            'id' => 1,
            'hours' => $this->faker->randomFloat(2),
            'dueDate' => $dt->format('c'),
            'learnerGroups' => ['1', '3'],
            'instructorGroups' => ['1'],
            'instructors' => [],
            'learners' => [],
            'session' => '5'
        );
        $dt->modify('+1 month');
        $arr[] = array(
            'id' => 2,
            'hours' => 21.2,
            'dueDate' => $dt->format('c'),
            'learnerGroups' => [],
            'instructorGroups' => ['3'],
            'instructors' => [],
            'learners' => [],
            'session' => '6'
        );

        $dt->modify('+1 month');
        $arr[] = array(
            'id' => 3,
            'hours' => $this->faker->randomFloat(2),
            'dueDate' => $dt->format('c'),
            'learnerGroups' => [],
            'instructorGroups' => [],
            'instructors' => ['2'],
            'learners' => [],
            'session' => '7'
        );

        $dt->modify('+1 month');
        $arr[] = array(
            'id' => 4,
            'hours' => $this->faker->randomFloat(2),
            'dueDate' => $dt->format('c'),
            'learnerGroups' => [],
            'instructorGroups' => [],
            'instructors' => [],
            'learners' => ['2'],
            'session' => '8'
        );

        return $arr;
    }

    public function create()
    {
        $dt = $this->faker->dateTime;
        $dt->setTime(0, 0, 0);
        return array(
            'id' => 5,
            'hours' => $this->faker->randomFloat(2),
            'dueDate' => $dt->format('c'),
            'learnerGroups' => ['1', '2'],
            'instructorGroups' => ['1', '2'],
            'instructors' => ['1', '2'],
            'learners' => ['1', '2'],
            'session' => '1'
        );
    }

    public function createInvalid()
    {
        return [];
    }
}
