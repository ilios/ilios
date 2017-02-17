<?php

namespace Tests\IliosApiBundle\Endpoints;

use Tests\IliosApiBundle\AbstractEndpointTest;

/**
 * ProgramYear API endpoint Test.
 * @package Tests\IliosApiBundle\Endpoints
 * @group api_2
 */
class ProgramYearTest extends AbstractEndpointTest
{
    protected $testName =  'programyear';

    /**
     * @inheritdoc
     */
    protected function getFixtures()
    {
        return [
            'Tests\CoreBundle\Fixture\LoadProgramYearData',
        ];
    }

    /**
     * @inheritDoc
     */
    public function putsToTest()
    {
        return [
            'startYear' => ['startYear', $this->getFaker()->text],
            'locked' => ['locked', false],
            'archived' => ['archived', false],
            'publishedAsTbd' => ['publishedAsTbd', false],
            'published' => ['published', false],
            'program' => ['program', $this->getFaker()->text],
            'cohort' => ['cohort', $this->getFaker()->text],
            'directors' => ['directors', [1]],
            'competencies' => ['competencies', [1]],
            'terms' => ['terms', [1]],
            'objectives' => ['objectives', [1]],
            'stewards' => ['stewards', [1]],
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
            'startYear' => [[0], ['startYear' => 'test']],
            'locked' => [[0], ['locked' => false]],
            'archived' => [[0], ['archived' => false]],
            'publishedAsTbd' => [[0], ['publishedAsTbd' => false]],
            'published' => [[0], ['published' => false]],
            'program' => [[0], ['program' => 'test']],
            'cohort' => [[0], ['cohort' => 'test']],
            'directors' => [[0], ['directors' => [1]]],
            'competencies' => [[0], ['competencies' => [1]]],
            'terms' => [[0], ['terms' => [1]]],
            'objectives' => [[0], ['objectives' => [1]]],
            'stewards' => [[0], ['stewards' => [1]]],
        ];
    }

}