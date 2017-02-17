<?php

namespace Tests\IliosApiBundle\Endpoints;

use Tests\IliosApiBundle\AbstractEndpointTest;

/**
 * CourseClerkshipType API endpoint Test.
 * @package Tests\IliosApiBundle\Endpoints
 * @group api_1
 */
class CourseClerkshipTypeTest extends AbstractEndpointTest
{
    protected $testName =  'courseclerkshiptype';

    /**
     * @inheritdoc
     */
    protected function getFixtures()
    {
        return [
            'Tests\CoreBundle\Fixture\LoadCourseClerkshipTypeData',
        ];
    }

    /**
     * @inheritDoc
     */
    public function putsToTest()
    {
        return [
            'title' => ['title', $this->getFaker()->text],
            'courses' => ['courses', [1]],
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
            'courses' => [[0], ['courses' => [1]]],
        ];
    }

}