<?php

namespace App\Tests\Endpoints;

use App\Tests\DataLoader\SessionData;
use App\Tests\ReadWriteEndpointTest;

/**
 * SessionDescription API endpoint Test.
 * @group api_5
 */
class SessionDescriptionTest extends ReadWriteEndpointTest
{
    protected $testName =  'sessionDescriptions';

    /**
     * @inheritdoc
     */
    protected function getFixtures()
    {
        return [
            'App\Tests\Fixture\LoadSessionDescriptionData',
            'App\Tests\Fixture\LoadSessionData'
        ];
    }

    /**
     * @inheritDoc
     */
    public function putsToTest()
    {
        return [
            'description' => ['description', $this->getFaker()->text],
            'session' => ['session', 3],
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
            'ids' => [[0, 1], ['id' => [1, 2]]],
            'session' => [[1], ['session' => 2]],
            'description' => [[1], ['description' => 'second description']],
        ];
    }


    /**
     * We need to create additional sessions to
     * go with each new SessionDescription
     * @inheritdoc
     */
    public function testPostMany()
    {
        $count = 51;
        $sessionDataLoader = $this->getContainer()->get(SessionData::class);
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
}
