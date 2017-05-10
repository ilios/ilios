<?php

namespace Tests\CoreBundle\DataLoader;

class SessionTypeData extends AbstractDataLoader
{
    protected function getData()
    {
        $arr = array();

        $arr[] = array(
            'id' => 1,
            'title' => $this->faker->text(50),
            'assessmentOption' => '1',
            'school' => '1',
            'aamcMethods' => ['AM001'],
            'sessions' => ['1', '5', '6', '7', '8'],
            'calendarColor' => $this->faker->hexColor,
            'assessment' => false,
            'active' => false,
        );

        $arr[] = array(
            'id' => 2,
            'title' => 'second session type',
            'assessmentOption' => '2',
            'school' => '1',
            'aamcMethods' => ['AM001'],
            'sessions' => ['2', '3', '4'],
            'calendarColor' => '#0a1b2c',
            'assessment' => true,
            'active' => true,
        );


        return $arr;
    }

    public function create()
    {
        return array(
            'id' => 3,
            'title' => $this->faker->text(50),
            'school' => '1',
            'aamcMethods' => ['AM001'],
            'sessions' => ['1'],
            'calendarColor' => $this->faker->hexColor,
            'assessment' => false,
            'active' => false,
        );
    }

    public function createInvalid()
    {
        return [];
    }
}
