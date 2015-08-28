<?php

namespace Ilios\CoreBundle\Tests\DataLoader;

class MeshQualifierData extends AbstractDataLoader
{
    protected function getData()
    {
        $arr = array();
        $arr[] = array(
            'id' => '1',
            'name' => $this->faker->text,
            'descriptors' => ['abc1']
        );
        $arr[] = array(
            'id' => '2',
            'name' => $this->faker->text,
            'descriptors' => ['abc1']
        );

        return $arr;
    }

    public function create()
    {
        return array(
            'id' => '3',
            'name' => $this->faker->text,
            'descriptors' => []
        );
    }

    public function createInvalid()
    {
        return array(
            'id' => 'bad'
        );
    }
}
