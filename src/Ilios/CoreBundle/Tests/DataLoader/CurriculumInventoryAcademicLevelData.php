<?php

namespace IliosCoreBundleTestsDataLoader;

class CurriculumInventoryAcademicLevelData extends AbstractDataLoader
{
    protected function getData()
    {

        $arr = array();

        $arr[81] = array(
            'id' => 81,
            'report' => "9",
            'sequenceBlocks' => ['16','18'            ]
        );

        $arr[82] = array(
            'id' => 82,
            'report' => "9",
            'sequenceBlocks' => ['24','64'            ]
        );

        $arr[83] = array(
            'id' => 83,
            'report' => "9",
            'sequenceBlocks' => ['25','33','36','53'            ]
        );

        $arr[84] = array(
            'id' => 84,
            'report' => "9",
            'sequenceBlocks' => ['26'            ]
        );

        $arr[85] = array(
            'id' => 85,
            'report' => "9",
            'sequenceBlocks' => []
        );

        $arr[86] = array(
            'id' => 86,
            'report' => "9",
            'sequenceBlocks' => []
        );

        $arr[87] = array(
            'id' => 87,
            'report' => "9",
            'sequenceBlocks' => []
        );

        $arr[88] = array(
            'id' => 88,
            'report' => "9",
            'sequenceBlocks' => []
        );

        $arr[89] = array(
            'id' => 89,
            'report' => "9",
            'sequenceBlocks' => []
        );

        $arr[90] = array(
            'id' => 90,
            'report' => "9",
            'sequenceBlocks' => []
        );

        $arr[91] = array(
            'id' => 91,
            'report' => "10",
            'sequenceBlocks' => ['68','70'            ]
        );

        $arr[92] = array(
            'id' => 92,
            'report' => "10",
            'sequenceBlocks' => ['76','78'            ]
        );

        $arr[93] = array(
            'id' => 93,
            'report' => "10",
            'sequenceBlocks' => ['82','83','86','95'            ]
        );

        $arr[94] = array(
            'id' => 94,
            'report' => "10",
            'sequenceBlocks' => ['93'            ]
        );

        $arr[95] = array(
            'id' => 95,
            'report' => "10",
            'sequenceBlocks' => []
        );

        $arr[96] = array(
            'id' => 96,
            'report' => "10",
            'sequenceBlocks' => []
        );

        $arr[97] = array(
            'id' => 97,
            'report' => "10",
            'sequenceBlocks' => []
        );

        $arr[98] = array(
            'id' => 98,
            'report' => "10",
            'sequenceBlocks' => []
        );

        $arr[99] = array(
            'id' => 99,
            'report' => "10",
            'sequenceBlocks' => []
        );

        $arr[100] = array(
            'id' => 100,
            'report' => "10",
            'sequenceBlocks' => []
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
