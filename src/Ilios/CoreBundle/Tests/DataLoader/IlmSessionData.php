<?php

namespace Ilios\CoreBundle\Tests\DataLoader;

class IlmSessionData extends AbstractDataLoader
{
    protected function getData()
    {
        $arr = array();
        $dt = $this->faker->dateTime;
        $dt->setTime(0, 0, 0);
        $arr[] = array(
            'id' => 1,
            'hours' => $this->faker->randomDigitNotNull,
            'dueDate' => $dt->format('c'),
            'learnerGroups' => ['1'],
            'instructorGroups' => ['1'],
            'instructors' => ['1'],
            'learners' => ['1'],
            'session' => 1
        );

        return $arr;
    }

    public function create()
    {
        $dt = $this->faker->dateTime;
        $dt->setTime(0, 0, 0);
        return array(
            'id' => 2,
            'hours' => $this->faker->randomDigitNotNull,
            'dueDate' => $dt->format('c'),
            'learnerGroups' => ['1', '2'],
            'instructorGroups' => ['1', '2'],
            'instructors' => ['1', '2'],
            'learners' => ['1', '2']
        );
    }

    public function createInvalid()
    {
        return [];
    }
}
