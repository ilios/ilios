<?php

namespace Ilios\CoreBundle\Tests\DataLoader;

class MeshTreeData extends AbstractDataLoader
{
    protected function getData()
    {
        $arr = array();
        $arr[] = array(
            'treeNumber' => '1',
            'descriptor' => 'abc1'
        );
        $arr[] = array(
            'treeNumber' => '2',
            'descriptor' => 'abc1'
        );

        return $arr;
    }

    public function create()
    {
        return array(
            'treeNumber' => '3',
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
