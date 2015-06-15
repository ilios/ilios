<?php

namespace Ilios\CoreBundle\Tests\DataLoader;

class CurriculumInventorySequenceData extends AbstractDataLoader
{
    protected function getData()
    {
        $arr = array();

        $arr[] = array(
            'report' => "9"
        );

        $arr[] = array(
            'report' => "10"
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
