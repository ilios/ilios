<?php

declare(strict_types=1);

namespace App\Tests\DataLoader;

use App\Entity\DTO\ProgramYearDTO;

class ProgramYearData extends AbstractDataLoader
{
    protected function getData()
    {
        $arr = [];

        $arr[] = [
            'id' => 1,
            'startYear' => '2013',
            'locked' => false,
            'archived' => false,
            'publishedAsTbd' => false,
            'published' => true,
            'program' => "1",
            'cohort' => "1",
            'directors' => ['1'],
            'competencies' => ['1', '3'],
            'terms' => [],
            'programYearObjectives' => ['1', '2'],
            'stewards' => ['1', '2']
        ];
        $arr[] = [
            'id' => 2,
            'startYear' => "2014",
            'locked' => false,
            'archived' => false,
            'publishedAsTbd' => true,
            'published' => true,
            'program' => "1",
            'cohort' => "2",
            'directors' => [],
            'competencies' => [],
            'terms' => ['1', '4'],
            'programYearObjectives' => [],
            'stewards' => []
        ];
        $arr[] = [
            'id' => 3,
            'startYear' => "2015",
            'locked' => false,
            'archived' => true,
            'publishedAsTbd' => false,
            'published' => false,
            'program' => "2",
            'cohort' => "3",
            'directors' => [],
            'competencies' => [],
            'terms' => [],
            'programYearObjectives' => [],
            'stewards' => []
        ];
        $arr[] = [
            'id' => 4,
            'startYear' => "2016",
            'locked' => true,
            'archived' => false,
            'publishedAsTbd' => false,
            'published' => true,
            'program' => "3",
            'cohort' => "4",
            'directors' => [],
            'competencies' => [],
            'terms' => [],
            'programYearObjectives' => [],
            'stewards' => []
        ];
        $arr[] = [
            'id' => 5,
            'startYear' => '2016',
            'locked' => false,
            'archived' => false,
            'publishedAsTbd' => false,
            'published' => true,
            'program' => "1",
            'cohort' => "5",
            'directors' => [],
            'competencies' => [],
            'terms' => [],
            'programYearObjectives' => ['3'],
            'stewards' => []
        ];

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
            'programYearObjectives' => [],
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

    public function getDtoClass(): string
    {
        return ProgramYearDTO::class;
    }
}
