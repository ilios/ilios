<?php

namespace Ilios\CoreBundle\Tests\DataLoader;

class MeshSemanticTypeData extends AbstractDataLoader
{
    protected function getData()
    {
        $arr = array();
        $arr[] = array(
            'id' => '1',
            'meshTermUid' => $this->faker->text,
            'name' => $this->faker->text,
            'lexicalTag' => $this->faker->text,
            'conceptPreferred' => true,
            'recordPreferred' => true,
            'permuted' => true,
            'printable' => true,
            'concepts' => ['1', '2']
        );
        $arr[] = array(
            'id' => '2',
            'meshTermUid' => $this->faker->text,
            'name' => $this->faker->text,
            'lexicalTag' => $this->faker->text,
            'conceptPreferred' => true,
            'recordPreferred' => true,
            'permuted' => true,
            'printable' => true,
            'concepts' => []
        );

        return $arr;
    }

    public function create()
    {
        
        return array(
            'id' => '3',
            'meshTermUid' => $this->faker->text,
            'name' => $this->faker->text,
            'lexicalTag' => $this->faker->text,
            'conceptPreferred' => true,
            'recordPreferred' => true,
            'permuted' => true,
            'printable' => true,
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
