<?php

declare(strict_types=1);

namespace App\Tests\Endpoints;

use PHPUnit\Framework\Attributes\Group;
use App\Tests\DataLoader\IlmSessionData;
use App\Tests\DataLoader\LearningMaterialData;
use App\Tests\DataLoader\SessionLearningMaterialData;
use App\Tests\Fixture\LoadCourseLearningMaterialData;
use App\Tests\Fixture\LoadCourseObjectiveData;
use App\Tests\Fixture\LoadIlmSessionData;
use App\Tests\Fixture\LoadLearningMaterialStatusData;
use App\Tests\Fixture\LoadOfferingData;
use App\Tests\Fixture\LoadSessionData;
use App\Tests\Fixture\LoadSessionLearningMaterialData;
use App\Tests\Fixture\LoadSessionObjectiveData;
use App\Tests\Fixture\LoadTermData;
use App\Tests\QEndpointTrait;

/**
 * Session API endpoint Test.
 */
#[Group('api_2')]
class SessionTest extends AbstractReadWriteEndpoint
{
    use QEndpointTrait;

    protected string $testName =  'sessions';

    protected function getFixtures(): array
    {
        return [
            LoadSessionData::class,
            LoadTermData::class,
            LoadSessionLearningMaterialData::class,
            LoadOfferingData::class,
            LoadSessionLearningMaterialData::class,
            LoadCourseLearningMaterialData::class,
            LoadLearningMaterialStatusData::class,
            LoadIlmSessionData::class,
            LoadSessionObjectiveData::class,
            LoadCourseObjectiveData::class,
        ];
    }

    /**
     * returns an array of field / value pairs to modify
     * the key for each item is reflected in the failure message
     * each one will be separately tested in a PUT request
     */
    public static function putsToTest(): array
    {
        return [
            'title' => ['title', 'lorem ipsum'],
            'attireRequired' => ['attireRequired', true],
            'equipmentRequired' => ['equipmentRequired', true],
            'supplemental' => ['supplemental', true],
            'attendanceRequired' => ['attendanceRequired', true],
            'nullAttireRequired' => ['attireRequired', null],
            'nullEquipmentRequired' => ['equipmentRequired', null],
            'nullSupplemental' => ['supplemental', null],
            'nullAttendanceRequired' => ['attendanceRequired', null],
            'publishedAsTbd' => ['publishedAsTbd', true],
            'published' => ['published', false],
            'instructionalNotes' => ['instructionalNotes', 'dev/null'],
            'emptyInstructionalNotes' => ['instructionalNotes', ''],
            'nullInstructionalNotes' => ['instructionalNotes', null],
            'sessionType' => ['sessionType', 2],
            'course' => ['course', 2],
            'ilmSession' => ['ilmSession', 1],
            'terms' => ['terms', [1]],
            // 'meshDescriptors' => ['meshDescriptors', [1]], // skipped
            // 'learningMaterials' => ['learningMaterials', [2]], // skipped
            // 'offerings' => ['offerings', [1]], // skipped
            'administrators' => ['administrators', [2]],
            'studentAdvisors' => ['studentAdvisors', [1]],
            'postrequisite' => ['postrequisite', 2],
            'prerequisites' => ['prerequisites', [2]],
            'description' => ['description', 'salt'],
            'blankDescription' => ['description', ''],
            'nullDescription' => ['description', null],
        ];
    }

    /**
     * returns an array of field / value pairs that are readOnly
     * the key for each item is reflected in the failure message
     * each one will be separately tested in a PUT request
     */
    public static function readOnlyPropertiesToTest(): array
    {
        return [
            'id' => ['id', 1, 99],
            'updatedAt' => ['updatedAt', 1, '2015-01-01'],
        ];
    }


    /**
     * returns an array of filters to test
     * the key for each item is reflected in the failure message
     * the first item is an array of the positions the expected items
     * can be found in the data loader
     * the second item is the filter we are testing
     */
    public static function filtersToTest(): array
    {
        return [
            'id' => [[0], ['id' => 1]],
            'ids' => [[0, 2], ['id' => [1, 3]]],
            'title' => [[0], ['title' => 'session1Title']],
            'attireRequired' => [[1], ['attireRequired' => true]],
            'equipmentRequired' => [[1], ['equipmentRequired' => true]],
            'supplemental' => [[1, 2], ['supplemental' => true]],
            'attendanceRequired' => [[1], ['attendanceRequired' => true]],
            'publishedAsTbd' => [[1], ['publishedAsTbd' => true]],
            'published' => [[0, 1], ['published' => true]],
            'sessionType' => [[1, 2, 3], ['sessionType' => 2]],
            'multipleSessionTypes' => [[1, 2, 3], ['sessionType' => [2]]],
            'supplementalAndSessionType' => [[1, 2], ['supplemental' => true, 'sessionType' => 2]],
            'course' => [[3], ['course' => 4]],
            'academicYear' => [[0,1], ['academicYears' => 2016]],
            'academicYears' => [[0,1], ['academicYears' => [2016]]],
            'multipleCourse' => [[0, 1, 3], ['course' => [1, 4]]],
            // 'ilmSession' => [[0], ['ilmSessions' => [1]]], // skipped
            'terms' => [[1], ['terms' => 1]],
            'termsMultiple' => [[0], ['terms' => [2, 5]]],
            'meshDescriptors' => [[3], ['meshDescriptors' => ['abc3']]],
            'meshDescriptorsMultiple' => [[0, 2, 3], ['meshDescriptors' => ['abc2', 'abc3']]],
            // 'sessionDescription' => [[0], ['sessionDescription' => 1]], // skipped
            'learningMaterials' => [[0], ['learningMaterials' => [1]]],
            // 'offerings' => [[0], ['offerings' => 1]], // skipped
            // 'administrators' => [[0], ['administrators' => [1]]], // skipped
            'supplementalAndAttireRequired' => [[1], ['supplemental' => true, 'attireRequired' => true]],
            'programs' => [[3], ['programs' => [2]]],
            'instructors' => [[0, 1, 4, 5, 6], ['instructors' => [2]]],
            'instructorGroups' => [[0, 4], ['instructorGroups' => [1]]],
            'competencies' => [[0], ['competencies' => [1]]],
            'schools' => [[3], ['schools' => [2]]],
            'schoolsAndCourses' => [[3], ['schools' => [2], 'courses' => [4]]],
        ];
    }

    public static function graphQLFiltersToTest(): array
    {
        $filters = self::filtersToTest();
        $filters['ids'] = [[0, 2], ['ids' => [1, 3]]];
        $filters['multipleCourse'] = [[0, 1, 3], ['courses' => [1, 4]]];
        $filters['multipleSessionTypes'] = [[1, 2, 3], ['sessionTypes' => [2]]];

        return $filters;
    }

    protected function getTimeStampFields(): array
    {
        return ['updatedAt'];
    }

    public function testUpdatingIlmUpdatesSessionStamp(): void
    {
        $jwt = $this->createJwtForRootUser($this->kernelBrowser);
        $dataLoader = self::getContainer()->get(IlmSessionData::class);
        $data = $dataLoader->getOne();
        $data['hours'] += 5;
        $this->relatedTimeStampUpdateTest($data['session'], 'ilmsessions', 'ilmSession', $data, $jwt);
    }

    public function testUpdatingIlmInstructorUpdatesSessionStamp(): void
    {
        $jwt = $this->createJwtForRootUser($this->kernelBrowser);
        $dataLoader = self::getContainer()->get(IlmSessionData::class);
        $data = $dataLoader->getOne();
        $data['instructors'] = ["1", "2"];
        $this->relatedTimeStampUpdateTest($data['session'], 'ilmsessions', 'ilmSession', $data, $jwt);
    }

    public function testUpdatingIlmInstructorGroupsUpdatesSessionStamp(): void
    {
        $jwt = $this->createJwtForRootUser($this->kernelBrowser);
        $dataLoader = self::getContainer()->get(IlmSessionData::class);
        $data = $dataLoader->getOne();
        $data['instructorGroups'] = ["1", "2"];
        $this->relatedTimeStampUpdateTest($data['session'], 'ilmsessions', 'ilmSession', $data, $jwt);
    }

    public function testUpdatingIlmLearnerGroupsUpdatesSessionStamp(): void
    {
        $jwt = $this->createJwtForRootUser($this->kernelBrowser);
        $dataLoader = self::getContainer()->get(IlmSessionData::class);
        $data = $dataLoader->getOne();
        $data['learnerGroups'] = ["1", "2"];
        $this->relatedTimeStampUpdateTest($data['session'], 'ilmsessions', 'ilmSession', $data, $jwt);
    }

    public function testUpdatingIlmLearnersUpdatesSessionStamp(): void
    {
        $jwt = $this->createJwtForRootUser($this->kernelBrowser);
        $dataLoader = self::getContainer()->get(IlmSessionData::class);
        $data = $dataLoader->getOne();
        $data['learners'] = ["1", "2"];
        $this->relatedTimeStampUpdateTest($data['session'], 'ilmsessions', 'ilmSession', $data, $jwt);
    }

    public function testUpdatingLearningMaterialUpdatesSessionStamp(): void
    {
        $jwt = $this->createJwtForRootUser($this->kernelBrowser);
        $dataLoader = self::getContainer()->get(LearningMaterialData::class);
        $data = $dataLoader->getOne();
        $data['status'] = '1';
        $this->relatedTimeStampUpdateTest(1, 'learningmaterials', 'learningMaterial', $data, $jwt);
    }

    public function testNewSessionLearningMaterialUpdatesSessionStamp(): void
    {
        $jwt = $this->createJwtForRootUser($this->kernelBrowser);
        $dataLoader = self::getContainer()->get(SessionLearningMaterialData::class);
        $data = $dataLoader->create();
        $this->relatedTimeStampPostTest(1, 'sessionlearningmaterials', 'sessionLearningMaterials', $data, $jwt);
    }

    public function testUpdatingSessionLearningMaterialUpdatesSessionStamp(): void
    {
        $jwt = $this->createJwtForRootUser($this->kernelBrowser);
        $dataLoader = self::getContainer()->get(SessionLearningMaterialData::class);
        $data = $dataLoader->getOne();
        $data['required'] = !$data['required'];
        $this->relatedTimeStampUpdateTest(1, 'sessionlearningmaterials', 'sessionLearningMaterial', $data, $jwt);
    }

    public function testDeletingSessionLearningMaterialUpdatesSessionStamp(): void
    {
        $jwt = $this->createJwtForRootUser($this->kernelBrowser);
        $dataLoader = self::getContainer()->get(SessionLearningMaterialData::class);
        $data = $dataLoader->getOne();
        $this->relatedTimeStampDeleteTest(1, 'sessionlearningmaterials', $data['id'], $jwt);
    }

    public function testRemovePostrequisite(): void
    {
        $jwt = $this->createJwtForRootUser($this->kernelBrowser);
        $dataLoader = $this->getDataLoader();
        $data = $dataLoader->getAll()[1];
        $this->assertNotNull($data['postrequisite']);
        $id = $data['id'];
        $data['postrequisite'] = null;
        $postData = $data;
        $this->putTest($data, $postData, $id, $jwt);
    }

    public static function qsToTest(): array
    {
        return [
            ['ess', [0, 2, 3, 4, 5, 6, 7]],
            ['ours', [0, 1, 2, 3, 4, 5, 6, 7]],
            ['fourth', [3]],
            ['bar', [1]],
            ['third session', [2]],
            ['2016', [0, 1]],
        ];
    }

    public function testFindByQWithLimit(): void
    {
        $jwt = $this->createJwtForRootUser($this->kernelBrowser);
        $dataLoader = $this->getDataLoader();
        $all = $dataLoader->getAll();
        $filters = ['q' => 'sess', 'limit' => 1];
        $this->filterTest($filters, [$all[0]], $jwt);
        $filters = ['q' => 'ours', 'limit' => 2];
        $this->filterTest($filters, [$all[0], $all[1]], $jwt);
    }

    public function testFindByQWithOffset(): void
    {
        $jwt = $this->createJwtForRootUser($this->kernelBrowser);
        $dataLoader = $this->getDataLoader();
        $all = $dataLoader->getAll();
        $filters = ['q' => 'sess', 'offset' => 2];
        $this->filterTest($filters, [$all[3], $all[4], $all[5], $all[6], $all[7]], $jwt);
    }

    public function testFindByQWithOffsetAndLimit(): void
    {
        $jwt = $this->createJwtForRootUser($this->kernelBrowser);
        $dataLoader = $this->getDataLoader();
        $all = $dataLoader->getAll();
        $filters = ['q' => 'sess', 'offset' => 2, 'limit' => 1];
        $this->filterTest($filters, [$all[3]], $jwt);
        $filters = ['q' => 'sess', 'offset' => 3, 'limit' => 2];
        $this->filterTest($filters, [$all[4], $all[5]], $jwt);
    }

    public function testFindByQWithOffsetAndLimitJsonApi(): void
    {
        $jwt = $this->createJwtForRootUser($this->kernelBrowser);
        $dataLoader = $this->getDataLoader();
        $all = $dataLoader->getAll();
        $filters = ['q' => 'sess', 'offset' => 2, 'limit' => 1];
        $this->jsonApiFilterTest($filters, [$all[3]], $jwt);
        $filters = ['q' => 'sess', 'offset' => 3, 'limit' => 2];
        $this->jsonApiFilterTest($filters, [$all[4], $all[5]], $jwt);
    }
}
