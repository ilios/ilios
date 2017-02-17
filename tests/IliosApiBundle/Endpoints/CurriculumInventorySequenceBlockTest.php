<?php

namespace Tests\IliosApiBundle\Endpoints;

use Tests\IliosApiBundle\AbstractEndpointTest;

/**
 * CurriculumInventorySequenceBlock API endpoint Test.
 * @package Tests\IliosApiBundle\Endpoints
 * @group api_2
 */
class CurriculumInventorySequenceBlockTest extends AbstractEndpointTest
{
    protected $testName =  'curriculuminventorysequenceblock';

    /**
     * @inheritdoc
     */
    protected function getFixtures()
    {
        return [
            'Tests\CoreBundle\Fixture\LoadCurriculumInventorySequenceBlockData',
        ];
    }

    /**
     * @inheritDoc
     */
    public function putsToTest()
    {
        return [
            'title' => ['title', $this->getFaker()->text],
            'description' => ['description', $this->getFaker()->text],
            'required' => ['required', $this->getFaker()->randomDigit],
            'childSequenceOrder' => ['childSequenceOrder', $this->getFaker()->randomDigit],
            'orderInSequence' => ['orderInSequence', $this->getFaker()->randomDigit],
            'minimum' => ['minimum', $this->getFaker()->randomDigit],
            'maximum' => ['maximum', $this->getFaker()->randomDigit],
            'track' => ['track', false],
            'startDate' => ['startDate', $this->getFaker()->text],
            'endDate' => ['endDate', $this->getFaker()->text],
            'duration' => ['duration', $this->getFaker()->randomDigit],
            'academicLevel' => ['academicLevel', $this->getFaker()->text],
            'course' => ['course', $this->getFaker()->text],
            'parent' => ['parent', $this->getFaker()->text],
            'children' => ['children', [1]],
            'report' => ['report', $this->getFaker()->text],
            'sessions' => ['sessions', [1]],
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
            'description' => [[0], ['description' => 'test']],
            'required' => [[0], ['required' => 1]],
            'childSequenceOrder' => [[0], ['childSequenceOrder' => 1]],
            'orderInSequence' => [[0], ['orderInSequence' => 1]],
            'minimum' => [[0], ['minimum' => 1]],
            'maximum' => [[0], ['maximum' => 1]],
            'track' => [[0], ['track' => false]],
            'startDate' => [[0], ['startDate' => 'test']],
            'endDate' => [[0], ['endDate' => 'test']],
            'duration' => [[0], ['duration' => 1]],
            'academicLevel' => [[0], ['academicLevel' => 'test']],
            'course' => [[0], ['course' => 'test']],
            'parent' => [[0], ['parent' => 'test']],
            'children' => [[0], ['children' => [1]]],
            'report' => [[0], ['report' => 'test']],
            'sessions' => [[0], ['sessions' => [1]]],
        ];
    }

}