<?php

namespace Tests\CoreBundle\DataLoader;

class MeshTreeData extends AbstractDataLoader
{
    protected function getData()
    {
        $arr = array();
        $arr[] = array(
            'id' => '1',
            'treeNumber' => $this->faker->text(31),
            'descriptor' => 'abc1'
        );
        $arr[] = array(
            'id' => '2',
            'treeNumber' => 'tree2',
            'descriptor' => 'abc1'
        );

        return $arr;
    }

    public function create()
    {
        return array(
            'id' => '3',
            'treeNumber' => $this->faker->word,
            'descriptor' => 'abc2'
        );
    }

    public function createInvalid()
    {
        return array(
            'id' => 'bad'
        );
    }
}
