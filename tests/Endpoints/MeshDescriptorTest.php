<?php

declare(strict_types=1);

namespace App\Tests\Endpoints;

use PHPUnit\Framework\Attributes\Group;
use App\Tests\Fixture\LoadCourseData;
use App\Tests\Fixture\LoadCourseLearningMaterialData;
use App\Tests\Fixture\LoadCourseObjectiveData;
use App\Tests\Fixture\LoadMeshConceptData;
use App\Tests\Fixture\LoadMeshDescriptorData;
use App\Tests\Fixture\LoadMeshPreviousIndexingData;
use App\Tests\Fixture\LoadMeshQualifierData;
use App\Tests\Fixture\LoadMeshTermData;
use App\Tests\Fixture\LoadMeshTreeData;
use App\Tests\Fixture\LoadProgramYearObjectiveData;
use App\Tests\Fixture\LoadSessionData;
use App\Tests\Fixture\LoadSessionLearningMaterialData;
use App\Tests\Fixture\LoadSessionObjectiveData;
use App\Tests\QEndpointTrait;

/**
 * MeshDescriptor API endpoint Test.
 */
#[Group('api_3')]
class MeshDescriptorTest extends AbstractMeshEndpoint
{
    use QEndpointTrait;

    protected string $testName =  'meshDescriptors';

    protected function getFixtures(): array
    {
        return [
            LoadMeshDescriptorData::class,
            LoadCourseData::class,
            LoadSessionData::class,
            LoadMeshConceptData::class,
            LoadMeshTreeData::class,
            LoadMeshPreviousIndexingData::class,
            LoadMeshQualifierData::class,
            LoadMeshTermData::class,
            LoadSessionLearningMaterialData::class,
            LoadCourseLearningMaterialData::class,
            LoadCourseObjectiveData::class,
            LoadSessionObjectiveData::class,
            LoadProgramYearObjectiveData::class,
        ];
    }

    public static function filtersToTest(): array
    {
        return [
            'id' => [[0], ['id' => 'abc1']],
            'ids' => [[1, 2], ['id' => ['abc2', 'abc3']]],
            'missingId' => [[], ['id' => 'nothing']],
            'missingIds' => [[], ['id' => ['nothing']]],
            'name' => [[2], ['name' => 'desc3']],
            'annotation' => [[0], ['annotation' => 'annotation1']],
            'courses' => [[0, 1], ['courses' => [2, 3]]],
            'sessions' => [[1], ['sessions' => [3]]],
            // 'concepts' => [[0], ['concepts' => [1]]], // skipped
            // 'qualifiers' => [[0], ['qualifiers' => [1]]], // skipped
            // 'trees' => [[0], ['trees' => [1]]], // skipped
            // 'sessionLearningMaterials' => [[0], ['sessionLearningMaterials' => [1]]], // skipped
            // 'courseLearningMaterials' => [[0], ['courseLearningMaterials' => [1]]], // skipped
            'learningMaterials' => [[0], ['learningMaterials' => [1, 2]]],
            // 'previousIndexing' => [[1], ['previousIndexing' => 2]], // skipped
            'terms' => [[0, 1, 2], ['terms' => [1, 2, 3]]],
            'sessionTypes' => [[0, 1, 2], ['sessionTypes' => [2]]],
            'school' => [[0, 2], ['schools' => 2]],
            'schools' => [[0, 2], ['schools' => [2]]],
            'schoolsAndCourses' => [[0], ['schools' => [2], 'courses' => [2]]],
        ];
    }

    public static function graphQLFiltersToTest(): array
    {
        $filters = self::filtersToTest();
        $filters['ids'] = [[1, 2], ['ids' => ['abc2', 'abc3']]];
        $filters['missingIds'] = [[], ['ids' => ['nothing']]];
        unset($filters['school']);

        return $filters;
    }

    protected function getTimeStampFields(): array
    {
        return ['updatedAt', 'createdAt'];
    }

    public static function qsToTest(): array
    {
        return [
            ['abc1', [0]],
            ['desc2', [1]],
            ['desc', [0, 1, 2]],
            ['annotation2', [1]],
            ['second previous indexing', [1]],
            ['second term', [0]],
            ['second concept', [0]],
            ['first scopeNote', [0]],
            ['first casn', [0]],
        ];
    }

    /**
     * Ensure offset and limit work
     */
    public function testFindByQWithLimit(): void
    {
        $jwt = $this->createJwtForRootUser($this->kernelBrowser);
        $dataLoader = $this->getDataLoader();
        $all = $dataLoader->getAll();
        $filters = ['q' => $all[0]['name'], 'limit' => 1];
        $this->filterTest($filters, [$all[0]], $jwt);
        $filters = ['q' => 'desc', 'limit' => 2];
        $this->filterTest($filters, [$all[0], $all[1]], $jwt);
    }

    /**
     * Ensure offset and limit work
     */
    public function testFindByQWithOffset(): void
    {
        $jwt = $this->createJwtForRootUser($this->kernelBrowser);
        $dataLoader = $this->getDataLoader();
        $all = $dataLoader->getAll();
        $filters = ['q' => $all[0]['name'], 'offset' => 0];
        $this->filterTest($filters, [$all[0]], $jwt);
    }

    /**
     * Ensure offset and limit work
     */
    public function testFindByQWithOffsetAndLimit(): void
    {
        $jwt = $this->createJwtForRootUser($this->kernelBrowser);
        $dataLoader = $this->getDataLoader();
        $all = $dataLoader->getAll();
        $filters = ['q' => $all[0]['name'], 'offset' => 0, 'limit' => 1];
        $this->filterTest($filters, [$all[0]], $jwt);
        $filters = ['q' => 'desc', 'offset' => 1, 'limit' => 1];
        $this->filterTest($filters, [$all[1]], $jwt);
    }

    public function testFindByQWithOffsetAndLimitJsonApi(): void
    {
        $jwt = $this->createJwtForRootUser($this->kernelBrowser);
        $dataLoader = $this->getDataLoader();
        $all = $dataLoader->getAll();
        $filters = ['q' => $all[0]['name'], 'offset' => 0, 'limit' => 1];
        $this->filterTest($filters, [$all[0]], $jwt);
        $filters = ['q' => 'desc', 'offset' => 1, 'limit' => 1];
        $this->jsonApiFilterTest($filters, [$all[1]], $jwt);
    }
}
