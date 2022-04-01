<?php

declare(strict_types=1);

namespace App\Tests\Endpoints;

use App\Entity\CourseInterface;
use App\Tests\Fixture\LoadSchoolData;
use App\Tests\Fixture\LoadVocabularyData;
use App\Tests\ReadWriteEndpointTest;
use Symfony\Component\HttpFoundation\Response;

/**
 * Vocabulary API endpoint Test.
 * @group api_2
 */
class VocabularyTest extends ReadWriteEndpointTest
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
            'terms' => ['terms', [1], $skipped = true],
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
            'terms' => [[1], ['terms' => [5]], $skipped = true],
            'active' => [[0], ['active' => true]],
            'notActive' => [[1], ['active' => false]],
        ];
    }

    public function testCannotCreateWithEmptyTitle()
    {
        $dataLoader = $this->getDataLoader();
        $data = $dataLoader->create();
        $data['title'] = '';
        $this->badPostTest($data);
    }

    public function testCannotCreateWithNoTitle()
    {
        $dataLoader = $this->getDataLoader();
        $data = $dataLoader->create();
        unset($data['title']);
        $this->badPostTest($data);
    }

    public function testCannotSaveWithEmptyTitle()
    {
        $dataLoader = $this->getDataLoader();
        $data = $dataLoader->getOne();
        $data['title'] = '';
        $this->badPutTest($data, $data['id']);
    }

    public function testCannotSaveWithNoTitle()
    {
        $dataLoader = $this->getDataLoader();
        $data = $dataLoader->getOne();
        unset($data['title']);
        $this->badPutTest($data, $data['id']);
    }
}
