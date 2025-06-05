<?php

declare(strict_types=1);

namespace App\Tests\Endpoints;

use PHPUnit\Framework\Attributes\Group;
use App\Tests\Fixture\LoadAamcResourceTypeData;
use App\Tests\Fixture\LoadCourseData;
use App\Tests\Fixture\LoadCourseLearningMaterialData;
use App\Tests\Fixture\LoadCourseObjectiveData;
use App\Tests\Fixture\LoadIlmSessionData;
use App\Tests\Fixture\LoadLearningMaterialData;
use App\Tests\Fixture\LoadMeshDescriptorData;
use App\Tests\Fixture\LoadOfferingData;
use App\Tests\Fixture\LoadProgramYearData;
use App\Tests\Fixture\LoadProgramYearObjectiveData;
use App\Tests\Fixture\LoadSchoolData;
use App\Tests\Fixture\LoadSessionData;
use App\Tests\Fixture\LoadSessionLearningMaterialData;
use App\Tests\Fixture\LoadSessionObjectiveData;
use App\Tests\Fixture\LoadTermData;
use App\Tests\Fixture\LoadVocabularyData;

/**
 * Term API endpoint Test.
 */
#[Group('api_4')]
final class TermTest extends AbstractReadWriteEndpoint
{
    protected string $testName =  'terms';

    protected function getFixtures(): array
    {
        return [
            LoadAamcResourceTypeData::class,
            LoadVocabularyData::class,
            LoadSchoolData::class,
            LoadCourseData::class,
            LoadProgramYearData::class,
            LoadSessionData::class,
            LoadOfferingData::class,
            LoadIlmSessionData::class,
            LoadLearningMaterialData::class,
            LoadCourseLearningMaterialData::class,
            LoadSessionLearningMaterialData::class,
            LoadMeshDescriptorData::class,
            LoadTermData::class,
            LoadSessionObjectiveData::class,
            LoadCourseObjectiveData::class,
            LoadProgramYearObjectiveData::class,
        ];
    }

    public static function putsToTest(): array
    {
        return [
            'description' => ['description', 'lorem'],
            'descriptionNull' => ['description', null],
            'title' => ['title', 'ipsum'],
            'courses' => ['courses', [1]],
            'parent' => ['parent', 2],
            // 'children' => ['children', [1]], // skipped
            'programYears' => ['programYears', [1]],
            'sessions' => ['sessions', [1]],
            'vocabulary' => ['vocabulary', 2],
            // 'aamcResourceTypes' => ['aamcResourceTypes', [1]], // skipped
            'active' => ['active', false],
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
            'missingId' => [[], ['id' => 99]],
            'missingIds' => [[], ['id' => [99]]],
            'title' => [[1], ['title' => 'second term']],
            'courses' => [[0, 1, 3, 4], ['courses' => [1]]],
            'description' => [[2], ['description' => 'third description']],
            'parent' => [[1, 2], ['parent' => 1]],
            // 'children' => [[0], ['children' => [3]]], // skipped
            'programYears' => [[0, 3], ['programYears' => [2]]],
            'sessions' => [[0, 1, 3, 4], ['sessions' => [1, 2]]],
            'vocabulary' => [[3, 4, 5], ['vocabulary' => 2]],
            'aamcResourceTypes' => [[1, 2], ['aamcResourceTypes' => ['RE002']]],
            'active' => [[0, 1, 3, 4], ['active' => true]],
            'notActive' => [[2, 5], ['active' => false]],
            'sessionTypes' => [[0, 1, 2, 3, 4, 5], ['sessionTypes' => [1, 2]]],
            'instructors' => [[0, 1, 3, 4], ['instructors' => [2]]],
            'instructorGroups' => [[0, 1, 3, 4], ['instructorGroups' => [1, 2, 3]]],
            'learningMaterials' => [[0, 1, 2, 4, 5], ['learningMaterials' => [1, 2, 3]]],
            'competencies' => [[0, 1, 4], ['competencies' => [1, 2]]],
            'meshDescriptors' => [[0, 1, 2, 3, 4, 5], ['meshDescriptors' => ['abc1', 'abc2', 'abc3']]],
            'programs' => [[0, 3], ['programs' => [1]]],
            'schools' => [[0, 1, 2], ['schools' => [1]]],
            'courseObjectives' => [[0, 3], ['courseObjectives' => [1]]],
            'sessionObjectives' => [[2, 3], ['sessionObjectives' => [1]]],
            'programYearObjectives' => [[1, 3], ['programYearObjectives' => [1]]],
            'academicYear' => [[0, 2, 3, 5], ['academicYears' => 2012]],
            'academicYears' => [[0, 1, 3, 4], ['academicYears' => [2016]]],
        ];
    }

    public static function graphQLFiltersToTest(): array
    {
        $filters = self::filtersToTest();
        $filters['ids'] = [[1, 2], ['ids' => [2, 3]]];
        $filters['missingIds'] = [[], ['ids' => [99]]];

        return $filters;
    }

    public function testCreateTopLevelTerm(): void
    {
        $jwt = $this->createJwtForRootUser($this->kernelBrowser);
        $dataLoader = $this->getDataLoader();

        $postData = $dataLoader->create();
        $postData['parent'] = null;
        $this->postOne(
            'terms',
            'term',
            'terms',
            $postData,
            $jwt
        );
    }

    public function testCannotCreateTermWithEmptyTitle(): void
    {
        $jwt = $this->createJwtForRootUser($this->kernelBrowser);
        $dataLoader = $this->getDataLoader();
        $data = $dataLoader->create();
        $data['title'] = '';
        $this->badPostTest($data, $jwt);
    }

    public function testCannotCreateTermWithNoTitle(): void
    {
        $jwt = $this->createJwtForRootUser($this->kernelBrowser);
        $dataLoader = $this->getDataLoader();
        $data = $dataLoader->create();
        unset($data['title']);
        $this->badPostTest($data, $jwt);
    }

    public function testCannotSaveTermWithEmptyTitle(): void
    {
        $jwt = $this->createJwtForRootUser($this->kernelBrowser);
        $dataLoader = $this->getDataLoader();
        $data = $dataLoader->getOne();
        $data['title'] = '';
        $this->badPutTest($data, $data['id'], $jwt);
    }

    public function testCannotSaveTermWithNoTitle(): void
    {
        $jwt = $this->createJwtForRootUser($this->kernelBrowser);
        $dataLoader = $this->getDataLoader();
        $data = $dataLoader->getOne();
        unset($data['title']);
        $this->badPutTest($data, $data['id'], $jwt);
    }

    public function testFetchTermsWithAndWithoutParents(): void
    {
        $this->createGraphQLRequest(
            json_encode([
                'query' =>
                    "query { terms(ids: [1, 2]) " .
                    "{ id parent { id } }}",
            ]),
            $this->createJwtForRootUser($this->kernelBrowser)
        );
        $response = $this->kernelBrowser->getResponse();

        $this->assertGraphQLResponse($response);

        $content = json_decode($response->getContent());

        $this->assertIsObject($content->data);
        $this->assertObjectNotHasProperty('errors', $content);
        $this->assertIsArray($content->data->terms);

        $result = $content->data->terms;
        $this->assertCount(2, $result);
        $term = $result[0];
        $this->assertObjectHasProperty('id', $term);
        $this->assertSame('1', $term->id);
        $this->assertObjectHasProperty('parent', $term);
        $this->assertNull($term->parent);

        $term = $result[1];
        $this->assertObjectHasProperty('id', $term);
        $this->assertSame('2', $term->id);
        $this->assertObjectHasProperty('parent', $term);
        $this->assertNotNull($term->parent);
        $this->assertObjectHasProperty('id', $term->parent);
        $this->assertSame('1', $term->parent->id);
    }
}
