<?php

declare(strict_types=1);

namespace App\Tests\Endpoints;

use App\Tests\DataLoader\ObjectiveData;
use App\Tests\DataLoader\SessionData;
use App\Tests\DataLoader\TermData;
use App\Tests\ReadWriteEndpointTest;

/**
 * SessionObjectiveTest API endpoint Test.
 * @group api_1
 */
class SessionObjectiveTest extends ReadWriteEndpointTest
{
    protected $testName =  'sessionObjectives';

    /**
     * @inheritdoc
     */
    protected function getFixtures()
    {
        return [
            'App\Tests\Fixture\LoadObjectiveData',
            'App\Tests\Fixture\LoadTermData',
            'App\Tests\Fixture\LoadCourseData',
            'App\Tests\Fixture\LoadSessionData',
            'App\Tests\Fixture\LoadProgramYearData',
            'App\Tests\Fixture\LoadSessionObjectiveData',
            'App\Tests\Fixture\LoadCourseObjectiveData',
            'App\Tests\Fixture\LoadProgramYearObjectiveData',
        ];
    }

    /**
     * @inheritdoc
     */
    public function putsToTest()
    {
        return [
            'session' => ['session', 2],
            'objective' => ['objective', 2],
            'terms' => ['terms', [1, 4]],
        ];
    }

    /**
     * @inheritdoc
     */
    public function readOnlyPropertiesToTest()
    {
        return [];
    }

    /**
     * @inheritdoc
     */
    public function filtersToTest()
    {
        return [
            'id' => [[0], ['id' => 1]],
            'ids' => [[1, 2], ['id' => [2, 3]]],
            'session' => [[1, 2], ['session' => 4]],
            'objective' => [[2], ['objective' => 7]],
            'terms' => [[0, 1], ['terms' => [3]]],
            'position' => [[0, 1, 2], ['position' => 0]],
            'courses' => [[1, 2], ['courses' => 4]],
        ];
    }

    /**
     * @inheritdoc
     */
    public function testPostMany()
    {
        $n = 10;
        $objectiveDataLoader = $this->getContainer()->get(ObjectiveData::class);
        $objectives = $objectiveDataLoader->createMany($n);
        $savedObjectives = $this->postMany('objectives', 'objectives', $objectives);

        $sessionDataLoader = $this->getContainer()->get(SessionData::class);
        $sessions = $sessionDataLoader->createMany($n);
        $savedSessions = $this->postMany('sessions', 'sessions', $sessions);

        $dataLoader = $this->getDataLoader();

        $data = [];
        for ($i = 0; $i < $n; $i++) {
            $arr = $dataLoader->create();
            $arr['id'] += $i;
            $arr['session'] = $savedSessions[$i]['id'];
            $arr['objective'] = $savedObjectives[$i]['id'];
            $data[] = $arr;
        }
        $this->postManyTest($data);
    }

    /**
     * @inheritdoc
     */
    public function testPutForAllData()
    {
        $dataLoader = $this->getDataLoader();
        $all = $dataLoader->getAll();

        $n = count($all);
        $termsDataLoader = $this->getContainer()->get(TermData::class);
        $terms = $termsDataLoader->createMany($n);
        $savedTerms = $this->postMany('terms', 'terms', $terms);

        for ($i = 0; $i < $n; $i++) {
            $data = $all[$i];
            $data['terms'][] = $savedTerms[$i]['id'];
            $this->putTest($data, $data, $data['id']);
        }
    }

    public function testRemoveLinksFromOrphanedObjectives()
    {
        $dataLoader = $this->getContainer()->get(ObjectiveData::class);
        $arr = $dataLoader->create();
        $arr['parents'] = ['1'];
        $arr['children'] = ['7', '8'];
        $arr['competency'] = 1;
        $arr['programYearObjectives'] = [];
        $arr['courseObjectives'] = [];
        $arr['sessionObjectives'] = [];
        unset($arr['id']);
        $objective = $this->postOne('objectives', 'objective', 'objectives', $arr);
        $dataLoader = $this->getContainer()->get(SessionData::class);
        $arr = $dataLoader->create();
        $session = $this->postOne('sessions', 'session', 'sessions', $arr);

        $dataLoader = $this->getDataLoader();
        $arr = $dataLoader->create();
        $arr['session'] = $session['id'];
        $arr['objective'] = $objective['id'];
        unset($arr['id']);
        $sessionObjective = $this->postOne('sessionobjectives', 'sessionObjective', 'sessionObjectives', $arr);

        $this->assertNotEmpty($objective['parents'], 'parents have been created');
        $this->assertNotEmpty($objective['children'], 'children have been created');
        $this->assertArrayHasKey('competency', $objective);

        $this->deleteTest($sessionObjective['id']);

        $objective = $this->getOne('objectives', 'objectives', $objective['id']);

        $this->assertEmpty($objective['parents'], 'parents have been removed');
        $this->assertEmpty($objective['children'], 'children have been removed');
        $this->assertArrayNotHasKey('competency', $objective);
    }
}
