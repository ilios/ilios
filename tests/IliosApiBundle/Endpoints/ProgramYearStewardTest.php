<?php

namespace Tests\IliosApiBundle\Endpoints;

use Tests\IliosApiBundle\AbstractEndpointTest;

/**
 * ProgramYearSteward API endpoint Test.
 * @package Tests\IliosApiBundle\Endpoints
 * @group api_1
 */
class ProgramYearStewardTest extends AbstractEndpointTest
{
    protected $testName =  'programyearsteward';

    /**
     * @inheritdoc
     */
    protected function getFixtures()
    {
        return [
            'Tests\CoreBundle\Fixture\LoadProgramYearStewardData',
        ];
    }

    /**
     * @inheritDoc
     */
    public function putsToTest()
    {
        return [
            'department' => ['department', $this->getFaker()->text],
            'programYear' => ['programYear', $this->getFaker()->text],
            'school' => ['school', $this->getFaker()->text],
        ];
    }

    /**
     * @inheritDoc
     */
    public function readOnliesToTest()
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
            'department' => [[0], ['department' => 'test']],
            'programYear' => [[0], ['programYear' => 'test']],
            'school' => [[0], ['school' => 'test']],
        ];
    }

}