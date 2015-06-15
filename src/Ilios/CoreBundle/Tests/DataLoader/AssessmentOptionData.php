<?php

namespace Ilios\CoreBundle\Tests\DataLoader;

class AssessmentOptionData extends AbstractDataLoader
{
    protected function getData()
    {
        $arr = array();

        $arr[] = array(
            'id' => 2,
            'name' => "formative",
            'sessionTypes' => [
                '144',
                '146',
                '147',
                '150',
                '152',
                '153',
                '154',
                '155',
                '156',
                '157',
                '158',
            ]
        );

        $arr[] = array(
            'id' => 1,
            'name' => "summative",
            'sessionTypes' => [
                '27',
                '134',
                '135',
                '137',
                '138',
                '139',
                '140',
                '141',
                '142',
                '143',
                '145',
                '148',
                '149',
                '151',
            ]
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
