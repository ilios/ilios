<?php

namespace Tests\CoreBundle\DataLoader;

class MeshConceptData extends AbstractDataLoader
{
    protected function getData()
    {
        $arr = array();
        $arr[] = array(
            'id' => '1',
            'name' => 'concept' . $this->faker->text,
            'umlsUid' => $this->faker->text(9),
            'preferred' => true,
            'scopeNote' => 'scopeNote' . $this->faker->text,
            'casn1Name' => 'casn' . $this->faker->text(120),
            'registryNumber' => $this->faker->text(20),
            'semanticTypes' => ['1', '2'],
            'terms' => ['1', '2'],
            'descriptors' => ['abc1']
        );
        $arr[] = array(
            'id' => '2',
            'name' => 'concept' . $this->faker->text,
            'umlsUid' => $this->faker->text(9),
            'preferred' => true,
            'scopeNote' => 'scopeNote' . $this->faker->text,
            'casn1Name' => 'casn' . $this->faker->text(120),
            'registryNumber' => $this->faker->text(20),
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
            'name' => 'concept' . $this->faker->text,
            'umlsUid' => $this->faker->text(9),
            'preferred' => true,
            'scopeNote' => 'scopeNote' . $this->faker->text,
            'casn1Name' => 'casn' . $this->faker->text(120),
            'registryNumber' => $this->faker->text(20),
            'semanticTypes' => [],
            'terms' => ['1'],
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
