<?php

namespace Tests\IliosApiBundle\Endpoints;

use Tests\IliosApiBundle\AbstractEndpointTest;

/**
 * Program API endpoint Test.
 * @package Tests\IliosApiBundle\Endpoints
 * @group api_2
 */
class ProgramTest extends AbstractEndpointTest
{
    protected $testName =  'program';

    /**
     * @inheritdoc
     */
    protected function getFixtures()
    {
        return [
            'Tests\CoreBundle\Fixture\LoadProgramData',
        ];
    }

    /**
     * @inheritDoc
     */
    public function putsToTest()
    {
        return [
            'title' => ['title', $this->getFaker()->text],
            'shortTitle' => ['shortTitle', $this->getFaker()->text],
            'duration' => ['duration', $this->getFaker()->randomDigit],
            'publishedAsTbd' => ['publishedAsTbd', false],
            'published' => ['published', false],
            'school' => ['school', $this->getFaker()->text],
            'programYears' => ['programYears', [1]],
            'curriculumInventoryReports' => ['curriculumInventoryReports', [1]],
            'directors' => ['directors', [1]],
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
            'title' => [[0], ['title' => 'test']],
            'shortTitle' => [[0], ['shortTitle' => 'test']],
            'duration' => [[0], ['duration' => 1]],
            'publishedAsTbd' => [[0], ['publishedAsTbd' => false]],
            'published' => [[0], ['published' => false]],
            'school' => [[0], ['school' => 'test']],
            'programYears' => [[0], ['programYears' => [1]]],
            'curriculumInventoryReports' => [[0], ['curriculumInventoryReports' => [1]]],
            'directors' => [[0], ['directors' => [1]]],
        ];
    }

}