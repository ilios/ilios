<?php

namespace Tests\IliosApiBundle\Endpoints;

use Tests\CoreBundle\DataLoader\SessionData;
use Tests\IliosApiBundle\AbstractEndpointTest;
use Tests\IliosApiBundle\EndpointTestsTrait;

/**
 * IlmSession API endpoint Test.
 * @group api_3
 */
class IlmSessionTest extends AbstractEndpointTest
{
    use EndpointTestsTrait;

    protected $testName =  'ilmSessions';

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
    public function readOnlyPropertiesToTest()
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
            'sessions' => [[1, 2], ['sessions' => [6, 7]]],
            'hours' => [[1], ['hours' => 21.2]],
            'learnerGroups' => [[0], ['learnerGroups' => [3]]],
            'instructorGroups' => [[1], ['instructorGroups' => [3]]],
            'instructors' => [[2], ['instructors' => [2]]],
            'learners' => [[3], ['learners' => [2]]],
            'courses' => [[0, 1, 2, 3], ['courses' => [2]]],
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
        $sessionDataLoader = $this->container->get(SessionData::class);
        $sessions = $sessionDataLoader->createMany($count);
        $savedSessions = $this->postMany('sessions', 'sessions', $sessions);

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

    public function testDueDateInSystemTimeZone()
    {
        $systemTimeZone = new \DateTimeZone(date_default_timezone_get());
        $now = new \DateTime('now', $systemTimeZone);
        $dataLoader = $this->getDataLoader();
        $data = $dataLoader->create();
        $data['dueDate'] = $now->format('c');
        $postData = $data;
        $this->postTest($data, $postData);
    }

    public function testDueDateConvertedToSystemTimeZone()
    {
        $americaLa = new \DateTimeZone('America/Los_Angeles');
        $utc = new \DateTimeZone('UTC');
        $systemTimeZone = date_default_timezone_get();
        if ($systemTimeZone === 'UTC') {
            $systemTime = $utc;
            $now = new \DateTime('now', $americaLa);
        } else {
            $systemTime = $americaLa;
            $now = new \DateTime('now', $utc);
        }

        $dataLoader = $this->getDataLoader();
        $data = $dataLoader->create();
        $postData = $data;
        $postData['dueDate'] = $now->format('c');
        $data['dueDate'] = $now->setTimezone($systemTime)->format('c');

        $this->postTest($data, $postData);
    }
}
