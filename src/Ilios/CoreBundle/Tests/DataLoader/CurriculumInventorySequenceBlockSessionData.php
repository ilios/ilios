<?php

namespace Ilios\CoreBundle\Tests\DataLoader;

class CurriculumInventorySequenceBlockSessionData extends AbstractDataLoader
{
    protected function getData()
    {
        $arr = array();
        $arr[] = array(
            'id' => 1,
            'sequenceBlock' => 1
        );

        return $arr;
    }

    public function create()
    {
        return [];
    }

    public function createInvalid()
    {
        return [];
    }
}
