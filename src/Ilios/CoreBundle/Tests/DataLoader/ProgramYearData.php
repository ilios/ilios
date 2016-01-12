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
            'locked' => false,
            'archived' => false,
            'publishedAsTbd' => false,
            'published' => true,
            'program' => "1",
            'cohort' => "1",
            'directors' => [],
            'competencies' => ['1', '3'],
            'topics' => [],
            'objectives' => ['1'],
            'stewards' => ['1', '2']
        );
        $arr[] = array(
            'id' => 2,
            'startYear' => "2014",
            'locked' => false,
            'archived' => false,
            'publishedAsTbd' => false,
            'published' => false,
            'program' => "1",
            'cohort' => "2",
            'directors' => [],
            'competencies' => [],
            'topics' => ["1"],
            'objectives' => [],
            'stewards' => []
        );
        $arr[] = array(
            'id' => 3,
            'startYear' => "2014",
            'locked' => false,
            'archived' => false,
            'publishedAsTbd' => false,
            'published' => false,
            'program' => "2",
            'cohort' => "3",
            'directors' => [],
            'competencies' => [],
            'topics' => [],
            'objectives' => [],
            'stewards' => []
        );


        return $arr;
    }

    public function create()
    {
        return [
            'id' => 4,
            'startYear' => "2015",
            'program' => "1",
            'directors' => [],
            'competencies' => [],
            'topics' => [],
            'objectives' => [],
            'locked' => false,
            'archived' => false,
            'publishedAsTbd' => true,
            'published' => true,
            'stewards' => []
        ];
    }

    public function createInvalid()
    {
        return [];
    }
}
