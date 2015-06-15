<?php

namespace Ilios\CoreBundle\Tests\DataLoader;

class CourseClerkshipTypeData extends AbstractDataLoader
{
    protected function getData()
    {
        $arr = array();

        $arr[] = array(
            'id' => 1,
            'title' => "Block",
            'courses' => ['[object Object]']
        );

        $arr[] = array(
            'id' => 2,
            'title' => "Longitudinal",
            'courses' => []
        );

        $arr[] = array(
            'id' => 3,
            'title' => "Integrated",
            'courses' => []
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
