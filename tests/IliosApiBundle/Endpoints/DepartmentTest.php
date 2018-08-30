<?php

namespace Tests\IliosApiBundle\Endpoints;

use Tests\IliosApiBundle\ReadWriteEndpointTest;

/**
 * Department API endpoint Test.
 * @group api_2
 */
class DepartmentTest extends ReadWriteEndpointTest
{
    protected $testName =  'departments';

    /**
     * @inheritdoc
     */
    protected function getFixtures()
    {
        return [
            'Tests\AppBundle\Fixture\LoadDepartmentData',
            'Tests\AppBundle\Fixture\LoadSchoolData',
            'Tests\AppBundle\Fixture\LoadProgramYearStewardData'
        ];
    }

    /**
     * @inheritDoc
     */
    public function putsToTest()
    {
        return [
            'title' => ['title', $this->getFaker()->text(50)],
            'school' => ['school', 3],
            'stewards' => ['stewards', [2], $skipped = true],
        ];
    }

    /**
     * @inheritDoc
     */
    public function readOnlyPropertiesToTest()
    {
        return [
            'id' => ['id', 1, 99],
        ];
    }

    /**
     * @inheritDoc
     */
    public function filtersToTest()
    {
        return [
            'id' => [[0], ['id' => 1]],
            'ids' => [[0, 1], ['id' => [1, 2]]],
            'title' => [[1], ['title' => 'second department']],
            'school' => [[0, 1], ['school' => 1]],
            'stewards' => [[0], ['stewards' => [1]]],
        ];
    }
}
