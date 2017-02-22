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
            'Tests\CoreBundle\Fixture\LoadSessionData'
        ];
    }

    /**
     * @inheritDoc
     */
    public function putsToTest()
    {
        return [
            'hours' => ['hours', $this->getFaker()->randomFloat(2)],
            'session' => ['session', 1],
            'learnerGroups' => ['learnerGroups', [1]],
            'instructorGroups' => ['instructorGroups', [2, 3]],
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
            'ids' => [[1, 2], ['id' => [2, 3]]],
            'session' => [[1], ['session' => 6]],
            'hours' => [[1], ['hours' => 21.2]],
//            'learnerGroups' => [[0], ['learnerGroups' => [3]]],
//            'instructorGroups' => [[1], ['instructorGroups' => [3]]],
//            'instructors' => [[2], ['instructors' => [2]]],
//            'learners' => [[0], ['learners' => [1]]],
        ];
    }


    /**
     * We need to create additional sessions to
     * go with each new IlmSession
     * @inheritdoc
     */
    public function testPostMany()
    {
        $count = 51;
        $sessionDataLoader = $this->container->get('ilioscore.dataloader.session');
        $sessions = $sessionDataLoader->createMany($count);
        $savedSessions = $this->postMany('sessions', $sessions);

        $dataLoader = $this->getDataLoader();
        $data = [];

        foreach ($savedSessions as $i => $session) {
            $arr = $dataLoader->create();
            $arr['id'] += $i;
            $arr['session'] = $session['id'];

            $data[] = $arr;
        }

        $this->postManyTest($data);
    }
}
