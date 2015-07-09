<?php

namespace Ilios\CoreBundle\Tests\DataLoader;

class CurriculumInventoryReportData extends AbstractDataLoader
{
    protected function getData()
    {
        $arr = array();

        $arr[] = array(
            'id' => 1,
            'year' => '1999'
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
