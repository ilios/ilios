<?php

namespace Ilios\CoreBundle\Tests\DataLoader;

class MeshConceptData extends AbstractDataLoader
{
    protected function getData()
    {
        $arr = array();
        $arr[] = array(
            'id' => '1',
            'name' => 'concept' . $this->faker->text,
            'umlsUid' => $this->faker->text,
            'preferred' => true,
            'scopeNote' => 'concept' . $this->faker->text,
            'casn1Name' => $this->faker->text,
            'registryNumber' => $this->faker->text,
            'semanticTypes' => ['1', '2'],
            'terms' => ['1', '2'],
            'descriptors' => ['abc1']
        );
        $arr[] = array(
            'id' => '2',
            'name' => 'concept' . $this->faker->text,
            'umlsUid' => $this->faker->text,
            'preferred' => false,
            'scopeNote' => 'concept' . $this->faker->text,
            'casn1Name' => $this->faker->text,
            'registryNumber' => $this->faker->text,
            'semanticTypes' => [],
            'terms' => [],
            'descriptors' => ['abc1']
        );

        return $arr;
    }

    public function create()
    {
        return array(
            'id' => '3',
            'name' => $this->faker->text,
            'umlsUid' => $this->faker->text,
            'preferred' => true,
            'scopeNote' => $this->faker->text,
            'casn1Name' => $this->faker->text,
            'registryNumber' => $this->faker->text,
            'semanticTypes' => [],
            'terms' => [],
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
