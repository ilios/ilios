<?php

namespace Ilios\CoreBundle\Tests\DataLoader;

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
            'aamcMethods' => ['AM001', 'AM002'],
            'sessions' => ['1']
        );

        $arr[] = array(
            'id' => 2,
            'title' => $this->faker->text(50),
            'assessmentOption' => '2',
            'school' => '1',
            'aamcMethods' => ['AM001'],
            'sessions' => ['2'. '4']
        );


        return $arr;
    }

    public function create()
    {
        return array(
            'id' => 3,
            'title' => $this->faker->text(50),
            'school' => '1',
            'aamcMethods' => []
        );
    }

    public function createInvalid()
    {
        return [];
    }
}
