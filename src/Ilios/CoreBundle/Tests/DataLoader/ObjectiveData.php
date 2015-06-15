<?php

namespace Ilios\CoreBundle\Tests\DataLoader;

class ObjectiveData extends AbstractDataLoader
{
    protected function getData()
    {
        $arr = array();

        $arr[] = array(
            'id' => 47719,
            'title' => $this->faker->text,
            'competency' => "7",
            'courses' => [],
            'programYears' => ['42'],
            'sessions' => [],
            'parents' => [],
            'children' => [],
            'meshDescriptors' => []
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
