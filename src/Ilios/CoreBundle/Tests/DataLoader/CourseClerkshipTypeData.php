<?php

namespace IliosCoreBundleTestsDataLoader;

class CourseClerkshipTypeData extends AbstractDataLoader
{
    protected function getData()
    {

        $arr = array();

        $arr[1] = array(
            'id' => 1,
            'title' => "Block",
            'courses' => ['[object Object]'            ]
        );

        $arr[2] = array(
            'id' => 2,
            'title' => "Longitudinal",
            'courses' => []
        );

        $arr[3] = array(
            'id' => 3,
            'title' => "Integrated",
            'courses' => []
        );

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
