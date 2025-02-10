<?php

declare(strict_types=1);

namespace App\Tests\Endpoints;

use PHPUnit\Framework\Attributes\Group;
use App\Tests\Fixture\LoadAamcPcrsData;
use App\Tests\Fixture\LoadSchoolData;
use App\Tests\Fixture\LoadSessionTypeData;
use App\Tests\Fixture\LoadTermData;
use App\Tests\Fixture\LoadCompetencyData;
use App\Tests\Fixture\LoadCourseData;
use App\Tests\Fixture\LoadCourseObjectiveData;
use App\Tests\Fixture\LoadProgramYearData;
use App\Tests\Fixture\LoadProgramYearObjectiveData;
use App\Tests\Fixture\LoadSessionData;
use App\Tests\Fixture\LoadSessionObjectiveData;

/**
 * Competency API endpoint Test.
 */
#[Group('api_5')]
class CompetencyTest extends AbstractReadWriteEndpoint
{
    protected string $testName =  'competencies';

    protected function getFixtures(): array
    {
        return [
            LoadSchoolData::class,
            LoadTermData::class,
            LoadCompetencyData::class,
            LoadSessionData::class,
            LoadSessionTypeData::class,
            LoadCourseData::class,
            LoadAamcPcrsData::class,
            LoadProgramYearData::class,
            LoadSessionObjectiveData::class,
            LoadCourseObjectiveData::class,
            LoadProgramYearObjectiveData::class,
        ];
    }

    public static function putsToTest(): array
    {
        return [
            'title' => ['title', 'rhababerbarbara'],
            'school' => ['school', 3],
            'parent' => ['parent', 2],
            // 'children' => ['children', [1]], // skipped
            'aamcPcrses' => ['aamcPcrses', ['aamc-pcrs-comp-c0102']],
            'programYears' => ['programYears', [2]],
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
            'title' => [[2], ['title' => 'third competency']],
            'school' => [[0, 1, 2], ['school' => 1]],
            'schools' => [[0, 1, 2], ['schools' => [1]]],
            'parent' => [[2], ['parent' => 1]],
            // 'children' => [[0], ['children' => 3]], // skipped
            // 'aamcPcrses' => [[1], ['aamcPcrses' => ['aamc-pcrs-comp-c0101', 'aamc-pcrs-comp-c0102']]], // skipped
            // 'programYears' => [[0, 2], ['programYears' => [1]]], // skipped
            'notActive' => [[1], ['active' => false]],
            'active' => [[0, 2], ['active' => true]],
            'terms' => [[0], ['terms' => [1]]],
            'sessions' => [[0], ['sessions' => [1]]],
            'sessionTypes' => [[0], ['sessionTypes' => [1]]],
            'courses' => [[0], ['courses' => [1]]],
            'academicYear' => [[0], ['academicYears' => 2016]],
            'academicYears' => [[0], ['academicYears' => [2016]]],
        ];
    }

    public static function graphQLFiltersToTest(): array
    {
        $filters = self::filtersToTest();
        $filters['ids'] = [[1, 2], ['ids' => [2, 3]]];
        $filters['missingIds'] = [[], ['ids' => [99]]];

        return $filters;
    }

    public function testPostCompetencyProgramYear(): void
    {
        $jwt = $this->createJwtForRootUser($this->kernelBrowser);
        $dataLoader = $this->getDataLoader();
        $data = $dataLoader->create();
        $postData = $data;
        $this->relatedPostDataTest($data, $postData, $jwt, 'competencies', 'programYears');
    }

    public function testRemoveParent(): void
    {
        $jwt = $this->createJwtForRootUser($this->kernelBrowser);
        $dataLoader = $this->getDataLoader();
        $all = $dataLoader->getAll();
        $data = $all[2];
        $this->assertArrayHasKey('parent', $data);
        $this->assertEquals('1', $data['parent']);
        $postData = $data;
        unset($postData['parent']);
        $this->putTest($data, $postData, $data['id'], $jwt);
    }
}
