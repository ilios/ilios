<?php

namespace Ilios\CoreBundle\Tests\DataLoader;

class CurriculumInventoryAcademicLevelData extends AbstractDataLoader
{
    protected function getData()
    {
        $arr = array();

        $arr[] = array(
            'id' => 81,
            'report' => "9",
            'sequenceBlocks' => ['16','18']
        );

        $arr[] = array(
            'id' => 82,
            'report' => "9",
            'sequenceBlocks' => ['24','64']
        );

        $arr[] = array(
            'id' => 83,
            'report' => "9",
            'sequenceBlocks' => ['25','33','36','53']
        );

        $arr[] = array(
            'id' => 84,
            'report' => "9",
            'sequenceBlocks' => ['26']
        );

        $arr[] = array(
            'id' => 85,
            'report' => "9",
            'sequenceBlocks' => []
        );

        $arr[] = array(
            'id' => 86,
            'report' => "9",
            'sequenceBlocks' => []
        );

        $arr[] = array(
            'id' => 87,
            'report' => "9",
            'sequenceBlocks' => []
        );

        $arr[] = array(
            'id' => 88,
            'report' => "9",
            'sequenceBlocks' => []
        );

        $arr[] = array(
            'id' => 89,
            'report' => "9",
            'sequenceBlocks' => []
        );

        $arr[] = array(
            'id' => 90,
            'report' => "9",
            'sequenceBlocks' => []
        );

        $arr[] = array(
            'id' => 91,
            'report' => "10",
            'sequenceBlocks' => ['68','70']
        );

        $arr[] = array(
            'id' => 92,
            'report' => "10",
            'sequenceBlocks' => ['76','78']
        );

        $arr[] = array(
            'id' => 93,
            'report' => "10",
            'sequenceBlocks' => ['82','83','86','95']
        );

        $arr[] = array(
            'id' => 94,
            'report' => "10",
            'sequenceBlocks' => ['93']
        );

        $arr[] = array(
            'id' => 95,
            'report' => "10",
            'sequenceBlocks' => []
        );

        $arr[] = array(
            'id' => 96,
            'report' => "10",
            'sequenceBlocks' => []
        );

        $arr[] = array(
            'id' => 97,
            'report' => "10",
            'sequenceBlocks' => []
        );

        $arr[] = array(
            'id' => 98,
            'report' => "10",
            'sequenceBlocks' => []
        );

        $arr[] = array(
            'id' => 99,
            'report' => "10",
            'sequenceBlocks' => []
        );

        $arr[] = array(
            'id' => 100,
            'report' => "10",
            'sequenceBlocks' => []
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
