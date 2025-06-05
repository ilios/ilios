<?php

declare(strict_types=1);

namespace App\Tests\Endpoints;

use PHPUnit\Framework\Attributes\Group;
use PHPUnit\Framework\Attributes\DataProvider;
use App\Tests\DataLoader\ProgramYearData;
use App\Tests\DataLoader\ProgramYearObjectiveData;
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
 * ProgramYearObjectiveTest API endpoint Test.
 */
#[Group('api_2')]
final class ProgramYearObjectiveTest extends AbstractReadWriteEndpoint
{
    protected string $testName =  'programYearObjectives';

    protected function getFixtures(): array
    {
        return [
            LoadMeshDescriptorData::class,
            LoadTermData::class,
            LoadCourseData::class,
            LoadSessionData::class,
            LoadSessionObjectiveData::class,
            LoadProgramYearData::class,
            LoadCourseObjectiveData::class,
            LoadProgramYearObjectiveData::class,
        ];
    }

    public static function putsToTest(): array
    {
        return [
            'title' => ['title', 'gather all the salt'],
            'position' => ['position', 9],
            'notActive' => ['active', false],
            'programYear' => ['programYear', 2],
            'terms' => ['terms', [1, 4]],
            'meshDescriptors' => ['meshDescriptors', ['abc2']],
            'competency' => ['competency', 2],
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
            'ids' => [[0, 1], ['id' => [1, 2]]],
            'missingId' => [[], ['id' => 99]],
            'missingIds' => [[], ['id' => [99]]],
            'programYear' => [[0], ['programYear' => 1]],
            'terms' => [[0, 1], ['terms' => [2]]],
            'position' => [[0, 1], ['position' => 0]],
            'title' => [[1], ['title' => 'program year objective 2']],
            'active' => [[0, 1], ['active' => true]],
            'notActive' => [[], ['active' => false]],
            'ancestor' => [[1], ['ancestor' => 1]],
            'competencies' => [[1], ['competency' => 2]],
        ];
    }

    public static function graphQLFiltersToTest(): array
    {
        $filters = self::filtersToTest();
        $filters['ids'] = [[0, 1], ['ids' => [1, 2]]];
        $filters['missingIds'] = [[], ['ids' => [99]]];

        return $filters;
    }

    protected function createMany(int $count, string $jwt): array
    {
        $programYearDataLoader = self::getContainer()->get(ProgramYearData::class);
        $programYears = $programYearDataLoader->createMany($count);
        $savedProgramYears = $this->postMany('programyears', 'programYears', $programYears, $jwt);

        $dataLoader = $this->getDataLoader();

        $data = [];
        for ($i = 0; $i < $count; $i++) {
            $arr = $dataLoader->create();
            $arr['id'] += $i;
            $arr['programYear'] = $savedProgramYears[$i]['id'];
            $arr['title'] = 'Program Year Objective ' . $arr['id'];
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

    /**
     *
     * @param string $input A given objective title as un-sanitized input.
     * @param string $output The expected sanitized objective title output as returned from the server.
     */
    #[DataProvider('inputSanitationTestProvider')]
    public function testInputSanitation(string $input, string $output): void
    {
        $postData = self::getContainer()->get(ProgramYearObjectiveData::class)
            ->create();
        $postData['title'] = $input;
        unset($postData['id']);

        $this->createJsonRequest(
            'POST',
            $this->getUrl($this->kernelBrowser, 'app_api_programyearobjectives_post', [
                'version' => $this->apiVersion,
            ]),
            json_encode(['programYearObjectives' => [$postData]]),
            $this->createJwtForRootUser($this->kernelBrowser)
        );

        $response = $this->kernelBrowser->getResponse();

        $this->assertJsonResponse($response, Response::HTTP_CREATED);
        $this->assertEquals(
            json_decode($response->getContent(), true)['programYearObjectives'][0]['title'],
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
                '<a href="https://iliosproject.org" target="_blank" rel="noreferrer noopener">Ilios</a>',
            ],
        ];
    }

    /**
     * Assert that a POST request fails if form validation fails due to input sanitation.
     */
    public function testInputSanitationFailure(): void
    {
        $postData = self::getContainer()->get(ProgramYearObjectiveData::class)
            ->create();
        // this markup will get stripped out, leaving a blank string as input.
        // which in turn will cause the form validation to fail.
        $postData['title'] = '<iframe></iframe>';
        unset($postData['id']);

        $this->createJsonRequest(
            'POST',
            $this->getUrl($this->kernelBrowser, 'app_api_programyearobjectives_post', [
                'version' => $this->apiVersion,
            ]),
            json_encode(['programYearObjectives' => [$postData]]),
            $this->createJwtForRootUser($this->kernelBrowser)
        );

        $response = $this->kernelBrowser->getResponse();
        $this->assertJsonResponse($response, Response::HTTP_BAD_REQUEST);
    }
}
