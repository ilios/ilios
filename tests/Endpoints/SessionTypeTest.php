<?php

declare(strict_types=1);

namespace App\Tests\Endpoints;

use App\Tests\Fixture\LoadAamcMethodData;
use App\Tests\Fixture\LoadAssessmentOptionData;
use App\Tests\Fixture\LoadCohortData;
use App\Tests\Fixture\LoadCourseLearningMaterialData;
use App\Tests\Fixture\LoadCourseObjectiveData;
use App\Tests\Fixture\LoadIlmSessionData;
use App\Tests\Fixture\LoadLearningMaterialData;
use App\Tests\Fixture\LoadOfferingData;
use App\Tests\Fixture\LoadProgramData;
use App\Tests\Fixture\LoadProgramYearData;
use App\Tests\Fixture\LoadProgramYearObjectiveData;
use App\Tests\Fixture\LoadSchoolData;
use App\Tests\Fixture\LoadSessionData;
use App\Tests\Fixture\LoadSessionLearningMaterialData;
use App\Tests\Fixture\LoadSessionObjectiveData;
use App\Tests\Fixture\LoadSessionTypeData;
use App\Tests\Fixture\LoadTermData;
use App\Tests\Fixture\LoadVocabularyData;
use Symfony\Component\HttpFoundation\Response;
use App\Tests\DataLoader\SessionData;
use App\Tests\ReadWriteEndpointTest;

/**
 * SessionType API endpoint Test.
 * @group api_3
 */
class SessionTypeTest extends ReadWriteEndpointTest
{
    protected string $testName =  'sessionTypes';

    protected function getFixtures(): array
    {
        return [
            LoadLearningMaterialData::class,
            LoadSessionTypeData::class,
            LoadSessionLearningMaterialData::class,
            LoadCourseLearningMaterialData::class,
            LoadAssessmentOptionData::class,
            LoadSchoolData::class,
            LoadAamcMethodData::class,
            LoadSessionData::class,
            LoadOfferingData::class,
            LoadIlmSessionData::class,
            LoadCohortData::class,
            LoadProgramYearData::class,
            LoadProgramData::class,
            LoadVocabularyData::class,
            LoadTermData::class,
            LoadSessionData::class,
            LoadSessionObjectiveData::class,
            LoadCourseObjectiveData::class,
            LoadProgramYearObjectiveData::class,
        ];
    }

    /**
     * @inheritDoc
     */
    public static function putsToTest(): array
    {
        return [
            'title' => ['title', 'lorem ipsum'],
            'assessmentOption' => ['assessmentOption', '2'],
            'removeAssessmentOption' => ['assessmentOption', null],
            'school' => ['school', '2'],
            'aamcMethods' => ['aamcMethods', ['AM002']],
            'sessions' => ['sessions', ['1', '2' , '5', '6', '7', '8']],
            'calendarColor' => ['calendarColor', '#000000'],
            'assessment' => ['assessment', true],
            'active' => ['active', true],
        ];
    }

    /**
     * @inheritDoc
     */
    public static function readOnlyPropertiesToTest(): array
    {
        return [
            'id' => ['id', 1, 99],
        ];
    }

    /**
     * @inheritDoc
     */
    public static function filtersToTest(): array
    {
        return [
            'id' => [[0], ['id' => 1]],
            'ids' => [[0, 1], ['id' => [1, 2]]],
            'title' => [[1], ['title' => 'second session type']],
            'assessmentOption' => [[1], ['assessmentOption' => 2]],
            'school' => [[0, 1], ['school' => 1]],
            'schools' => [[0, 1], ['school' => [1]]],
            'aamcMethods' => [[0, 1], ['aamcMethods' => ['AM001']], $skipped = true],
            'sessions' => [[1], ['sessions' => [2]]],
            'courses' => [[0, 1], ['courses' => [1, 2]]],
            'learningMaterials' => [[0, 1], ['learningMaterials' => [1, 2, 3]]],
            'instructors' => [[0, 1], ['instructors' => [2]]],
            'programs' => [[0, 1], ['programs' => [1, 2]]],
            'instructorGroups' => [[1], ['instructorGroups' => [2]]],
            'competencies' => [[0], ['competencies' => [1]]],
            'meshDescriptors' => [[0, 1], ['meshDescriptors' => ['abc1']]],
            'terms' => [[0 , 1], ['terms' => [1, 2]]],
            'calendarColor' => [[1], ['calendarColor' => '#0a1b2c']],
            'assessment' => [[1], ['assessment' => true]],
            'notAssessment' => [[0], ['assessment' => false]],
            'active' => [[1], ['active' => true]],
            'notActive' => [[0], ['active' => false]],
        ];
    }

    public static function graphQLFiltersToTest(): array
    {
        $filters = self::filtersToTest();
        $filters['ids'] = [[0, 1], ['ids' => [1, 2]]];
        $filters['schools'] = [[0, 1], ['schools' => [1]]];

        return $filters;
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
            $arr['sessions'] = [$session['id']];

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
