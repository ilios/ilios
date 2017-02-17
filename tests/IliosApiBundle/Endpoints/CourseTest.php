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
            'level' => [[0], ['level' => 1]],
            'year' => [[0], ['year' => 1]],
            'startDate' => [[0], ['startDate' => 'test']],
            'endDate' => [[0], ['endDate' => 'test']],
            'externalId' => [[0], ['externalId' => 'test']],
            'locked' => [[0], ['locked' => false]],
            'archived' => [[0], ['archived' => false]],
            'publishedAsTbd' => [[0], ['publishedAsTbd' => false]],
            'published' => [[0], ['published' => false]],
            'clerkshipType' => [[0], ['clerkshipType' => 'test']],
            'school' => [[0], ['school' => 'test']],
            'directors' => [[0], ['directors' => [1]]],
            'administrators' => [[0], ['administrators' => [1]]],
            'cohorts' => [[0], ['cohorts' => [1]]],
            'terms' => [[0], ['terms' => [1]]],
            'objectives' => [[0], ['objectives' => [1]]],
            'meshDescriptors' => [[0], ['meshDescriptors' => [1]]],
            'learningMaterials' => [[0], ['learningMaterials' => [1]]],
            'sessions' => [[0], ['sessions' => [1]]],
            'ancestor' => [[0], ['ancestor' => 'test']],
            'descendants' => [[0], ['descendants' => [1]]],
        ];
    }

}