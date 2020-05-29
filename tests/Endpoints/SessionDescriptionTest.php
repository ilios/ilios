<?php

declare(strict_types=1);

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
     */
    protected function createMany(int $count): array
    {
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

        return $data;
    }

    public function testPostMany()
    {
        $data = $this->createMany(51);
        $this->postManyTest($data);
    }

    public function testPostManyJsonApi()
    {
        $data = $this->createMany(10);
        $jsonApiData = $this->getDataLoader()->createBulkJsonApi($data);
        $this->postManyJsonApiTest($jsonApiData, $data);
    }
}
