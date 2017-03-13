<?php

namespace Tests\CoreBundle\DataLoader;

class MeshPreviousIndexingData extends AbstractDataLoader
{
    protected function getData()
    {
        $arr = array();
        $arr[] = array(
            'id' => 1,
            'descriptor' => 'abc1',
            'previousIndexing' => $this->faker->text,
        );
        $arr[] = array(
            'id' => 2,
            'descriptor' => 'abc2',
            'previousIndexing' => 'second previous indexing',
        );

        return $arr;
    }

    public function create()
    {
        return array(
            'id' => 3,
            'descriptor' => 'abc3',
            'previousIndexing' => $this->faker->text,
        );
    }

    public function createInvalid()
    {
        return array(
            'id' => 'bad'
        );
    }
}
