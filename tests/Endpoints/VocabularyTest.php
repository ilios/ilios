<?php

declare(strict_types=1);

namespace App\Tests\Endpoints;

use App\Tests\Fixture\LoadSchoolData;
use App\Tests\Fixture\LoadVocabularyData;
use Exception;

/**
 * Vocabulary API endpoint Test.
 * @group api_2
 */
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

    /**
     * @inheritDoc
     */
    public function putsToTest(): array
    {
        return [
            'title' => ['title', 'foo bar'],
            'school' => ['school', 2],
            'terms' => ['terms', [1], true],
            'active' => ['active', false],
        ];
    }

    /**
     * @inheritDoc
     */
    public function readOnlyPropertiesToTest(): array
    {
        return [
            'id' => ['id', 1, 99],
        ];
    }

    /**
     * @inheritDoc
     */
    public function filtersToTest(): array
    {
        return [
            'id' => [[0], ['id' => 1]],
            'ids' => [[0, 1], ['id' => [1, 2]]],
            'title' => [[1], ['title' => 'second vocabulary']],
            'school' => [[1], ['school' => 2]],
            'terms' => [[1], ['terms' => [5]], true],
            'active' => [[0], ['active' => true]],
            'notActive' => [[1], ['active' => false]],
        ];
    }

    public function graphQLFiltersToTest(): array
    {
        $filters = $this->filtersToTest();
        $filters['ids'] = [[0, 1], ['ids' => [1, 2]]];

        return $filters;
    }

    /**
     * @throws Exception
     */
    public function testCannotCreateWithEmptyTitle(): void
    {
        $jwt = $this->createJwtForRootUser($this->kernelBrowser);
        $dataLoader = $this->getDataLoader();
        $data = $dataLoader->create();
        $data['title'] = '';
        $this->badPostTest($data, $jwt);
    }

    /**
     * @throws Exception
     */
    public function testCannotCreateWithoutTitle(): void
    {
        $jwt = $this->createJwtForRootUser($this->kernelBrowser);
        $dataLoader = $this->getDataLoader();
        $data = $dataLoader->create();
        unset($data['title']);
        $this->badPostTest($data, $jwt);
    }

    /**
     * @throws Exception
     */
    public function testCannotSaveWithEmptyTitle(): void
    {
        $jwt = $this->createJwtForRootUser($this->kernelBrowser);
        $dataLoader = $this->getDataLoader();
        $data = $dataLoader->getOne();
        $data['title'] = '';
        $this->badPutTest($data, $data['id'], $jwt);
    }

    /**
     * @throws Exception
     */
    public function testCannotSaveWithoutTitle(): void
    {
        $jwt = $this->createJwtForRootUser($this->kernelBrowser);
        $dataLoader = $this->getDataLoader();
        $data = $dataLoader->getOne();
        unset($data['title']);
        $this->badPutTest($data, $data['id'], $jwt);
    }
}
