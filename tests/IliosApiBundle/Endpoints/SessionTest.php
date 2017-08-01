<?php

namespace Tests\IliosApiBundle\Endpoints;

use Tests\CoreBundle\DataLoader\IlmSessionData;
use Tests\CoreBundle\DataLoader\LearningMaterialData;
use Tests\CoreBundle\DataLoader\SessionDescriptionData;
use Tests\CoreBundle\DataLoader\SessionLearningMaterialData;
use Tests\IliosApiBundle\AbstractEndpointTest;
use Tests\IliosApiBundle\EndpointTestsTrait;

/**
 * Session API endpoint Test.
 * @group api_2
 */
class SessionTest extends AbstractEndpointTest
{
    use EndpointTestsTrait;

    protected $testName =  'sessions';

    /**
     * @inheritdoc
     */
    protected function getFixtures()
    {
        return [
            'Tests\CoreBundle\Fixture\LoadSessionData',
            'Tests\CoreBundle\Fixture\LoadTermData',
            'Tests\CoreBundle\Fixture\LoadSessionDescriptionData',
            'Tests\CoreBundle\Fixture\LoadSessionLearningMaterialData',
            'Tests\CoreBundle\Fixture\LoadOfferingData',
            'Tests\CoreBundle\Fixture\LoadSessionLearningMaterialData',
            'Tests\CoreBundle\Fixture\LoadCourseLearningMaterialData',
            'Tests\CoreBundle\Fixture\LoadLearningMaterialStatusData',
            'Tests\CoreBundle\Fixture\LoadIlmSessionData',
            'Tests\CoreBundle\Fixture\LoadUserData',
        ];
    }

    /**
     * @inheritDoc
     *
     * returns an array of field / value pairs to modify
     * the key for each item is reflected in the failure message
     * each one will be separately tested in a PUT request
     */
    public function putsToTest()
    {
        return [
            'title' => ['title', $this->getFaker()->text],
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
            'sessionType' => ['sessionType', 2],
            'course' => ['course', 2],
            'ilmSession' => ['ilmSession', 1],
            'terms' => ['terms', [1]],
            'objectives' => ['objectives', [1]],
            'meshDescriptors' => ['meshDescriptors', [1], $skipped = true],
            'learningMaterials' => ['learningMaterials', [2], $skipped = true],
            'offerings' => ['offerings', [1], $skipped = true],
            'administrators' => ['administrators', [2]],
        ];
    }

    /**
     * @inheritDoc
     *
     * returns an array of field / value pairs that are readOnly
     * the key for each item is reflected in the failure message
     * each one will be separately tested in a PUT request
     */
    public function readOnlyPropertiesToTest()
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
    public function filtersToTest()
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
//            'objectives' => [[0], ['objectives' => [1]]],
            'meshDescriptors' => [[2], ['meshDescriptors' => ['abc2']]],
            'meshDescriptorsMultiple' => [[2, 3], ['meshDescriptors' => ['abc2', 'abc3']]],
//            'sessionDescription' => [[0], ['sessionDescription' => 1]],
            'learningMaterials' => [[0], ['learningMaterials' => [1]]],
//            'offerings' => [[0], ['offerings' => 1]],
//            'administrators' => [[0], ['administrators' => [1]]],
            'supplementalAndAttireRequired' => [[1], ['supplemental' => true, 'attireRequired' => true]],
            'programs' => [[3], ['programs' => [2]]],
            'instructors' => [[0, 1, 4, 5, 6], ['instructors' => [2]]],
            'instructorGroups' => [[0, 4], ['instructorGroups' => [1]]],
            'competencies' => [[0, 3], ['competencies' => [1]]],
            'schools' => [[3], ['schools' => [2]]],
        ];
    }

    protected function getTimeStampFields()
    {
        return ['updatedAt'];
    }

    public function testUpdatingIlmUpdatesSessionStamp()
    {
        $dataLoader = $this->container->get(IlmSessionData::class);
        $data = $dataLoader->getOne();
        $data['instructors'] = ["1", "2"];
        $this->relatedTimeStampUpdateTest($data['session'], 'ilmsessions', 'ilmSession', $data);
    }

    public function testUpdatingIlmInstructorUpdatesSessionStamp()
    {
        $dataLoader = $this->container->get(IlmSessionData::class);
        $data = $dataLoader->getOne();
        $data['instructors'] = ["1", "2"];
        $this->relatedTimeStampUpdateTest($data['session'], 'ilmsessions', 'ilmSession', $data);
    }

    public function testUpdatingIlmInstructorGroupsUpdatesSessionStamp()
    {
        $dataLoader = $this->container->get(IlmSessionData::class);
        $data = $dataLoader->getOne();
        $data['instructorGroups'] = ["1", "2"];
        $this->relatedTimeStampUpdateTest($data['session'], 'ilmsessions', 'ilmSession', $data);
    }

    public function testUpdatingIlmLearnerGroupsUpdatesSessionStamp()
    {
        $dataLoader = $this->container->get(IlmSessionData::class);
        $data = $dataLoader->getOne();
        $data['learnerGroups'] = ["1", "2"];
        $this->relatedTimeStampUpdateTest($data['session'], 'ilmsessions', 'ilmSession', $data);
    }

    public function testUpdatingIlmLearnersUpdatesSessionStamp()
    {
        $dataLoader = $this->container->get(IlmSessionData::class);
        $data = $dataLoader->getOne();
        $data['learners'] = ["1", "2"];
        $this->relatedTimeStampUpdateTest($data['session'], 'ilmsessions', 'ilmSession', $data);
    }

    public function testUpdatingLearningMaterialUpdatesSessionStamp()
    {
        $dataLoader = $this->container->get(LearningMaterialData::class);
        $data = $dataLoader->getOne();
        $data['status'] = '1';
        $this->relatedTimeStampUpdateTest(1, 'learningmaterials', 'learningMaterial', $data);
    }

    public function testNewSessionLearningMaterialUpdatesSessionStamp()
    {
        $dataLoader = $this->container->get(SessionLearningMaterialData::class);
        $data = $dataLoader->create();
        $this->relatedTimeStampPostTest(1, 'sessionlearningmaterials', 'sessionLearningMaterials', $data);
    }

    public function testUpdatingSessionLearningMaterialUpdatesSessionStamp()
    {
        $dataLoader = $this->container->get(SessionLearningMaterialData::class);
        $data = $dataLoader->getOne();
        $data['required'] = !$data['required'];
        $this->relatedTimeStampUpdateTest(1, 'sessionlearningmaterials', 'sessionLearningMaterial', $data);
    }

    public function testDeletingSessionLearningMaterialUpdatesSessionStamp()
    {
        $dataLoader = $this->container->get(SessionLearningMaterialData::class);
        $data = $dataLoader->getOne();
        $this->relatedTimeStampDeleteTest(1, 'sessionlearningmaterials', $data['id']);
    }

    public function testDeletingSessionDescriptionUpdatesSessionStamp()
    {
        $dataLoader = $this->container->get(SessionDescriptionData::class);
        $data = $dataLoader->getOne();
        $this->relatedTimeStampDeleteTest($data['session'], 'sessiondescriptions', $data['id']);
    }

    public function testUpdatingSessionDescriptionUpdatesSessionStamp()
    {
        $dataLoader = $this->container->get(SessionDescriptionData::class);
        $data = $dataLoader->getOne();
        $data['description'] = 'new description';
        $this->relatedTimeStampUpdateTest($data['session'], 'sessiondescriptions', 'sessionDescription', $data);
    }

    public function testSendingNullForSessionDescription()
    {
        $dataLoader = $this->getDataLoader();
        $data = $dataLoader->create();
        $postData = $data;
        $postData['sessionDescription'] = null;
        $this->postTest($data, $postData);
    }
}
