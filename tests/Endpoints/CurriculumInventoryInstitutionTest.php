<?php

declare(strict_types=1);

namespace App\Tests\Endpoints;

use App\Tests\DataLoader\SchoolData;
use App\Tests\Fixture\LoadCurriculumInventoryInstitutionData;
use App\Tests\Fixture\LoadSchoolData;

/**
 * CurriculumInventoryInstitution API endpoint Test.
 */
#[\PHPUnit\Framework\Attributes\Group('api_2')]
class CurriculumInventoryInstitutionTest extends AbstractReadWriteEndpoint
{
    protected string $testName =  'curriculumInventoryInstitutions';

    protected function getFixtures(): array
    {
        return [
            LoadCurriculumInventoryInstitutionData::class,
            LoadSchoolData::class,
        ];
    }

    public static function putsToTest(): array
    {
        return [
            'name' => ['name', 'school of learning'],
            'aamcCode' => ['aamcCode', '0123456789'],
            'addressStreet' => ['addressStreet', 'alte salzstrasse'],
            'addressCity' => ['addressCity', 'halle'],
            'addressStateOrProvince' => ['addressStateOrProvince', 'KI'],
            'addressZipCode' => ['addressZipCode', '4020'],
            'addressCountryCode' => ['addressCountryCode', 'dd'],
            'school' => ['school', 3],
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
            'name' => [[1], ['name' => 'second institution']],
            'aamcCode' => [[1], ['aamcCode' => '14']],
            'addressStreet' => [[1], ['addressStreet' => '221 East']],
            'addressCity' => [[0], ['addressCity' => 'first city']],
            'addressStateOrProvince' => [[1], ['addressStateOrProvince' => 'CA']],
            'addressZipCode' => [[1], ['addressZipCode' => '90210']],
            'addressCountryCode' => [[1], ['addressCountryCode' => 'BC']],
            'school' => [[1], ['school' => 2]],
        ];
    }

    public static function graphQLFiltersToTest(): array
    {
        $filters = self::filtersToTest();
        $filters['ids'] = [[0, 1], ['ids' => [1, 2]]];

        return $filters;
    }

    /**
     * We need to create additional schools to
     * go with each new CI institution
     */
    public function testPostMany(): void
    {
        $jwt = $this->createJwtForRootUser($this->kernelBrowser);
        $data = $this->createMany(26);
        $this->postManyTest($data, $jwt);
    }

    /**
     * We need to create additional schools to
     * go with each new CI institution
     */
    public function testPostManyWithServiceToken(): void
    {
        $data = $this->createMany(26);
        $schoolIds = array_map(fn(array $newInstitution) => $newInstitution['school'], $data);
        $jwt = $this->createJwtForEnabledServiceToken($this->kernelBrowser, $schoolIds);
        $this->postManyTest($data, $jwt);
    }

    /**
     * We need to create additional schools to
     * go with each new CI institution
     */
    public function testPostManyJsonApi(): void
    {
        $jwt = $this->createJwtForRootUser($this->kernelBrowser);
        $data = $this->createMany(26);
        $jsonApiData = $this->getDataLoader()->createBulkJsonApi($data);
        $this->postManyJsonApiTest($jsonApiData, $data, $jwt);
    }

    /**
     * We need to create additional schools to
     * go with each new CI institution
     */
    public function testPostManyJsonApiWithServiceToken(): void
    {
        $data = $this->createMany(26);
        $jsonApiData = $this->getDataLoader()->createBulkJsonApi($data);
        $schoolIds = array_map(fn(array $newInstitution) => $newInstitution['school'], $data);
        $jwt = $this->createJwtForEnabledServiceToken($this->kernelBrowser, $schoolIds);
        $this->postManyJsonApiTest($jsonApiData, $data, $jwt);
    }

    protected function createMany(int $count): array
    {
        $jwt = $this->createJwtForRootUser($this->kernelBrowser);
        $schoolDataLoader = self::getContainer()->get(SchoolData::class);
        $schools = $schoolDataLoader->createMany($count);
        $savedSchools = $this->postMany('schools', 'schools', $schools, $jwt);

        $dataLoader = $this->getDataLoader();
        $id = $dataLoader->create()['id'];
        $data = [];
        foreach ($savedSchools as $school) {
            $arr = $dataLoader->create();
            $arr['school'] = (string) $school['id'];
            $arr['id'] = $id;
            $id++;

            $data[] = $arr;
        }

        return $data;
    }
}
