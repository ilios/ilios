<?php

declare(strict_types=1);

namespace App\Tests\Endpoints;

use PHPUnit\Framework\Attributes\Group;
use PHPUnit\Framework\Attributes\DataProvider;
use App\Tests\DataLoader\SessionData;
use App\Tests\DataLoader\SessionObjectiveData;
use App\Tests\DataLoader\TermData;
use App\Tests\Fixture\LoadCourseData;
use App\Tests\Fixture\LoadCourseObjectiveData;
use App\Tests\Fixture\LoadMeshDescriptorData;
use App\Tests\Fixture\LoadProgramYearData;
use App\Tests\Fixture\LoadProgramYearObjectiveData;
use App\Tests\Fixture\LoadSessionData;
use App\Tests\Fixture\LoadSessionObjectiveData;
use App\Tests\Fixture\LoadTermData;
use Symfony\Component\HttpFoundation\Response;

/**
 * SessionObjectiveTest API endpoint Test.
 */
#[Group('api_3')]
class SessionObjectiveTest extends AbstractReadWriteEndpoint
{
    protected string $testName =  'sessionObjectives';

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
            'title' => ['title', 'lorem ipsum'],
            'position' => ['position', 12],
            'notActive' => ['active', false],
            'session' => ['session', 2],
            'terms' => ['terms', [1, 4]],
            'meshDescriptors' => ['meshDescriptors', ['abc3']],
            'courseObjectives' => ['courseObjectives', [2]],
        ];
    }

    public static function readOnlyPropertiesToTest(): array
    {
        return [
            'id' => ['id', 1, 99],
        ];
    }

    public static function filtersToTest(): array
    {
        return [
            'id' => [[0], ['id' => 1]],
            'ids' => [[1, 2], ['id' => [2, 3]]],
            'session' => [[1, 2], ['session' => 4]],
            'sessions' => [[1, 2], ['sessions' => [4]]],
            'terms' => [[0, 1], ['terms' => [3]]],
            'position' => [[0, 1, 2], ['position' => 0]],
            'courses' => [[1, 2], ['courses' => 4]],
            'title' => [[1], ['title' => 'session objective 2']],
            'active' => [[0, 1, 2], ['active' => true]],
            'notActive' => [[], ['active' => false]],
            'ancestor' => [[2], ['ancestor' => 1]],
            'school' => [[0], ['schools' => 1]],
            'schools' => [[0], ['schools' => [1]]],
        ];
    }

    public static function graphQLFiltersToTest(): array
    {
        $filters = self::filtersToTest();
        $filters['ids'] = [[1, 2], ['ids' => [2, 3]]];

        return $filters;
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
                '<a href="https://iliosproject.org" target="_blank" rel="noreferrer noopener">Ilios</a>',
            ],
        ];
    }

    /**
     *
     * @param string $input A given objective title as un-sanitized input.
     * @param string $output The expected sanitized objective title output as returned from the server.
     */
    #[DataProvider('inputSanitationTestProvider')]
    public function testInputSanitation(string $input, string $output): void
    {
        $postData = self::getContainer()->get(SessionObjectiveData::class)
            ->create();
        $postData['title'] = $input;
        unset($postData['id']);

        $this->createJsonRequest(
            'POST',
            $this->getUrl($this->kernelBrowser, 'app_api_sessionobjectives_post', [
                'version' => $this->apiVersion,
            ]),
            json_encode(['sessionObjectives' => [$postData]]),
            $this->createJwtForRootUser($this->kernelBrowser)
        );

        $response = $this->kernelBrowser->getResponse();

        $this->assertJsonResponse($response, Response::HTTP_CREATED);
        $this->assertEquals(
            json_decode($response->getContent(), true)['sessionObjectives'][0]['title'],
            $output,
            $response->getContent()
        );
    }

    /**
     * Assert that a POST request fails if form validation fails due to input sanitation.
     */
    public function testInputSanitationFailure(): void
    {
        $postData = self::getContainer()->get(SessionObjectiveData::class)
            ->create();
        // this markup will get stripped out, leaving a blank string as input.
        // which in turn will cause the form validation to fail.
        $postData['title'] = '<iframe></iframe>';
        unset($postData['id']);

        $this->createJsonRequest(
            'POST',
            $this->getUrl($this->kernelBrowser, 'app_api_sessionobjectives_post', [
                'version' => $this->apiVersion,
            ]),
            json_encode(['sessionObjectives' => [$postData]]),
            $this->createJwtForRootUser($this->kernelBrowser)
        );

        $response = $this->kernelBrowser->getResponse();
        $this->assertJsonResponse($response, Response::HTTP_BAD_REQUEST);
    }

    protected function createMany(int $count, string $jwt): array
    {
        $sessionDataLoader = self::getContainer()->get(SessionData::class);
        $sessions = $sessionDataLoader->createMany($count);
        $savedSessions = $this->postMany('sessions', 'sessions', $sessions, $jwt);

        $dataLoader = $this->getDataLoader();

        $data = [];
        for ($i = 0; $i < $count; $i++) {
            $arr = $dataLoader->create();
            $arr['id'] += $i;
            $arr['session'] = $savedSessions[$i]['id'];
            $arr['title'] = 'Session Objective ' . $arr['id'];
            $data[] = $arr;
        }

        return $data;
    }

    protected function runPostManyTest(string $jwt): void
    {
        $data = $this->createMany(10, $jwt);
        $this->postManyTest($data, $jwt);
    }

    protected function runPostManyJsonApiTest(string $jwt): void
    {
        $data = $this->createMany(10, $jwt);
        $jsonApiData = $this->getDataLoader()->createBulkJsonApi($data);
        $this->postManyJsonApiTest($jsonApiData, $data, $jwt);
    }

    protected function runPutForAllDataTest(string $jwt): void
    {
        $dataLoader = $this->getDataLoader();
        $all = $dataLoader->getAll();

        $n = count($all);
        $termsDataLoader = self::getContainer()->get(TermData::class);
        $terms = $termsDataLoader->createMany($n);
        $savedTerms = $this->postMany('terms', 'terms', $terms, $jwt);

        for ($i = 0; $i < $n; $i++) {
            $data = $all[$i];
            $data['terms'][] = $savedTerms[$i]['id'];
            $this->putTest($data, $data, $data['id'], $jwt);
        }
    }
}
