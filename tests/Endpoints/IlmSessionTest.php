<?php

declare(strict_types=1);

namespace App\Tests\Endpoints;

use App\Tests\DataLoader\SessionData;
use App\Tests\Fixture\LoadIlmSessionData;
use App\Tests\Fixture\LoadSessionData;
use App\Tests\ReadWriteEndpointTest;
use DateTime;
use DateTimeZone;

/**
 * IlmSession API endpoint Test.
 * @group api_3
 */
class IlmSessionTest extends ReadWriteEndpointTest
{
    protected string $testName =  'ilmSessions';

    protected function getFixtures(): array
    {
        return [
            LoadIlmSessionData::class,
            LoadSessionData::class
        ];
    }

    /**
     * @inheritDoc
     */
    public function putsToTest(): array
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
    public function readOnlyPropertiesToTest(): array
    {
        return [
            'id' => ['id', 1, 99],
        ];
    }

    /**
     * @inheritDoc
     */
    public function filtersToTest(): array
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
     */
    protected function createMany(int $count): array
    {
        $sessionDataLoader = self::getContainer()->get(SessionData::class);
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

    public function testDueDateInSystemTimeZone()
    {
        $systemTimeZone = new DateTimeZone(date_default_timezone_get());
        $now = new DateTime('now', $systemTimeZone);
        $dataLoader = $this->getDataLoader();
        $data = $dataLoader->create();
        $data['dueDate'] = $now->format('c');
        $postData = $data;
        $this->postTest($data, $postData);
    }

    public function testDueDateConvertedToSystemTimeZone()
    {
        $americaLa = new DateTimeZone('America/Los_Angeles');
        $utc = new DateTimeZone('UTC');
        $systemTimeZone = date_default_timezone_get();
        if ($systemTimeZone === 'UTC') {
            $systemTime = $utc;
            $now = new DateTime('now', $americaLa);
        } else {
            $systemTime = $americaLa;
            $now = new DateTime('now', $utc);
        }

        $dataLoader = $this->getDataLoader();
        $data = $dataLoader->create();
        $postData = $data;
        $postData['dueDate'] = $now->format('c');
        $data['dueDate'] = $now->setTimezone($systemTime)->format('c');

        $this->postTest($data, $postData);
    }
}
