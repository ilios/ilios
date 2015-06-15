<?php

namespace IliosCoreBundleTestsDataLoader;

class CurriculumInventorySequenceData extends AbstractDataLoader
{
    protected function getData()
    {

        $arr = array();

        $arr[undefined] = array(
            'report' => "9"
        );

        $arr[undefined] = array(
            'report' => "10"
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
