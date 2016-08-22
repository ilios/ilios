<?php

namespace Tests\CoreBundle\DataLoader;

class MeshSemanticTypeData extends AbstractDataLoader
{
    protected function getData()
    {
        $arr = array();
        $arr[] = array(
            'id' => '1',
            'name' => 'type' . $this->faker->text,
            'concepts' => ['1']
        );
        $arr[] = array(
            'id' => '2',
            'name' => 'type' . $this->faker->text,
            'concepts' => ['1']
        );

        return $arr;
    }

    public function create()
    {
        return array(
            'id' => '3',
            'name' => $this->faker->text,
            'concepts' => ['1']
        );
    }

    public function createInvalid()
    {
        return array(
            'id' => 'bad'
        );
    }
}
