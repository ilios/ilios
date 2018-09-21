<?php

namespace App\Tests\Endpoints;

use Symfony\Component\HttpFoundation\Response;
use App\Tests\DataLoader\SessionData;
use App\Tests\ReadWriteEndpointTest;

/**
 * SessionType API endpoint Test.
 * @group api_3
 */
class SessionTypeTest extends ReadWriteEndpointTest
{
    protected $testName =  'sessionTypes';

    /**
     * @inheritdoc
     */
    protected function getFixtures()
    {
        return [
            'App\Tests\Fixture\LoadLearningMaterialData',
            'App\Tests\Fixture\LoadSessionTypeData',
            'App\Tests\Fixture\LoadSessionLearningMaterialData',
            'App\Tests\Fixture\LoadCourseLearningMaterialData',
            'App\Tests\Fixture\LoadAssessmentOptionData',
            'App\Tests\Fixture\LoadSchoolData',
            'App\Tests\Fixture\LoadAamcMethodData',
            'App\Tests\Fixture\LoadSessionData',
            'App\Tests\Fixture\LoadOfferingData',
            'App\Tests\Fixture\LoadIlmSessionData',
            'App\Tests\Fixture\LoadCohortData',
            'App\Tests\Fixture\LoadProgramYearData',
            'App\Tests\Fixture\LoadProgramData',
            'App\Tests\Fixture\LoadVocabularyData',
            'App\Tests\Fixture\LoadTermData',
            'App\Tests\Fixture\LoadSessionData'
        ];
    }

    /**
     * @inheritDoc
     */
    public function putsToTest()
    {
        return [
            'title' => ['title', $this->getFaker()->text(100)],
            'assessmentOption' => ['assessmentOption', '2'],
            'removeAssessmentOption' => ['assessmentOption', null],
            'school' => ['school', '2'],
            'aamcMethods' => ['aamcMethods', ['AM002']],
            'sessions' => ['sessions', ['1', '2' , '5', '6', '7', '8']],
            'calendarColor' => ['calendarColor', $this->getFaker()->hexColor],
            'assessment' => ['assessment', true],
            'active' => ['active', true],
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
            'title' => [[1], ['title' => 'second session type']],
            'assessmentOption' => [[1], ['assessmentOption' => '2']],
            'school' => [[0, 1], ['school' => 1]],
            'schools' => [[0, 1], ['school' => [1]]],
            'aamcMethods' => [[0, 1], ['aamcMethods' => ['AM001']], $skipped = true],
            'sessions' => [[1], ['sessions' => [2]]],
            'courses' => [[0, 1], ['courses' => [1, 2]]],
            'learningMaterials' => [[0, 1], ['learningMaterials' => [1, 2, 3]]],
            'instructors' => [[0, 1], ['instructors' => [2]]],
            'programs' => [[0, 1], ['programs' => [1, 2]]],
            'instructorGroups' => [[1], ['instructorGroups' => [2]]],
            'competencies' => [[0, 1], ['competencies' => [1]]],
            'meshDescriptors' => [[1], ['meshDescriptors' => ['abc2', 'abc3']]],
            'terms' => [[0 , 1], ['terms' => [1, 2]]],
            'calendarColor' => [[1], ['calendarColor' => '#0a1b2c']],
            'assessment' => [[1], ['assessment' => true]],
            'notAssessment' => [[0], ['assessment' => false]],
            'active' => [[1], ['active' => true]],
            'notActive' => [[0], ['active' => false]],
        ];
    }

    public function removingSessionThrowsError(array $data)
    {
        $data = $this->getDataLoader()->getOne();
        $data['sessions'] = [];
        $this->badPostTest($data, Response::HTTP_INTERNAL_SERVER_ERROR);
    }


    /**
     * We need to create additional sessions to
     * go with each new sessionType otherwise only the last one created will have any sessions
     * attached to it.
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
            $arr['sessions'] = [$session['id']];

            $data[] = $arr;
        }

        $this->postManyTest($data);
    }
}
