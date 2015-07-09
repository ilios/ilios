<?php

namespace Ilios\CoreBundle\Tests\DataLoader;

class ProgramYearData extends AbstractDataLoader
{
    protected function getData()
    {
        $arr = array();

        $arr[] = array(
            'id' => 1,
            'startYear' => "2013",
            'deleted' => false,
            'locked' => false,
            'archived' => false,
            'publishedAsTbd' => false,
            'program' => "1",
            'cohort' => "1",
            'directors' => [],
            'competencies' => [],
            'disciplines' => [],
            'objectives' => [],
            'publishEvent' => '1'
        );
        $arr[] = array(
            'id' => 2,
            'startYear' => "2014",
            'deleted' => false,
            'locked' => false,
            'archived' => false,
            'publishedAsTbd' => false,
            'program' => "1",
            'cohort' => "2",
            'directors' => [],
            'competencies' => [],
            'disciplines' => [],
            'objectives' => [],
        );


        return $arr;
    }

    public function create()
    {
        return [
            'id' => 3,
            'startYear' => "2015",
            'program' => "1",
            'directors' => [],
            'competencies' => [],
            'disciplines' => [],
            'objectives' => [],
            'deleted' => true,
            'locked' => true,
            'archived' => true,
            'publishedAsTbd' => false
        ];
    }

    public function createInvalid()
    {
        return [];
    }
}
