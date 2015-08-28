<?php

namespace Ilios\CoreBundle\Tests\DataLoader;

class MeshSemanticTypeData extends AbstractDataLoader
{
    protected function getData()
    {
        $arr = array();
        $arr[] = array(
            'id' => '1',
            'name' => $this->faker->text,
            'concepts' => ['1', '2']
        );
        $arr[] = array(
            'id' => '2',
            'name' => $this->faker->text,
            'concepts' => ['abc2']
        );

        return $arr;
    }

    public function create()
    {
        return array(
            'id' => '3',
            'name' => $this->faker->text,
            'concepts' => []
        );
    }

    public function createInvalid()
    {
        return array(
            'id' => 'bad'
        );
    }
}
