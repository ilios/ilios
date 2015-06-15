<?php

namespace Ilios\CoreBundle\Tests\DataLoader;

class CourseLearningMaterialData extends AbstractDataLoader
{
    protected function getData()
    {
        $arr = array();

        $arr[] = array(
            'id' => 1247,
            'required' => true,
            'publicNotes' => true,
            'course' => "595",
            'learningMaterial' => "26515",
            'meshDescriptors' => []
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
