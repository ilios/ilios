<?php

namespace Tests\IliosApiBundle\Endpoints;

use Tests\IliosApiBundle\AbstractEndpointTest;

/**
 * Course API endpoint Test.
 * @package Tests\IliosApiBundle\Endpoints
 * @group api_2
 */
class CourseTest extends AbstractEndpointTest
{
    protected $testName =  'course';

    /**
     * @inheritdoc
     */
    protected function getFixtures()
    {
        return [
            'Tests\CoreBundle\Fixture\LoadCourseData',
        ];
    }

    /**
     * @inheritDoc
     *
     * returns an array of field / value pairs to modify
     * the key for each item is reflected in the failure message
     * each one will be separately tested in a PUT request
     */
    public function putsToTest()
    {
        return [
            'title' => ['title', $this->getFaker()->text],
            'level' => ['level', $this->getFaker()->randomDigit],
            'year' => ['year', $this->getFaker()->randomDigit],
            'startDate' => ['startDate', $this->getFaker()->text],
            'endDate' => ['endDate', $this->getFaker()->text],
            'externalId' => ['externalId', $this->getFaker()->text],
            'locked' => ['locked', false],
            'archived' => ['archived', false],
            'publishedAsTbd' => ['publishedAsTbd', false],
            'published' => ['published', false],
            'clerkshipType' => ['clerkshipType', $this->getFaker()->text],
            'school' => ['school', $this->getFaker()->text],
            'directors' => ['directors', [1]],
            'administrators' => ['administrators', [1]],
            'cohorts' => ['cohorts', [1]],
            'terms' => ['terms', [1]],
            'objectives' => ['objectives', [1]],
            'meshDescriptors' => ['meshDescriptors', [1]],
            'learningMaterials' => ['learningMaterials', [1]],
            'sessions' => ['sessions', [1]],
            'ancestor' => ['ancestor', $this->getFaker()->text],
            'descendants' => ['descendants', [1]],
        ];
    }

    /**
     * @inheritDoc
     *
     * returns an array of field / value pairs that are readOnly
     * the key for each item is reflected in the failure message
     * each one will be separately tested in a PUT request
     */
    public function readOnliesToTest()
    {
        return [
            'id' => ['id', 1, 99],
        ];
    }

    /**
     * @inheritDoc
     *
     * returns an array of filters to test
     * the key for each item is reflected in the failure message
     * the first item is an array of the positions the expected items
     * can be found in the data loader
     * the second item is the filter we are testing
     */
    public function filtersToTest()
    {
        return [
            'id' => [[0], ['filters[id]' => 1]],
            'title' => [[0], ['filters[title]' => 'test']],
            'level' => [[0], ['filters[level]' => 1]],
            'year' => [[0], ['filters[year]' => 1]],
            'startDate' => [[0], ['filters[startDate]' => 'test']],
            'endDate' => [[0], ['filters[endDate]' => 'test']],
            'externalId' => [[0], ['filters[externalId]' => 'test']],
            'locked' => [[0], ['filters[locked]' => false]],
            'archived' => [[0], ['filters[archived]' => false]],
            'publishedAsTbd' => [[0], ['filters[publishedAsTbd]' => false]],
            'published' => [[0], ['filters[published]' => false]],
            'clerkshipType' => [[0], ['filters[clerkshipType]' => 'test']],
            'school' => [[0], ['filters[school]' => 'test']],
            'directors' => [[0], ['filters[directors]' => [1]]],
            'administrators' => [[0], ['filters[administrators]' => [1]]],
            'cohorts' => [[0], ['filters[cohorts]' => [1]]],
            'terms' => [[0], ['filters[terms]' => [1]]],
            'objectives' => [[0], ['filters[objectives]' => [1]]],
            'meshDescriptors' => [[0], ['filters[meshDescriptors]' => [1]]],
            'learningMaterials' => [[0], ['filters[learningMaterials]' => [1]]],
            'sessions' => [[0], ['filters[sessions]' => [1]]],
            'ancestor' => [[0], ['filters[ancestor]' => 'test']],
            'descendants' => [[0], ['filters[descendants]' => [1]]],
        ];
    }

}