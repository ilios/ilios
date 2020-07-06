<?php

declare(strict_types=1);

namespace App\Tests\Endpoints;

use App\Tests\DataLoader\CourseData;
use App\Tests\DataLoader\CourseObjectiveData;
use App\Tests\DataLoader\TermData;
use App\Tests\ReadWriteEndpointTest;
use Symfony\Component\HttpFoundation\Response;

/**
 * CourseObjectiveTest API endpoint Test.
 * @group api_1
 */
class CourseObjectiveTest extends ReadWriteEndpointTest
{
    protected $testName =  'courseObjectives';

    /**
     * @inheritdoc
     */
    protected function getFixtures()
    {
        return [
            'App\Tests\Fixture\LoadMeshDescriptorData',
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
            'title' => ['title', $this->getFaker()->text],
            'position' => ['position', $this->getFaker()->randomDigit],
            'notActive' => ['active', false],
            'course' => ['course', 1],
            'terms' => ['terms', [1, 4]],
            'meshDescriptors' => ['meshDescriptors', ['abc2']],
            'programYearObjectives' => ['programYearObjectives', [2]],
            'sessionObjectives' => ['sessionObjectives', [2, 3]]
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
            'course' => [[1, 3], ['course' => 2]],
            'terms' => [[0, 1], ['terms' => [1]]],
            'position' => [[0, 1, 2, 3, 4], ['position' => 0]],
            'title' => [[1], ['title' => 'course objective 2']],
            'active' => [[0, 1, 2, 3, 4], ['active' => 1]],
            'notActive' => [[], ['active' => 0]],
            'ancestor' => [[1], ['ancestor' => [1]]]
        ];
    }

    protected function createMany(int $n): array
    {
        $courseDataLoader = $this->getContainer()->get(CourseData::class);
        $courses = $courseDataLoader->createMany($n);
        $savedCourses = $this->postMany('courses', 'courses', $courses);

        $dataLoader = $this->getDataLoader();

        $data = [];
        for ($i = 0; $i < $n; $i++) {
            $arr = $dataLoader->create();
            $arr['id'] += $i;
            $arr['course'] = $savedCourses[$i]['id'];
            $arr['title'] = 'Course Objective ' . $arr['id'];
            $data[] = $arr;
        }

        return $data;
    }

    /**
     * @inheritdoc
     */
    public function testPostMany()
    {
        $data = $this->createMany(10);
        $this->postManyTest($data);
    }

    public function testPostManyJsonApi()
    {
        $data = $this->createMany(10);
        $jsonApiData = $this->getDataLoader()->createBulkJsonApi($data);
        $this->postManyJsonApiTest($jsonApiData, $data);
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

    /**
     * @inheritdoc
     */
    public function testPatchForAllDataJsonApi()
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
            $jsonApiData = $dataLoader->createJsonApi($data);
            $this->patchJsonApiTest($data, $jsonApiData);
        }
    }

    public function testRemoveLinksFromOrphanedObjectives()
    {
        // @todo re-implement or remove this. [ST 2020/06/22]
        $this->markTestSkipped('tbd');
//        $dataLoader = $this->getContainer()->get(ObjectiveData::class);
//        $arr = $dataLoader->create();
//        $arr['parents'] = ['1'];
//        $arr['children'] = ['7', '8'];
//        $arr['competency'] = 1;
//        $arr['programYearObjectives'] = [];
//        $arr['courseObjectives'] = [];
//        $arr['sessionObjectives'] = [];
//        unset($arr['id']);
//        $objective = $this->postOne('objectives', 'objective', 'objectives', $arr);
//        $dataLoader = $this->getContainer()->get(CourseData::class);
//        $arr = $dataLoader->create();
//        $course = $this->postOne('courses', 'course', 'courses', $arr);
//
//        $dataLoader = $this->getDataLoader();
//        $arr = $dataLoader->create();
//        $arr['course'] = $course['id'];
//        $arr['objective'] = $objective['id'];
//        unset($arr['id']);
//        $courseObjective = $this->postOne('courseobjectives', 'courseObjective', 'courseObjectives', $arr);
//
//        $this->assertNotEmpty($objective['parents'], 'parents have been created');
//        $this->assertNotEmpty($objective['children'], 'children have been created');
//        $this->assertArrayHasKey('competency', $objective);
//
//        $this->deleteTest($courseObjective['id']);
//
//        $objective = $this->getOne('objectives', 'objectives', $objective['id']);
//
//        $this->assertEmpty($objective['parents'], 'parents have been removed');
//        $this->assertEmpty($objective['children'], 'children have been removed');
//        $this->assertArrayNotHasKey('competency', $objective);
    }

    /**
     * @dataProvider inputSanitationTestProvider
     *
     * @param string $input A given objective title as un-sanitized input.
     * @param string $output The expected sanitized objective title output as returned from the server.
     *
     */
    public function testInputSanitation($input, $output)
    {
        $postData = $this->getContainer()->get(CourseObjectiveData::class)
            ->create();
        $postData['title'] = $input;
        unset($postData['id']);

        $this->createJsonRequest(
            'POST',
            $this->getUrl($this->kernelBrowser, 'app_api_courseobjectives_post', [
                'version' => $this->apiVersion
            ]),
            json_encode(['courseObjectives' => [$postData]]),
            $this->getAuthenticatedUserToken($this->kernelBrowser)
        );

        $response = $this->kernelBrowser->getResponse();

        $this->assertJsonResponse($response, Response::HTTP_CREATED);
        $this->assertEquals(
            json_decode($response->getContent(), true)['courseObjectives'][0]['title'],
            $output,
            $response->getContent()
        );
    }

    /**
     * @return array
     */
    public function inputSanitationTestProvider()
    {
        return [
            ['foo', 'foo'],
            ['<p>foo</p>', '<p>foo</p>'],
            ['<ul><li>foo</li></ul>', '<ul><li>foo</li></ul>'],
            ['<script>alert("hello");</script><p>foo</p>', '<p>foo</p>'],
            [
                '<a href="https://iliosproject.org" target="_blank">Ilios</a>',
                '<a href="https://iliosproject.org" target="_blank" rel="noreferrer noopener">Ilios</a>'
            ],
        ];
    }

    /**
     * Assert that a POST request fails if form validation fails due to input sanitation.
     */
    public function testInputSanitationFailure()
    {
        $postData = $this->getContainer()->get(CourseObjectiveData::class)
            ->create();
        // this markup will get stripped out, leaving a blank string as input.
        // which in turn will cause the form validation to fail.
        $postData['title'] = '<iframe></iframe>';
        unset($postData['id']);

        $this->createJsonRequest(
            'POST',
            $this->getUrl($this->kernelBrowser, 'app_api_courseobjectives_post', [
                'version' => $this->apiVersion
            ]),
            json_encode(['courseObjectives' => [$postData]]),
            $this->getAuthenticatedUserToken($this->kernelBrowser)
        );

        $response = $this->kernelBrowser->getResponse();
        $this->assertJsonResponse($response, Response::HTTP_BAD_REQUEST);
    }
}
