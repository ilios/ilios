<?php

namespace Tests\CoreBundle\DataLoader;

class MeshQualifierData extends AbstractDataLoader
{
    protected function getData()
    {
        $arr = array();
        $arr[] = array(
            'id' => '1',
            'name' => 'qual' . $this->faker->text(5),
            'descriptors' => ['abc1']
        );
        $arr[] = array(
            'id' => '2',
            'name' => 'qual' . $this->faker->text(5),
            'descriptors' => ['abc1']
        );

        return $arr;
    }

    public function create()
    {
        return array(
            'id' => '3',
            'name' => $this->faker->text(5),
            'descriptors' => []
        );
    }

    public function createInvalid()
    {
        return [];
    }
}
