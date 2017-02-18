<?php

namespace Tests\IliosApiBundle\Endpoints;

use Tests\IliosApiBundle\AbstractEndpointTest;

/**
 * Competency API endpoint Test.
 * @package Tests\IliosApiBundle\Endpoints
 * @group api_2
 */
class CompetencyTest extends AbstractEndpointTest
{
    protected $testName =  'competencies';

    /**
     * @inheritdoc
     */
    protected function getFixtures()
    {
        return [
            'Tests\CoreBundle\Fixture\LoadCompetencyData',
        ];
    }

    /**
     * @inheritDoc
     */
    public function putsToTest()
    {
        return [
            'title' => ['title', $this->getFaker()->text],
            'school' => ['school', $this->getFaker()->text],
            'objectives' => ['objectives', [1]],
            'parent' => ['parent', $this->getFaker()->text],
            'children' => ['children', [1]],
            'aamcPcrses' => ['aamcPcrses', [1]],
            'programYears' => ['programYears', [1]],
            'active' => ['active', false],
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
            'school' => [[0], ['school' => 'test']],
            'objectives' => [[0], ['objectives' => [1]]],
            'parent' => [[0], ['parent' => 'test']],
            'children' => [[0], ['children' => [1]]],
            'aamcPcrses' => [[0], ['aamcPcrses' => [1]]],
            'programYears' => [[0], ['programYears' => [1]]],
            'active' => [[0], ['active' => false]],
        ];
    }

}