<?php

declare(strict_types=1);

namespace App\Tests\Endpoints;

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
use App\Tests\ReadWriteEndpointTest;

/**
 * Session API endpoint Test.
 * @group api_2
 * @group time-sensitive
 */
class SessionTest extends ReadWriteEndpointTest
{

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
     * @inheritDoc
     *
     * returns an array of field / value pairs to modify
     * the key for each item is reflected in the failure message
     * each one will be separately tested in a PUT request
     */
    public function putsToTest(): array
    {
        return [
            'title' => ['title', $this->getFaker()->text()],
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
            'instructionalNotes' => ['instructionalNotes', $this->getFaker()->text()],
            'emptyInstructionalNotes' => ['instructionalNotes', ''],
            'nullInstructionalNotes' => ['instructionalNotes', null],
            'sessionType' => ['sessionType', 2],
            'course' => ['course', 2],
            'ilmSession' => ['ilmSession', 1],
            'terms' => ['terms', [1]],
            'meshDescriptors' => ['meshDescriptors', [1], $skipped = true],
            'learningMaterials' => ['learningMaterials', [2], $skipped = true],
            'offerings' => ['offerings', [1], $skipped = true],
            'administrators' => ['administrators', [2]],
            'studentAdvisors' => ['studentAdvisors', [1]],
            'postrequisite' => ['postrequisite', 2],
            'emptyPostrequisite' => ['postrequisite', null],
            'prerequisites' => ['prerequisites', [2]],
            'description' => ['description', $this->getFaker()->text()],
            'blankDescription' => ['description', ''],
            'nullDescription' => ['description', null],
        ];
    }

    /**
     * @inheritDoc
     *
     * returns an array of field / value pairs that are readOnly
     * the key for each item is reflected in the failure message
     * each one will be separately tested in a PUT request
     */
    public function readOnlyPropertiesToTest(): array
    {
        return [
            'id' => ['id', 1, 99],
            'updatedAt' => ['updatedAt', 1, '2015-01-01'],
        ];
    }


    /**
     * @inheritDoc
     *
     * returns an array of filters to test
     * the key for each item is reflected in the failure message
     * the first item is an array of the positions the expected items
     * can be found in the data loader
     * the second item is the filter we are testing
     */
    public function filtersToTest(): array
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
            'supplementalAndSessionType' => [[1, 2], ['supplemental' => true, 'sessionType' => 2]],
            'course' => [[3], ['course' => 4]],
            'multipleCourse' => [[0, 1, 3], ['course' => [1, 4]]],
//            'ilmSession' => [[0], ['ilmSessions' => [1]]],
            'terms' => [[1], ['terms' => 1]],
            'termsMultiple' => [[0], ['terms' => [2, 5]]],
            'meshDescriptors' => [[3], ['meshDescriptors' => ['abc3']]],
            'meshDescriptorsMultiple' => [[0, 2, 3], ['meshDescriptors' => ['abc2', 'abc3']]],
//            'sessionDescription' => [[0], ['sessionDescription' => 1]],
            'learningMaterials' => [[0], ['learningMaterials' => [1]]],
//            'offerings' => [[0], ['offerings' => 1]],
//            'administrators' => [[0], ['administrators' => [1]]],
            'supplementalAndAttireRequired' => [[1], ['supplemental' => true, 'attireRequired' => true]],
            'programs' => [[3], ['programs' => [2]]],
            'instructors' => [[0, 1, 4, 5, 6], ['instructors' => [2]]],
            'instructorGroups' => [[0, 4], ['instructorGroups' => [1]]],
            'competencies' => [[0], ['competencies' => [1]]],
            'schools' => [[3], ['schools' => [2]]],
        ];
    }

    protected function getTimeStampFields(): array
    {
        return ['updatedAt'];
    }

    public function testUpdatingIlmUpdatesSessionStamp()
    {
        $dataLoader = self::getContainer()->get(IlmSessionData::class);
        $data = $dataLoader->getOne();
        $data['hours'] += 5;
        $this->relatedTimeStampUpdateTest($data['session'], 'ilmsessions', 'ilmSession', $data);
    }

    public function testUpdatingIlmInstructorUpdatesSessionStamp()
    {
        $dataLoader = self::getContainer()->get(IlmSessionData::class);
        $data = $dataLoader->getOne();
        $data['instructors'] = ["1", "2"];
        $this->relatedTimeStampUpdateTest($data['session'], 'ilmsessions', 'ilmSession', $data);
    }

    public function testUpdatingIlmInstructorGroupsUpdatesSessionStamp()
    {
        $dataLoader = self::getContainer()->get(IlmSessionData::class);
        $data = $dataLoader->getOne();
        $data['instructorGroups'] = ["1", "2"];
        $this->relatedTimeStampUpdateTest($data['session'], 'ilmsessions', 'ilmSession', $data);
    }

    public function testUpdatingIlmLearnerGroupsUpdatesSessionStamp()
    {
        $dataLoader = self::getContainer()->get(IlmSessionData::class);
        $data = $dataLoader->getOne();
        $data['learnerGroups'] = ["1", "2"];
        $this->relatedTimeStampUpdateTest($data['session'], 'ilmsessions', 'ilmSession', $data);
    }

    public function testUpdatingIlmLearnersUpdatesSessionStamp()
    {
        $dataLoader = self::getContainer()->get(IlmSessionData::class);
        $data = $dataLoader->getOne();
        $data['learners'] = ["1", "2"];
        $this->relatedTimeStampUpdateTest($data['session'], 'ilmsessions', 'ilmSession', $data);
    }

    public function testUpdatingLearningMaterialUpdatesSessionStamp()
    {
        $dataLoader = self::getContainer()->get(LearningMaterialData::class);
        $data = $dataLoader->getOne();
        $data['status'] = '1';
        $this->relatedTimeStampUpdateTest(1, 'learningmaterials', 'learningMaterial', $data);
    }

    public function testNewSessionLearningMaterialUpdatesSessionStamp()
    {
        $dataLoader = self::getContainer()->get(SessionLearningMaterialData::class);
        $data = $dataLoader->create();
        $this->relatedTimeStampPostTest(1, 'sessionlearningmaterials', 'sessionLearningMaterials', $data);
    }

    public function testUpdatingSessionLearningMaterialUpdatesSessionStamp()
    {
        $dataLoader = self::getContainer()->get(SessionLearningMaterialData::class);
        $data = $dataLoader->getOne();
        $data['required'] = !$data['required'];
        $this->relatedTimeStampUpdateTest(1, 'sessionlearningmaterials', 'sessionLearningMaterial', $data);
    }

    public function testDeletingSessionLearningMaterialUpdatesSessionStamp()
    {
        $dataLoader = self::getContainer()->get(SessionLearningMaterialData::class);
        $data = $dataLoader->getOne();
        $this->relatedTimeStampDeleteTest(1, 'sessionlearningmaterials', $data['id']);
    }
}
