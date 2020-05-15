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

        $v2ObjectiveInSession = $this->postOne(
            'objectives',
            'objective',
            'objectives',
            $objectiveInSessionData,
            $this->newVersion
        );
        $v2ObjectiveInCourse = $this->postOne(
            'objectives',
            'objective',
            'objectives',
            $objectiveInCourseData,
            $this->newVersion
        );
        $v2ObjectiveInProgramYear = $this->postOne(
            'objectives',
            'objective',
            'objectives',
            $objectiveInProgramYearData,
            $this->newVersion
        );

        $v2Courses = $this->postMany(
            'courses',
            'courses',
            [ $courseData1, $courseData2 ],
            $this->newVersion
        );
        $v2Session = $this->postOne(
            'sessions',
            'session',
            'sessions',
            $sessionData,
            $this->newVersion
        );
        $v2ProgramYear = $this->postOne(
            'programyears',
            'programYear',
            'programYears',
            $programYearData,
            $this->newVersion
        );

        $courseObjectiveData1 = $this->courseObjectiveDataLoader->create();
        unset($courseObjectiveData1['id']);
        $courseObjectiveData1['objective'] = $v2ObjectiveInCourse['id'];
        $courseObjectiveData1['course'] = $v2Courses[0]['id'];
        $courseObjectiveData1['position'] = 3;
        $courseObjectiveData2 = $this->courseObjectiveDataLoader->create();
        unset($courseObjectiveData2['id']);
        $courseObjectiveData2['objective'] = $v2ObjectiveInCourse['id'];
        $courseObjectiveData2['course'] = $v2Courses[1]['id'];
        $courseObjectiveData2['position'] = 5;
        $sessionObjectiveData = $this->sessionObjectiveDataLoader->create();
        unset($sessionObjectiveData['id']);
        $sessionObjectiveData['objective'] = $v2ObjectiveInSession['id'];
        $sessionObjectiveData['session'] = $v2Session['id'];
        $sessionObjectiveData['position'] = 3;
        $programYearObjectiveData = $this->programYearObjectiveDataLoader->create();
        unset($programYearObjectiveData['id']);
        $programYearObjectiveData['objective'] = $v2ObjectiveInProgramYear['id'];
        $programYearObjectiveData['programYear'] = $v2ProgramYear['id'];

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

        $v1ObjectiveInCourse = $this->getOne('objectives', 'objectives', $v2ObjectiveInCourse['id']);
        $this->assertEquals($v1ObjectiveInCourse['id'], $v2ObjectiveInCourse['id']);
        $this->assertEquals($v1ObjectiveInCourse['title'], $v2ObjectiveInCourse['title']);
        $this->assertEquals($v1ObjectiveInCourse['competency'], $v2ObjectiveInCourse['competency']);
        $this->assertEquals($v1ObjectiveInCourse['parents'], $v2ObjectiveInCourse['parents']);
        $this->assertEquals($v1ObjectiveInCourse['children'], $v2ObjectiveInCourse['children']);
        $this->assertEquals($v1ObjectiveInCourse['descendants'], $v2ObjectiveInCourse['descendants']);
        $this->assertEquals($v1ObjectiveInCourse['meshDescriptors'], $v2ObjectiveInCourse['meshDescriptors']);
        $this->assertEquals($v1ObjectiveInCourse['active'], $v2ObjectiveInCourse['active']);
        $this->assertEquals($v1ObjectiveInCourse['position'], $courseObjectiveData1['position']);
        $this->assertCount(2, $v1ObjectiveInCourse['courses']);
        $this->assertEquals($v1ObjectiveInCourse['courses'][0], $v2Courses[0]['id']);
        $this->assertEquals($v1ObjectiveInCourse['courses'][1], $v2Courses[1]['id']);
        $this->assertEmpty($v1ObjectiveInCourse['sessions']);
        $this->assertEmpty($v1ObjectiveInCourse['programYears']);

        $v2ObjectiveInSession = $this->getOne('objectives', 'objectives', $v2ObjectiveInSession['id']);
        $this->assertCount(1, $v2ObjectiveInSession['sessions']);
        $this->assertEquals($v2ObjectiveInSession['sessions'][0], $v2Session['id']);
        $this->assertEquals($v2ObjectiveInSession['position'], $sessionObjectiveData['position']);

        $v1ObjectiveInProgramYear = $this->getOne('objectives', 'objectives', $v2ObjectiveInProgramYear['id']);
        $this->assertCount(1, $v1ObjectiveInProgramYear['programYears']);
        $this->assertEquals($v1ObjectiveInProgramYear['programYears'][0], $v2ProgramYear['id']);
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
