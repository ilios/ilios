<?php

namespace App\Tests\DataLoader;

class ProgramYearData extends AbstractDataLoader
{
    protected function getData()
    {
        $arr = array();

        $arr[] = array(
            'id' => 1,
            'startYear' =>'2013',
            'locked' => false,
            'archived' => false,
            'program' => "1",
            'cohort' => "1",
            'directors' => ['1'],
            'competencies' => ['1', '3'],
            'terms' => [],
            'objectives' => ['1', '2'],
            'stewards' => ['1', '2']
        );
        $arr[] = array(
            'id' => 2,
            'startYear' => "2014",
            'locked' => false,
            'archived' => false,
            'program' => "1",
            'cohort' => "2",
            'directors' => [],
            'competencies' => [],
            'terms' => ['1', '4'],
            'objectives' => [],
            'stewards' => []
        );
        $arr[] = array(
            'id' => 3,
            'startYear' => "2015",
            'locked' => false,
            'archived' => true,
            'program' => "2",
            'cohort' => "3",
            'directors' => [],
            'competencies' => [],
            'terms' => [],
            'objectives' => [],
            'stewards' => []
        );
        $arr[] = array(
            'id' => 4,
            'startYear' => "2016",
            'locked' => true,
            'archived' => false,
            'program' => "3",
            'cohort' => "4",
            'directors' => [],
            'competencies' => [],
            'terms' => [],
            'objectives' => [],
            'stewards' => []
        );
        $arr[] = array(
            'id' => 5,
            'startYear' =>'2016',
            'locked' => false,
            'archived' => false,
            'program' => "1",
            'cohort' => "5",
            'directors' => [],
            'competencies' => [],
            'terms' => [],
            'objectives' => ['8'],
            'stewards' => []
        );


        return $arr;
    }

    public function create()
    {
        return [
            'id' => 6,
            'startYear' => '2015',
            'program' => "1",
            'directors' => [],
            'competencies' => [],
            'terms' => [],
            'objectives' => [],
            'locked' => false,
            'archived' => false,
            'stewards' => []
        ];
    }

    public function createInvalid()
    {
        return [];
    }
}
