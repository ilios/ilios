<?php

namespace Tests\IliosApiBundle\Endpoints;

use Symfony\Component\HttpFoundation\Response;
use Tests\CoreBundle\DataLoader\SessionData;
use Tests\IliosApiBundle\AbstractEndpointTest;
use Tests\IliosApiBundle\EndpointTestsTrait;

/**
 * SessionType API endpoint Test.
 * @group api_3
 */
class SessionTypeTest extends AbstractEndpointTest
{
    use EndpointTestsTrait;

    protected $testName =  'sessionTypes';

    /**
     * @inheritdoc
     */
    protected function getFixtures()
    {
        return [
            'Tests\CoreBundle\Fixture\LoadLearningMaterialData',
            'Tests\CoreBundle\Fixture\LoadSessionTypeData',
            'Tests\CoreBundle\Fixture\LoadSessionLearningMaterialData',
            'Tests\CoreBundle\Fixture\LoadCourseLearningMaterialData',
            'Tests\CoreBundle\Fixture\LoadAssessmentOptionData',
            'Tests\CoreBundle\Fixture\LoadSchoolData',
            'Tests\CoreBundle\Fixture\LoadAamcMethodData',
            'Tests\CoreBundle\Fixture\LoadSessionData',
            'Tests\CoreBundle\Fixture\LoadOfferingData',
            'Tests\CoreBundle\Fixture\LoadIlmSessionData',
            'Tests\CoreBundle\Fixture\LoadCohortData',
            'Tests\CoreBundle\Fixture\LoadProgramYearData',
            'Tests\CoreBundle\Fixture\LoadProgramData',
            'Tests\CoreBundle\Fixture\LoadVocabularyData',
            'Tests\CoreBundle\Fixture\LoadTermData',
            'Tests\CoreBundle\Fixture\LoadSessionData'
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
        $sessionDataLoader = $this->container->get(SessionData::class);
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
