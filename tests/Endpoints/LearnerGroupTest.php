<?php

declare(strict_types=1);

namespace App\Tests\Endpoints;

use PHPUnit\Framework\Attributes\Group;
use App\Tests\Fixture\LoadCohortData;
use App\Tests\Fixture\LoadIlmSessionData;
use App\Tests\Fixture\LoadLearnerGroupData;
use App\Tests\Fixture\LoadOfferingData;
use App\Tests\Fixture\LoadTermData;
use App\Tests\Fixture\LoadUserData;
use App\Tests\Fixture\LoadVocabularyData;

/**
 * LearnerGroup API endpoint Test.
 */
#[Group('api_2')]
final class LearnerGroupTest extends AbstractReadWriteEndpoint
{
    protected string $testName =  'learnerGroups';

    protected function getFixtures(): array
    {
        return [
            LoadLearnerGroupData::class,
            LoadCohortData::class,
            LoadIlmSessionData::class,
            LoadOfferingData::class,
            LoadUserData::class,
            LoadVocabularyData::class,
            LoadTermData::class,
        ];
    }

    public static function putsToTest(): array
    {
        return [
            'title' => ['title', 'group of learners'],
            'location' => ['location', 'lorem ipsum'],
            'url' => ['url', 'http://dev.null'],
            'needsAccommodation' => ['needsAccommodation', true],
            'cohort' => ['cohort', 3],
            'parent' => ['parent', 2],
            'ancestor' => ['ancestor', '3'],
            // 'children' => ['children', [1]], // skipped
            'ilmSessions' => ['ilmSessions', [2]],
            'offerings' => ['offerings', [2]],
            'instructorGroups' => ['instructorGroups', [1, 2]],
            'users' => ['users', [1]],
            'instructors' => ['instructors', [1, 2]],
            'descendants' => ['descendants', [2, 3]],
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
            'title' => [[2], ['title' => 'third learner group']],
            'location' => [[3], ['location' => 'fourth location']],
            'url' => [[0, 3], ['url' => 'https://iliosproject.org']],
            'needsAccommodation' => [[1], ['needsAccommodation' => true]],
            'doesNotNeedAccommodation' => [[0, 2, 3, 4], ['needsAccommodation' => false]],
            'cohort' => [[1], ['cohort' => 2]],
            'parent' => [[3], ['parent' => 1]],
            'ancestor' => [[3], ['ancestor' => 3]],
            'noParent' => [[0, 1, 2, 4], ['parent' => 'null']],
            // 'children' => [[0], ['children' => [4]]], // skipped
            // 'ilmSessions' => [[0, 2], ['ilmSessions' => [1]]], // skipped
            // 'offerings' => [[1, 4], ['offerings' => [2]]], // skipped
            // 'instructorGroups' => [[0], ['instructorGroups' => [1]]], // skipped
            // 'users' => [[0, 4], ['users' => [5]]], // skipped
            // 'instructors' => [[0, 2], ['instructors' => [1]]], // skipped
            'cohorts' => [[1], ['cohorts' => [2]]],
        ];
    }

    public static function graphQLFiltersToTest(): array
    {
        $filters = self::filtersToTest();
        $filters['ids'] = [[1, 2], ['ids' => [2, 3]]];
        $filters['missingIds'] = [[], ['ids' => [99]]];

        return $filters;
    }

    public function testPostLearnerGroupIlmSession(): void
    {
        $jwt = $this->createJwtForRootUser($this->kernelBrowser);
        $dataLoader = $this->getDataLoader();
        $data = $dataLoader->create();
        $postData = $data;
        $this->relatedPostDataTest($data, $postData, $jwt, 'learnerGroups', 'ilmSessions');
    }

    public function testPostLearnerGroupOfferings(): void
    {
        $jwt = $this->createJwtForRootUser($this->kernelBrowser);
        $dataLoader = $this->getDataLoader();
        $data = $dataLoader->create();
        $postData = $data;
        $this->relatedPostDataTest($data, $postData, $jwt, 'learnerGroups', 'offerings');
    }

    public function testRemoveParent(): void
    {
        $jwt = $this->createJwtForRootUser($this->kernelBrowser);
        $dataLoader = $this->getDataLoader();
        $data = $dataLoader->getAll()[3];
        $this->assertNotNull($data['parent']);
        $id = $data['id'];
        $data['parent'] = null;
        $postData = $data;
        $this->putTest($data, $postData, $id, $jwt);
    }
}
