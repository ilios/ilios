<?php

namespace Tests\IliosApiBundle\Endpoints;

use Tests\IliosApiBundle\AbstractEndpointTest;

/**
 * Offering API endpoint Test.
 * @package Tests\IliosApiBundle\Endpoints
 * @group api_1
 */
class OfferingTest extends AbstractEndpointTest
{
    protected $testName =  'offering';

    /**
     * @inheritdoc
     */
    protected function getFixtures()
    {
        return [
            'Tests\CoreBundle\Fixture\LoadOfferingData',
        ];
    }

    /**
     * @inheritDoc
     */
    public function putsToTest()
    {
        return [
            'room' => ['room', $this->getFaker()->text],
            'site' => ['site', $this->getFaker()->text],
            'startDate' => ['startDate', $this->getFaker()->text],
            'endDate' => ['endDate', $this->getFaker()->text],
            'session' => ['session', $this->getFaker()->text],
            'learnerGroups' => ['learnerGroups', [1]],
            'instructorGroups' => ['instructorGroups', [1]],
            'learners' => ['learners', [1]],
            'instructors' => ['instructors', [1]],
        ];
    }

    /**
     * @inheritDoc
     */
    public function readOnliesToTest()
    {
        return [
            'id' => ['id', 1, 99],
            'updatedAt' => ['updatedAt', 1, 99],
        ];
    }

    /**
     * @inheritDoc
     */
    public function filtersToTest()
    {
        return [
            'id' => [[0], ['id' => 1]],
            'room' => [[0], ['room' => 'test']],
            'site' => [[0], ['site' => 'test']],
            'startDate' => [[0], ['startDate' => 'test']],
            'endDate' => [[0], ['endDate' => 'test']],
            'updatedAt' => [[0], ['updatedAt' => 'test']],
            'session' => [[0], ['session' => 'test']],
            'learnerGroups' => [[0], ['learnerGroups' => [1]]],
            'instructorGroups' => [[0], ['instructorGroups' => [1]]],
            'learners' => [[0], ['learners' => [1]]],
            'instructors' => [[0], ['instructors' => [1]]],
        ];
    }

}