<?php

declare(strict_types=1);

namespace App\Tests\Endpoints;

use App\Tests\Fixture\LoadSchoolData;
use App\Tests\Fixture\LoadVocabularyData;

/**
 * Vocabulary API endpoint Test.
 *
 */
#[\PHPUnit\Framework\Attributes\Group('api_2')]
class VocabularyTest extends AbstractReadWriteEndpoint
{
    protected string $testName =  'vocabularies';

    protected function getFixtures(): array
    {
        return [
            LoadSchoolData::class,
            LoadVocabularyData::class,
        ];
    }

    public static function putsToTest(): array
    {
        return [
            'title' => ['title', 'foo bar'],
            'school' => ['school', 2],
            // 'terms' => ['terms', [1]], // skipped
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
            'ids' => [[0, 1], ['id' => [1, 2]]],
            'title' => [[1], ['title' => 'second vocabulary']],
            'school' => [[1], ['school' => 2]],
            'terms' => [[1], ['terms' => [5]]],
            'active' => [[0], ['active' => true]],
            'notActive' => [[1], ['active' => false]],
        ];
    }

    public static function graphQLFiltersToTest(): array
    {
        $filters = self::filtersToTest();
        $filters['ids'] = [[0, 1], ['ids' => [1, 2]]];

        return $filters;
    }

    public function testCannotCreateWithEmptyTitle(): void
    {
        $jwt = $this->createJwtForRootUser($this->kernelBrowser);
        $dataLoader = $this->getDataLoader();
        $data = $dataLoader->create();
        $data['title'] = '';
        $this->badPostTest($data, $jwt);
    }

    public function testCannotCreateWithoutTitle(): void
    {
        $jwt = $this->createJwtForRootUser($this->kernelBrowser);
        $dataLoader = $this->getDataLoader();
        $data = $dataLoader->create();
        unset($data['title']);
        $this->badPostTest($data, $jwt);
    }

    public function testCannotSaveWithEmptyTitle(): void
    {
        $jwt = $this->createJwtForRootUser($this->kernelBrowser);
        $dataLoader = $this->getDataLoader();
        $data = $dataLoader->getOne();
        $data['title'] = '';
        $this->badPutTest($data, $data['id'], $jwt);
    }

    public function testCannotSaveWithoutTitle(): void
    {
        $jwt = $this->createJwtForRootUser($this->kernelBrowser);
        $dataLoader = $this->getDataLoader();
        $data = $dataLoader->getOne();
        unset($data['title']);
        $this->badPutTest($data, $data['id'], $jwt);
    }
}
