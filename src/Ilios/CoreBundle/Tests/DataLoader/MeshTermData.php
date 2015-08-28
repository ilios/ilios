<?php

namespace Ilios\CoreBundle\Tests\DataLoader;

class MeshTermData extends AbstractDataLoader
{
    protected function getData()
    {
        $arr = array();
        $arr[] = array(
            'id' => '1',
            'meshTermUid' => 'term' . $this->faker->text,
            'name' => 'term' . $this->faker->text,
            'lexicalTag' => 'term' . $this->faker->text,
            'conceptPreferred' => true,
            'recordPreferred' => true,
            'permuted' => true,
            'printable' => true,
            'concepts' => ['1']
        );
        $arr[] = array(
            'id' => '2',
            'meshTermUid' => 'term' . $this->faker->text,
            'name' => 'term' . $this->faker->text,
            'lexicalTag' => 'term' . $this->faker->text,
            'conceptPreferred' => true,
            'recordPreferred' => true,
            'permuted' => true,
            'printable' => true,
            'concepts' => ['1']
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
