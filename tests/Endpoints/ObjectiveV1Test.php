<?php

declare(strict_types=1);

namespace App\Tests\Endpoints;

use App\Tests\DataLoader\CourseData;
use App\Tests\DataLoader\CourseObjectiveData;
use App\Tests\DataLoader\ProgramYearData;
use App\Tests\DataLoader\SessionData;
use App\Tests\DataLoader\SessionObjectiveData;
use App\Tests\DataLoader\ProgramYearObjectiveData;
use App\Tests\DataLoader\ObjectiveData;
use App\Tests\ReadEndpointTest;
use Symfony\Component\HttpFoundation\Response;

/**
 * Objective V1 API endpoint Test.
 * @group api_5
 */
class ObjectiveV1Test extends ReadEndpointTest
{
    protected $testName =  'objectives';

    protected $apiVersion = 'v1';

    protected $newVersion = 'v2';

    /**
     * @var ObjectiveData $objectiveDataLoader
     */
    protected $objectiveDataLoader;

    /**
     * @var SessionData $sessionDataLoader
     */
    protected $sessionDataLoader;

    /**
     * @var CourseData $courseDataLoader
     */
    protected $courseDataLoader;

    /**
     * @var ProgramYearData $programYearDataLoader
     */
    protected $programYearDataLoader;

    /**
     * @var SessionObjectiveData $sessionObjectiveDataLoader
     */
    protected $sessionObjectiveDataLoader;

    /**
     * @var CourseObjectiveData $courseObjectiveDataLoader
     */
    protected $courseObjectiveDataLoader;

    /**
     * @var ProgramYearObjectiveData $programYearObjectiveDataLoader
     */
    protected $programYearObjectiveDataLoader;

    /**
     * @inheritdoc
     */
    protected function getFixtures()
    {
        return [
            'App\Tests\Fixture\LoadObjectiveData',
            'App\Tests\Fixture\LoadCompetencyData',
            'App\Tests\Fixture\LoadCourseData',
            'App\Tests\Fixture\LoadProgramYearData',
            'App\Tests\Fixture\LoadSessionData',
            'App\Tests\Fixture\LoadMeshDescriptorData',
            'App\Tests\Fixture\LoadSessionObjectiveData',
            'App\Tests\Fixture\LoadCourseObjectiveData',
            'App\Tests\Fixture\LoadProgramYearObjectiveData',
        ];
    }

    public function setUp(): void
    {
        parent::setUp();
        $this->objectiveDataLoader = $this->getDataLoader();
        $this->sessionDataLoader = $this->getContainer()->get(SessionData::class);
        $this->courseDataLoader = $this->getContainer()->get(CourseData::class);
        $this->programYearDataLoader = $this->getContainer()->get(ProgramYearData::class);
        $this->sessionObjectiveDataLoader = $this->getContainer()->get(SessionObjectiveData::class);
        $this->courseObjectiveDataLoader = $this->getContainer()->get(CourseObjectiveData::class);
        $this->programYearObjectiveDataLoader = $this->getContainer()->get(ProgramYearObjectiveData::class);
    }

    public function tearDown(): void
    {
        unset($this->objectiveDataLoader);
        unset($this->sessionDataLoader);
        unset($this->courseDataLoader);
        unset($this->programYearDataLoader);
        unset($this->sessionObjectiveDataLoader);
        unset($this->courseObjectiveDataLoader);
        unset($this->programYearObjectiveDataLoader);
        parent::tearDown();
    }

    /**
     * @inheritDoc
     */
    public function readOnlyPropertiesToTest()
    {
        return [];
    }

    /**
     * @inheritDoc
     */
    public function filtersToTest()
    {
        return [];
    }

    public function testGetOne()
    {
        $objectiveInSessionData = $this->objectiveDataLoader->create();
        unset($objectiveInSessionData['id']);
        $objectiveInCourseData = $this->objectiveDataLoader->create();
        unset($objectiveInCourseData['id']);
        $objectiveInProgramYearData = $this->objectiveDataLoader->create();
        unset($objectiveInProgramYearData['id']);
        $courseData1 = $this->courseDataLoader->create();
        unset($courseData1['id']);
        $courseData2 = $this->courseDataLoader->create();
        unset($courseData2['id']);
        $sessionData = $this->sessionDataLoader->create();
        unset($sessionData['id']);
        $programYearData = $this->programYearDataLoader->create();
        unset($programYearData['id']);

        $newObjectiveInSession = $this->postOne(
            'objectives',
            'objective',
            'objectives',
            $objectiveInSessionData,
            $this->newVersion
        );
        $newObjectiveInCourse = $this->postOne(
            'objectives',
            'objective',
            'objectives',
            $objectiveInCourseData,
            $this->newVersion
        );
        $newObjectiveInProgramYear = $this->postOne(
            'objectives',
            'objective',
            'objectives',
            $objectiveInProgramYearData,
            $this->newVersion
        );

        $newCourses = $this->postMany(
            'courses',
            'courses',
            [ $courseData1, $courseData2 ],
            $this->newVersion
        );
        $newSession = $this->postOne(
            'sessions',
            'session',
            'sessions',
            $sessionData,
            $this->newVersion
        );
        $newProgramYear = $this->postOne(
            'programyears',
            'programYear',
            'programYears',
            $programYearData,
            $this->newVersion
        );

        $courseObjectiveData1 = $this->courseObjectiveDataLoader->create();
        unset($courseObjectiveData1['id']);
        $courseObjectiveData1['objective'] = $newObjectiveInCourse['id'];
        $courseObjectiveData1['course'] = $newCourses[0]['id'];
        $courseObjectiveData1['position'] = 3;
        $courseObjectiveData2 = $this->courseObjectiveDataLoader->create();
        unset($courseObjectiveData2['id']);
        $courseObjectiveData2['objective'] = $newObjectiveInCourse['id'];
        $courseObjectiveData2['course'] = $newCourses[1]['id'];
        $courseObjectiveData2['position'] = 5;
        $sessionObjectiveData = $this->sessionObjectiveDataLoader->create();
        unset($sessionObjectiveData['id']);
        $sessionObjectiveData['objective'] = $newObjectiveInSession['id'];
        $sessionObjectiveData['session'] = $newSession['id'];
        $sessionObjectiveData['position'] = 3;
        $programYearObjectiveData = $this->programYearObjectiveDataLoader->create();
        unset($programYearObjectiveData['id']);
        $programYearObjectiveData['objective'] = $newObjectiveInProgramYear['id'];
        $programYearObjectiveData['programYear'] = $newProgramYear['id'];

        $this->postMany(
            'courseobjectives',
            'courseObjectives',
            [ $courseObjectiveData1, $courseObjectiveData2 ],
            $this->newVersion
        );
        $this->postOne(
            'sessionobjectives',
            'sessionObjective',
            'sessionObjectives',
            $sessionObjectiveData,
            $this->newVersion
        );
        $this->postOne(
            'programyearobjectives',
            'programYearObjective',
            'programYearObjectives',
            $programYearObjectiveData,
            $this->newVersion
        );

        $v1ObjectiveInCourse = $this->getOne('objectives', 'objectives', $newObjectiveInCourse['id']);
        $this->assertEquals($v1ObjectiveInCourse['id'], $newObjectiveInCourse['id']);
        $this->assertEquals($v1ObjectiveInCourse['title'], $newObjectiveInCourse['title']);
        $this->assertEquals($v1ObjectiveInCourse['competency'], $newObjectiveInCourse['competency']);
        $this->assertEquals($v1ObjectiveInCourse['parents'], $newObjectiveInCourse['parents']);
        $this->assertEquals($v1ObjectiveInCourse['children'], $newObjectiveInCourse['children']);
        $this->assertEquals($v1ObjectiveInCourse['descendants'], $newObjectiveInCourse['descendants']);
        $this->assertEquals($v1ObjectiveInCourse['meshDescriptors'], $newObjectiveInCourse['meshDescriptors']);
        $this->assertEquals($v1ObjectiveInCourse['active'], $newObjectiveInCourse['active']);
        $this->assertEquals($v1ObjectiveInCourse['position'], $courseObjectiveData1['position']);
        $this->assertCount(2, $v1ObjectiveInCourse['courses']);
        $this->assertEquals($v1ObjectiveInCourse['courses'][0], $newCourses[0]['id']);
        $this->assertEquals($v1ObjectiveInCourse['courses'][1], $newCourses[1]['id']);
        $this->assertEmpty($v1ObjectiveInCourse['sessions']);
        $this->assertEmpty($v1ObjectiveInCourse['programYears']);

        $v1ObjectiveInSession = $this->getOne('objectives', 'objectives', $newObjectiveInSession['id']);
        $this->assertCount(1, $v1ObjectiveInSession['sessions']);
        $this->assertEquals($v1ObjectiveInSession['sessions'][0], $newSession['id']);
        $this->assertEquals($v1ObjectiveInSession['position'], $sessionObjectiveData['position']);

        $v1ObjectiveInProgramYear = $this->getOne('objectives', 'objectives', $newObjectiveInProgramYear['id']);
        $this->assertCount(1, $v1ObjectiveInProgramYear['programYears']);
        $this->assertEquals($v1ObjectiveInProgramYear['programYears'][0], $newProgramYear['id']);
        $this->assertEquals($v1ObjectiveInProgramYear['position'], 0);
    }

    public function testGetAll()
    {
        $endpoint = $this->getPluralName();
        $responseKey = $this->getCamelCasedPluralName();
        $loader = $this->getDataLoader();
        $data = $loader->getAll();
        $this->createJsonRequest(
            'GET',
            $this->getUrl(
                $this->kernelBrowser,
                'ilios_api_getall',
                ['version' => $this->apiVersion, 'object' => $endpoint]
            ),
            null,
            $this->getAuthenticatedUserToken($this->kernelBrowser)
        );
        $response = $this->kernelBrowser->getResponse();

        $this->assertJsonResponse($response, Response::HTTP_OK);
        $responses = json_decode($response->getContent(), true)[$responseKey];

        // cutting some corners here - just making sure that entities are all returned.
        // `testGetOne()` already does a decent job in comparing entity attributes, so we're skipping this here.
        // [ST 2020/05/13]
        $this->assertCount(count($data), $responses);
    }
}
