<?php

namespace Ilios\CoreBundle\Tests\DataLoader;

class SessionTypeData extends AbstractDataLoader
{
    protected function getData()
    {
        $arr = array();

        $arr[] = array(
            'id' => 1,
            'title' => $this->faker->text,
            'assessmentOption' => "2",
            'owningSchool' => "1",
            'aamcMethods' => ['AM001', 'AM002'],
            'sessions' => []
        );

        $arr[] = array(
            'id' => 2,
            'title' => $this->faker->text,
            'assessmentOption' => "2",
            'owningSchool' => "1",
            'aamcMethods' => ['AM001'],
            'sessions' => []
        );


        return $arr;
    }

    public function create()
    {
        return [];
    }

    public function createInvalid()
    {
        return [];
    }
}
