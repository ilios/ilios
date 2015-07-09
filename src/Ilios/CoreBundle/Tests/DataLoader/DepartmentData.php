<?php

namespace Ilios\CoreBundle\Tests\DataLoader;

class DepartmentData extends AbstractDataLoader
{
    protected function getData()
    {
        $arr = array();

        $arr[] = array(
            'id' => 1,
            // 'title' => "Department of Clinical Pharmacy",
            // 'school' => "3"
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
