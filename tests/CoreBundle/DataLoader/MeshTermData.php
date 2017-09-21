<?php

namespace Tests\CoreBundle\DataLoader;

class MeshTermData extends AbstractDataLoader
{
    protected function getData()
    {
        $arr = array();
        $arr[] = array(
            'id' => '1',
            'meshTermUid' => 'tuid' . $this->faker->text(5),
            'name' => 'term' . $this->faker->text,
            'lexicalTag' => 'first tag',
            'conceptPreferred' => true,
            'recordPreferred' => false,
            'permuted' => true,
            'concepts' => ['1']
        );
        $arr[] = array(
            'id' => '2',
            'meshTermUid' => 'uid2',
            'name' => 'second term',
            'lexicalTag' => 'tag' . $this->faker->text(5),
            'conceptPreferred' => false,
            'recordPreferred' => true,
            'permuted' => false,
            'concepts' => ['1']
        );

        return $arr;
    }

    public function create()
    {

        return array(
            'id' => '3',
            'meshTermUid' => 'tuid123',
            'name' => $this->faker->text(192),
            'lexicalTag' => $this->faker->text(12),
            'conceptPreferred' => true,
            'recordPreferred' => true,
            'permuted' => true,
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
