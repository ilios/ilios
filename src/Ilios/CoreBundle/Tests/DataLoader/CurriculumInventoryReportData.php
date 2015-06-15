<?php

namespace IliosCoreBundleTestsDataLoader;

class CurriculumInventoryReportData extends AbstractDataLoader
{
    protected function getData()
    {

        $arr = array();

        $arr[9] = array(
            'id' => 9,
            'sequence' => "9",
            'sequenceBlocks' => ['16','18','24','25','26','33','36','53','64'            ],
            'program' => "1",
            'academicLevels' => [
                '81',
                '82',
                '83',
                '84',
                '85',
                '86',
                '87',
                '88',
                '89',
                '90',
            ]
        );

        $arr[10] = array(
            'id' => 10,
            'sequence' => "10",
            'sequenceBlocks' => ['68','70','76','78','82','83','86','93','95'            ],
            'program' => "1",
            'academicLevels' => [
                '91',
                '92',
                '93',
                '94',
                '95',
                '96',
                '97',
                '98',
                '99',
                '100',
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
