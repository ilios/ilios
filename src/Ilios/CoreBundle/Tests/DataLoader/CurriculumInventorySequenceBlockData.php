<?php

namespace IliosCoreBundleTestsDataLoader;

class CurriculumInventorySequenceBlockData extends AbstractDataLoader
{
    protected function getData()
    {

        $arr = array();

        $arr[16] = array(
            'id' => 16,
            'academicLevel' => "81",
            'children' => ['18'            ],
            'report' => "9",
            'sessions' => []
        );

        $arr[18] = array(
            'id' => 18,
            'academicLevel' => "81",
            'parent' => "16",
            'children' => [],
            'report' => "9",
            'sessions' => []
        );

        $arr[24] = array(
            'id' => 24,
            'academicLevel' => "82",
            'children' => ['64'            ],
            'report' => "9",
            'sessions' => []
        );

        $arr[25] = array(
            'id' => 25,
            'academicLevel' => "83",
            'children' => ['33','36','53'            ],
            'report' => "9",
            'sessions' => []
        );

        $arr[26] = array(
            'id' => 26,
            'academicLevel' => "84",
            'children' => [],
            'report' => "9",
            'sessions' => []
        );

        $arr[33] = array(
            'id' => 33,
            'academicLevel' => "83",
            'parent' => "25",
            'children' => [],
            'report' => "9",
            'sessions' => []
        );

        $arr[36] = array(
            'id' => 36,
            'academicLevel' => "83",
            'parent' => "25",
            'children' => [],
            'report' => "9",
            'sessions' => []
        );

        $arr[53] = array(
            'id' => 53,
            'academicLevel' => "83",
            'parent' => "25",
            'children' => [],
            'report' => "9",
            'sessions' => []
        );

        $arr[64] = array(
            'id' => 64,
            'academicLevel' => "82",
            'parent' => "24",
            'children' => [],
            'report' => "9",
            'sessions' => []
        );

        $arr[68] = array(
            'id' => 68,
            'academicLevel' => "91",
            'children' => ['70'            ],
            'report' => "10",
            'sessions' => []
        );

        $arr[70] = array(
            'id' => 70,
            'academicLevel' => "91",
            'parent' => "68",
            'children' => [],
            'report' => "10",
            'sessions' => []
        );

        $arr[76] = array(
            'id' => 76,
            'academicLevel' => "92",
            'children' => ['78'            ],
            'report' => "10",
            'sessions' => []
        );

        $arr[78] = array(
            'id' => 78,
            'academicLevel' => "92",
            'parent' => "76",
            'children' => [],
            'report' => "10",
            'sessions' => []
        );

        $arr[82] = array(
            'id' => 82,
            'academicLevel' => "93",
            'children' => ['83','86','95'            ],
            'report' => "10",
            'sessions' => []
        );

        $arr[83] = array(
            'id' => 83,
            'academicLevel' => "93",
            'parent' => "82",
            'children' => [],
            'report' => "10",
            'sessions' => []
        );

        $arr[86] = array(
            'id' => 86,
            'academicLevel' => "93",
            'parent' => "82",
            'children' => [],
            'report' => "10",
            'sessions' => []
        );

        $arr[93] = array(
            'id' => 93,
            'academicLevel' => "94",
            'children' => [],
            'report' => "10",
            'sessions' => []
        );

        $arr[95] = array(
            'id' => 95,
            'academicLevel' => "93",
            'parent' => "82",
            'children' => [],
            'report' => "10",
            'sessions' => []
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
