<?php

namespace Tests\IliosApiBundle\Endpoints;

use Tests\IliosApiBundle\AbstractEndpointTest;

/**
 * Vocabulary API endpoint Test.
 * @package Tests\IliosApiBundle\Endpoints
 * @group api_1
 */
class VocabularyTest extends AbstractEndpointTest
{
    protected $testName =  'vocabulary';

    /**
     * @inheritdoc
     */
    protected function getFixtures()
    {
        return [
            'Tests\CoreBundle\Fixture\LoadVocabularyData',
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
            'terms' => ['terms', [1]],
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
            'terms' => [[0], ['terms' => [1]]],
            'active' => [[0], ['active' => false]],
        ];
    }

}