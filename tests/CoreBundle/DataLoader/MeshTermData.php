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
            'lexicalTag' => 'tag' . $this->faker->text(5),
            'conceptPreferred' => true,
            'recordPreferred' => true,
            'permuted' => true,
            'printable' => true,
            'concepts' => ['1']
        );
        $arr[] = array(
            'id' => '2',
            'meshTermUid' => 'tuid' . $this->faker->text(5),
            'name' => 'term' . $this->faker->text,
            'lexicalTag' => 'tag' . $this->faker->text(5),
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
            'meshTermUid' => 'tuid123',
            'name' => 'term' . $this->faker->word,
            'lexicalTag' => 'tag' . $this->faker->word(5),
            'conceptPreferred' => true,
            'recordPreferred' => true,
            'permuted' => true,
            'printable' => true,
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
