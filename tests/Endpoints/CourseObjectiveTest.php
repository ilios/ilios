<?php

declare(strict_types=1);

namespace App\Tests\Endpoints;

use App\Tests\DataLoader\CourseData;
use App\Tests\DataLoader\CourseObjectiveData;
use App\Tests\DataLoader\TermData;
use App\Tests\Fixture\LoadCourseData;
use App\Tests\Fixture\LoadCourseObjectiveData;
use App\Tests\Fixture\LoadMeshDescriptorData;
use App\Tests\Fixture\LoadProgramYearData;
use App\Tests\Fixture\LoadProgramYearObjectiveData;
use App\Tests\Fixture\LoadSessionData;
use App\Tests\Fixture\LoadSessionObjectiveData;
use App\Tests\Fixture\LoadTermData;
use App\Tests\ReadWriteEndpointTest;
use PHPUnit\Framework\Attributes\DataProvider;
use Symfony\Component\HttpFoundation\Response;

/**
 * CourseObjectiveTest API endpoint Test.
 * @group api_1
 */
class CourseObjectiveTest extends ReadWriteEndpointTest
{
    protected string $testName =  'courseObjectives';

    protected function getFixtures(): array
    {
        return [
            LoadMeshDescriptorData::class,
            LoadTermData::class,
            LoadCourseData::class,
            LoadSessionData::class,
            LoadProgramYearData::class,
            LoadSessionObjectiveData::class,
            LoadCourseObjectiveData::class,
            LoadProgramYearObjectiveData::class,
        ];
    }

    public static function putsToTest(): array
    {
        return [
            'title' => ['title', 'enough with the salt already'],
            'position' => ['position', 10],
            'notActive' => ['active', false],
            'course' => ['course', 5],
            'terms' => ['terms', [1, 4]],
            'meshDescriptors' => ['meshDescriptors', ['abc2']],
            'programYearObjectives' => ['programYearObjectives', [2]],
            'sessionObjectives' => ['sessionObjectives', [2, 3]]
        ];
    }

    public static function readOnlyPropertiesToTest(): array
    {
        return [];
    }

    public static function filtersToTest(): array
    {
        return [
            'id' => [[0], ['id' => 1]],
            'ids' => [[1, 2], ['id' => [2, 3]]],
            'course' => [[1, 3], ['course' => 2]],
            'courses' => [[1, 3], ['courses' => [2]]],
            'terms' => [[0, 1], ['terms' => [1]]],
            'position' => [[0, 1, 2, 3, 4], ['position' => 0]],
            'title' => [[1], ['title' => 'course objective 2']],
            'active' => [[0, 1, 2, 3, 4], ['active' => true]],
            'notActive' => [[], ['active' => false]],
            'ancestor' => [[1], ['ancestor' => [1]]]
        ];
    }

    public static function graphQLFiltersToTest(): array
    {
        $filters = self::filtersToTest();
        $filters['ids'] = [[1, 2], ['ids' => [2, 3]]];
        $filters['ancestor'] = [[1], ['ancestor' => 1]];

        return $filters;
    }

    protected function createMany(int $n): array
    {
        $courseDataLoader = self::getContainer()->get(CourseData::class);
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

    public function testPutForAllData()
    {
        $dataLoader = $this->getDataLoader();
        $all = $dataLoader->getAll();

        $n = count($all);
        $termsDataLoader = self::getContainer()->get(TermData::class);
        $terms = $termsDataLoader->createMany($n);
        $savedTerms = $this->postMany('terms', 'terms', $terms);

        for ($i = 0; $i < $n; $i++) {
            $data = $all[$i];
            $data['terms'][] = $savedTerms[$i]['id'];
            $this->putTest($data, $data, $data['id']);
        }
    }

    public function testPatchForAllDataJsonApi()
    {
        $dataLoader = $this->getDataLoader();
        $all = $dataLoader->getAll();

        $n = count($all);
        $termsDataLoader = self::getContainer()->get(TermData::class);
        $terms = $termsDataLoader->createMany($n);
        $savedTerms = $this->postMany('terms', 'terms', $terms);

        for ($i = 0; $i < $n; $i++) {
            $data = $all[$i];
            $data['terms'][] = $savedTerms[$i]['id'];
            $jsonApiData = $dataLoader->createJsonApi($data);
            $this->patchJsonApiTest($data, $jsonApiData);
        }
    }

    /**
     * @param string $input A given objective title as un-sanitized input.
     * @param string $output The expected sanitized objective title output as returned from the server.
     */
    #[DataProvider('inputSanitationTestProvider')]
    public function testInputSanitation($input, $output)
    {
        $postData = self::getContainer()->get(CourseObjectiveData::class)
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

    public static function inputSanitationTestProvider(): array
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
        $postData = self::getContainer()->get(CourseObjectiveData::class)
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

    public function testGraphQLIncludedData()
    {
        $loader = $this->getDataLoader();
        $data = $loader->getOne();

        $this->createGraphQLRequest(
            json_encode([
                'query' => "query { courseObjectives(id: {$data['id']}) { id, course { id } }}"
            ]),
            $this->getAuthenticatedUserToken($this->kernelBrowser)
        );
        $response = $this->kernelBrowser->getResponse();
        $this->assertGraphQLResponse($response);
        $content = json_decode($response->getContent());
        $this->assertIsObject($content->data);
        $this->assertIsArray($content->data->courseObjectives);
        $this->assertCount(1, $content->data->courseObjectives);

        $courseObjective = $content->data->courseObjectives[0];
        $this->assertTrue(property_exists($courseObjective, 'id'));
        $this->assertEquals($data['id'], $courseObjective->id);
        $this->assertTrue(property_exists($courseObjective, 'course'));
        $this->assertTrue(property_exists($courseObjective->course, 'id'));
        $this->assertEquals($data['course'], $courseObjective->course->id);
    }

    public function testPutReadOnly($key = null, $id = null, $value = null, $skipped = false)
    {
        parent::markTestSkipped('Skipped');
    }
}
