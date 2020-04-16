<?php

declare(strict_types=1);

namespace App\Tests\DataLoader;

class ObjectiveData extends AbstractDataLoader
{
    protected function getData()
    {
        $arr = array();

        $arr[] = array(
            'id' => 1,
            'title' => 'first objective',
            'position' => 0,
            'active' => true,
            'competency' => '3',
            'courseObjectives' => [],
            'programYearObjectives' => ['1'],
            'sessionObjectives' => [],
            'parents' => [],
            'children' => ['2'],
            'meshDescriptors' => [],
            'descendants' => ['8']
        );

        $arr[] = array(
            'id' => 2,
            'title' => 'second objective',
            'position' => 0,
            'active' => true,
            'courseObjectives' => ['1', '2', '3'],
            'programYearObjectives' => ['2'],
            'sessionObjectives' => [],
            'parents' => ['1'],
            'children' => ['3', '6'],
            'meshDescriptors' => ['abc1'],
            'descendants' => ['3']
        );

        $arr[] = array(
            'id' => 3,
            'title' => 'third objective',
            'position' => 0,
            'active' => true,
            'courseObjectives' => [],
            'programYearObjectives' => [],
            'sessionObjectives' => ['1'],
            'parents' => ['2'],
            'children' => [],
            'meshDescriptors' => [],
            'ancestor' => '2',
            'descendants' => []
        );

        $arr[] = array(
            'id' => 4,
            'title' => 'fourth objective',
            'position' => 0,
            'active' => true,
            'courseObjectives' => ['4'],
            'programYearObjectives' => [],
            'sessionObjectives' => [],
            'parents' => [],
            'children' => [],
            'meshDescriptors' => [],
            'descendants' => []
        );

        $arr[] = array(
            'id' => 5,
            'title' => 'fifth objective',
            'position' => 0,
            'active' => false,
            'courseObjectives' => ['5'],
            'programYearObjectives' => [],
            'sessionObjectives' => [],
            'parents' => [],
            'children' => [],
            'meshDescriptors' => ["abc1"],
            'descendants' => []
        );

        $arr[] = array(
            'id' => 6,
            'title' => 'sixth objective',
            'position' => 0,
            'active' => true,
            'courseObjectives' => [],
            'programYearObjectives' => [],
            'sessionObjectives' => ['2'],
            'parents' => ['2'],
            'children' => [],
            'meshDescriptors' => ["abc1"],
            'descendants' => ['7']
        );

        $arr[] = array(
            'id' => 7,
            'title' => 'seventh objective',
            'position' => 0,
            'active' => false,
            'courseObjectives' => [],
            'programYearObjectives' => [],
            'sessionObjectives' => ['3'],
            'parents' => [],
            'children' => [],
            'meshDescriptors' => ["abc3"],
            'ancestor' => '6',
            'descendants' => []
        );
        $arr[] = array(
            'id' => 8,
            'title' => 'eighth objective',
            'position' => 0,
            'active' => true,
            'courseObjectives' => [],
            'programYearObjectives' => ['3'],
            'sessionObjectives' => [],
            'parents' => [],
            'children' => [],
            'meshDescriptors' => [],
            'ancestor' => '1',
            'descendants' => []
        );

        // unlinked objectives

        // link to this from new program-year objective
        $arr[] = array(
            'id' => 9,
            'title' => 'ninth objective',
            'position' => 0,
            'active' => true,
            'courseObjectives' => [],
            'programYearObjectives' => [],
            'sessionObjectives' => [],
            'parents' => [],
            'children' => ['10'],
            'meshDescriptors' => [],
            'descendants' => []
        );

        // link to this from new course objective
        $arr[] = array(
            'id' => 10,
            'title' => 'tenth objective',
            'position' => 0,
            'active' => true,
            'courseObjectives' => [],
            'programYearObjectives' => [],
            'sessionObjectives' => [],
            'parents' => ['9'],
            'children' => ['11'],
            'meshDescriptors' => [],
            'descendants' => []
        );

        // link to this from new session objective
        $arr[] = array(
            'id' => 11,
            'title' => 'eleventh objective',
            'position' => 0,
            'active' => true,
            'courseObjectives' => [],
            'programYearObjectives' => [],
            'sessionObjectives' => [],
            'parents' => ['10'],
            'children' => [],
            'meshDescriptors' => [],
            'descendants' => []
        );

        return $arr;
    }

    public function create()
    {
        return array(
            'id' => 12,
            'title' => $this->faker->text,
            'position' => 0,
            'active' => true,
            'competency' => "1",
            'courseObjectives' => [],
            'programYearObjectives' => [],
            'sessionObjectives' => [],
            'parents' => ['1'],
            'children' => [],
            'meshDescriptors' => [],
            'descendants' => []
        );
    }

    public function createInvalid()
    {
        return [];
    }
}
