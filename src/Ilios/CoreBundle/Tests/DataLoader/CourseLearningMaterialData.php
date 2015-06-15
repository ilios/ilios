<?php

namespace IliosCoreBundleTestsDataLoader;

class CourseLearningMaterialData extends AbstractDataLoader
{
    protected function getData()
    {

        $arr = array();

        $arr[1247] = array(
            'id' => 1247,
            'required' => true,
            'publicNotes' => true,
            'course' => "595",
            'learningMaterial' => "26515",
            'meshDescriptors' => []
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
