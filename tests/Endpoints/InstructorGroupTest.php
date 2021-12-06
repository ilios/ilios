<?php

declare(strict_types=1);

namespace App\Tests\Endpoints;

use App\Tests\Fixture\LoadIlmSessionData;
use App\Tests\Fixture\LoadInstructorGroupData;
use App\Tests\Fixture\LoadLearnerGroupData;
use App\Tests\Fixture\LoadLearningMaterialData;
use App\Tests\Fixture\LoadOfferingData;
use App\Tests\Fixture\LoadSchoolData;
use App\Tests\Fixture\LoadSessionLearningMaterialData;
use App\Tests\Fixture\LoadTermData;
use App\Tests\Fixture\LoadUserData;
use App\Tests\ReadWriteEndpointTest;

/**
 * InstructorGroup API endpoint Test.
 * @group api_1
 */
class InstructorGroupTest extends ReadWriteEndpointTest
{
    protected string $testName =  'instructorGroups';

    protected function getFixtures(): array
    {
        return [
            LoadInstructorGroupData::class,
            LoadSchoolData::class,
            LoadTermData::class,
            LoadLearnerGroupData::class,
            LoadIlmSessionData::class,
            LoadUserData::class,
            LoadOfferingData::class,
            LoadLearningMaterialData::class,
            LoadSessionLearningMaterialData::class,
        ];
    }

    /**
     * @inheritDoc
     */
    public function putsToTest(): array
    {
        return [
            'title' => ['title', $this->getFaker()->text(60)],
            'school' => ['school', 2],
            'learnerGroups' => ['learnerGroups', [2, 3]],
            'ilmSessions' => ['ilmSessions', [1, 2]],
            'users' => ['users', [1]],
            'offerings' => ['offerings', [2, 3, 4], $skipped = true],
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
            'ids' => [[1, 2], ['id' => [2, 3]]],
            'title' => [[1], ['title' => 'second instructor group']],
            'school' => [[0, 1, 2], ['school' => 1]],
            'schools' => [[0, 1, 2], ['schools' => [1]]],
            'learnerGroups' => [[0], ['learnerGroups' => [1]]],
            'ilmSessions' => [[0], ['ilmSessions' => [1]]],
            'users' => [[0, 1, 2], ['users' => [2]]],
            'offerings' => [[1], ['offerings' => [3]]],
            'courses' => [[0, 1], ['courses' => [1]]],
            'sessions' => [[0, 1], ['sessions' => [1, 2, 3]]],
            'sessionTypes' => [[0, 1, 2], ['sessionTypes' => [1, 2]]],
            'learningMaterials' => [[0], ['learningMaterials' => [1]]],
            'instructors' => [[0, 1, 2], ['instructors' => [2]]],
            'terms' => [[0, 1], ['terms' => [1, 2, 3]]],
        ];
    }

    public function testPostInstructorGroupIlmSession()
    {
        $dataLoader = $this->getDataLoader();
        $data = $dataLoader->create();
        $postData = $data;
        $this->relatedPostDataTest($data, $postData, 'instructorGroups', 'learnerGroups');
    }

    public function testPostInstructorGroupLearnerGroup()
    {
        $dataLoader = $this->getDataLoader();
        $data = $dataLoader->create();
        $postData = $data;
        $this->relatedPostDataTest($data, $postData, 'instructorGroups', 'ilmSessions');
    }
}
