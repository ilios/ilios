<?php

namespace IliosCoreBundleTestsDataLoader;

class LearningMaterialUserRoleData extends AbstractDataLoader
{
    protected function getData()
    {

        $arr = array();

        $arr[1] = array(
            'id' => 1,
            'title' => "Author",
            'learningMaterials' => ['26390','26752'            ]
        );

        $arr[2] = array(
            'id' => 2,
            'title' => "Co-Author",
            'learningMaterials' => ['26722'            ]
        );

        $arr[3] = array(
            'id' => 3,
            'title' => "Instructional Designer",
            'learningMaterials' => [
                '26416',
                '26424',
                '26455',
                '26456',
                '26457',
                '26458',
                '26459',
                '26515',
                '26544',
                '26550',
                '26551',
                '26557',
                '26558',
                '26559',
                '26560',
                '26718',
                '26719',
                '26723',
                '26730',
                '26731',
                '26732',
                '26733',
                '26734',
                '26735',
                '26759',
                '26801',
                '26802',
                '26842',
                '26843',
                '26844',
                '26848',
                '26929',
                '26930',
                '26937',
                '26938',
                '26957',
                '26958',
                '26959',
                '26960',
                '26962',
                '26963',
                '27037',
                '27038',
                '27039',
                '27040',
                '28030',
                '28031',
                '28077',
                '28079',
                '28414',
            ]
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
