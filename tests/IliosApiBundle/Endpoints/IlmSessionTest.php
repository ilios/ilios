<?php

namespace Tests\IliosApiBundle\Endpoints;

use Tests\IliosApiBundle\AbstractEndpointTest;
use Tests\IliosApiBundle\EndpointTestsTrait;

/**
 * IlmSession API endpoint Test.
 * @package Tests\IliosApiBundle\Endpoints
 * @group api_1
 */
class IlmSessionTest extends AbstractEndpointTest
{
    use EndpointTestsTrait;

    protected $testName =  'ilmsessions';

    /**
     * @inheritdoc
     */
    protected function getFixtures()
    {
        return [
            'Tests\CoreBundle\Fixture\LoadIlmSessionData',
        ];
    }

    /**
     * @inheritDoc
     */
    public function putsToTest()
    {
        return [
            'session' => ['session', 1],
            'hours' => ['hours', $this->getFaker()->text],
            'dueDate' => ['dueDate', $this->getFaker()->iso8601],
            'learnerGroups' => ['learnerGroups', [1]],
            'instructorGroups' => ['instructorGroups', [1]],
            'instructors' => ['instructors', [1]],
            'learners' => ['learners', [1]],
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
            'session' => [[0], ['session' => 1]],
            'hours' => [[0], ['hours' => 'test']],
            'dueDate' => [[0], ['dueDate' => 'test']],
            'learnerGroups' => [[0], ['learnerGroups' => [1]]],
            'instructorGroups' => [[0], ['instructorGroups' => [1]]],
            'instructors' => [[0], ['instructors' => [1]]],
            'learners' => [[0], ['learners' => [1]]],
        ];
    }

}