<?php

namespace Tests\IliosApiBundle\Endpiont;

use Tests\IliosApiBundle\Endpoint\AbstractTest;

/**
 * Session controller Test.
 * @package Ilios\CoreBundle\Test\Controller;
 */
class SessionsTest extends AbstractTest
{
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

    protected function getPluralName()
    {
        return 'sessions';
    }

    /**
     * @group api_1
     */
    public function testGetOne()
    {
        $this->getOneTest(['updatedAt']);
    }

    /**
     * @group api_1
     */
    public function testGetAll()
    {
        $this->getAllTest(['updatedAt']);
    }

    /**
     * @group api_1
     */
    public function testPost()
    {
        $dataLoader = $this->getDataLoader();
        $data = $dataLoader->create();
        $postData = $data;
        $this->postTest($data, $postData, ['updatedAt']);
    }

    /**
     * @group api_1
     */
    public function testPostBad()
    {
        $dataLoader = $this->getDataLoader();
        $data = $dataLoader->createInvalid();
        $this->badPostTest($data);
    }

    /**
     * @group api_1
     */
    public function testPut()
    {
        $dataLoader = $this->getDataLoader();
        $data = $dataLoader->getOne();
        $data['title'] = 'new';

        $postData = $data;
        $this->putTest($data, $postData, ['updatedAt']);
    }

    /**
     * @group api_1
     */
    public function testDelete()
    {
        $dataLoader = $this->getDataLoader();
        $data = $dataLoader->getOne();
        $this->deleteTest($data['id']);
    }

    /**
     * @group api_1
     */
    public function testNotFound()
    {
        $this->notFoundTest(99);
    }

    /**
     * @group api_1
     */
    public function testFilterByAttireRequired()
    {
        $dataLoader = $this->getDataLoader();
        $all = $dataLoader->getAll();
        $expectedData[] = $all[1];
        $filters = ['filters[attireRequired]' => true];
        $this->filterTest($filters, $expectedData, ['updatedAt']);
    }

    /**
     * @group api_1
     */
    public function testFilterBySupplemental()
    {
        $dataLoader = $this->getDataLoader();
        $all = $dataLoader->getAll();
        $expectedData[] = $all[1];
        $expectedData[] = $all[2];
        $filters = ['filters[supplemental]' => true];
        $this->filterTest($filters, $expectedData, ['updatedAt']);
    }

    /**
     * @group api_1
     */
    public function testFilterByIds()
    {
        $dataLoader = $this->getDataLoader();
        $all = $dataLoader->getAll();
        $expectedData[] = $all[0];
        $expectedData[] = $all[2];
        $filters = ['filters[id]' => [1,3]];
        $this->filterTest($filters, $expectedData, ['updatedAt']);
    }

    /**
     * @group api_1
     */
    public function testFilterBySupplementalAndAttireRequired()
    {
        $dataLoader = $this->getDataLoader();
        $all = $dataLoader->getAll();
        $expectedData[] = $all[1];
        $filters = ['filters[supplemental]' => true, 'filters[attireRequired]' => true];
        $this->filterTest($filters, $expectedData, ['updatedAt']);
    }

    /**
     * @group api_1
     */
    public function testFilterBySessionType()
    {
        $dataLoader = $this->getDataLoader();
        $all = $dataLoader->getAll();
        $expectedData[] = $all[1];
        $expectedData[] = $all[2];
        $expectedData[] = $all[3];
        $filters = ['filters[sessionType]' => 2];
        $this->filterTest($filters, $expectedData, ['updatedAt']);
    }

    /**
     * @group api_1
     */
    public function testFilterBySupplementalAndSessionType()
    {
        $dataLoader = $this->getDataLoader();
        $all = $dataLoader->getAll();
        $expectedData[] = $all[1];
        $expectedData[] = $all[2];
        $filters = ['filters[supplemental]' => true, 'filters[sessionType]' => 2];
        $this->filterTest($filters, $expectedData, ['updatedAt']);
    }

    /**
     * @group api_1
     */
    public function testFilterByTerm()
    {
        $dataLoader = $this->getDataLoader();
        $all = $dataLoader->getAll();
        $expectedData[] = $all[1];
        $filters = ['filters[terms]' => 1];
        $this->filterTest($filters, $expectedData, ['updatedAt']);
    }

    /**
     * @group api_1
     */
    public function testFilterByCourse()
    {
        $dataLoader = $this->getDataLoader();
        $all = $dataLoader->getAll();
        $expectedData[] = $all[3];
        $filters = ['filters[course]' => 4];
        $this->filterTest($filters, $expectedData, ['updatedAt']);
    }

    /**
     * @group api_1
     */
    public function testFilterByCourseMultiple()
    {
        $dataLoader = $this->getDataLoader();
        $all = $dataLoader->getAll();
        $expectedData[] = $all[0];
        $expectedData[] = $all[1];
        $expectedData[] = $all[3];
        $filters = ['filters[course]' => [1, 4]];
        $this->filterTest($filters, $expectedData, ['updatedAt']);
    }

    /**
     * @group api_1
     */
    public function testFilterByProgram()
    {
        $dataLoader = $this->getDataLoader();
        $all = $dataLoader->getAll();
        $expectedData[] = $all[3];
        $filters = ['filters[programs]' => [2]];
        $this->filterTest($filters, $expectedData, ['updatedAt']);
    }

    /**
     * @group api_1
     */
    public function testFilterByInstructor()
    {
        $dataLoader = $this->getDataLoader();
        $all = $dataLoader->getAll();
        $expectedData[] = $all[0];
        $expectedData[] = $all[1];
        $expectedData[] = $all[4];
        $expectedData[] = $all[5];
        $expectedData[] = $all[6];
        $filters = ['filters[instructors]' => [2]];
        $this->filterTest($filters, $expectedData, ['updatedAt']);
    }

    /**
     * @group api_1
     */
    public function testFilterByInstructorGroup()
    {
        $dataLoader = $this->getDataLoader();
        $all = $dataLoader->getAll();
        $expectedData[] = $all[0];
        $expectedData[] = $all[4];
        $filters = ['filters[instructorGroups]' => [1]];
        $this->filterTest($filters, $expectedData, ['updatedAt']);
    }

    /**
     * @group api_1
     */
    public function testFilterByLearningMaterial()
    {
        $dataLoader = $this->getDataLoader();
        $all = $dataLoader->getAll();
        $expectedData[] = $all[2];
        $filters = ['filters[learningMaterials]' => [3]];
        $this->filterTest($filters, $expectedData, ['updatedAt']);
    }

    /**
     * @group api_1
     */
    public function testFilterByCompetency()
    {
        $dataLoader = $this->getDataLoader();
        $all = $dataLoader->getAll();
        $expectedData[] = $all[0];
        $expectedData[] = $all[3];
        $filters = ['filters[competencies]' => [1]];
        $this->filterTest($filters, $expectedData, ['updatedAt']);
    }

    /**
     * @group api_1
     */
    public function testFilterByMeshDescriptor()
    {
        $dataLoader = $this->getDataLoader();
        $all = $dataLoader->getAll();
        $expectedData[] = $all[2];
        $expectedData[] = $all[3];
        $filters = ['filters[meshDescriptors]' => ['abc2', 'abc3']];
        $this->filterTest($filters, $expectedData, ['updatedAt']);
    }

    /**
     * @group api_1
     */
    public function testFilterBySchools()
    {
        $dataLoader = $this->getDataLoader();
        $all = $dataLoader->getAll();
        $expectedData[] = $all[3];
        $filters = ['filters[schools]' => [2]];
        $this->filterTest($filters, $expectedData, ['updatedAt']);
    }

    /**
     * @group api_1
     */
    public function testUpdatingIlmUpdatesSessionStamp()
    {
        $dataLoader = $this->container->get('ilioscore.dataloader.ilmsession');
        $data = $dataLoader->getOne();
        $data['instructors'] = ["1", "2"];
        $this->relatedTimeStampUpdateTest($data['session'], ['updatedAt'], 'ilmsessions', $data);
    }

    /**
     * @group api_1
     */
    public function testUpdatingIlmInstructorGroupsUpdatesSessionStamp()
    {
        $dataLoader = $this->container->get('ilioscore.dataloader.ilmsession');
        $data = $dataLoader->getOne();
        $data['instructors'] = ["1", "2"];
        $this->relatedTimeStampUpdateTest($data['session'], ['updatedAt'], 'ilmsessions', $data);
    }

    /**
     * @group api_1
     */
    public function testUpdatingIlmLearnerGroupsUpdatesSessionStamp()
    {
        $dataLoader = $this->container->get('ilioscore.dataloader.ilmsession');
        $data = $dataLoader->getOne();
        $data['learnerGroups'] = ["1", "2"];
        $this->relatedTimeStampUpdateTest($data['session'], ['updatedAt'], 'ilmsessions', $data);
    }

    /**
     * @group api_1
     */
    public function testUpdatingIlmLearnersUpdatesSessionStamp()
    {
        $dataLoader = $this->container->get('ilioscore.dataloader.ilmsession');
        $data = $dataLoader->getOne();
        $data['learners'] = ["1", "2"];
        $this->relatedTimeStampUpdateTest($data['session'], ['updatedAt'], 'ilmsessions', $data);
    }

    /**
     * @group api_1
     */
    public function testUpdatingLearningMaterialUpdatesSessionStamp()
    {
        $dataLoader = $this->container->get('ilioscore.dataloader.learningmaterial');
        $data = $dataLoader->getOne();
        $data['status'] = '1';
        $this->relatedTimeStampUpdateTest(1, ['updatedAt'], 'learningmaterials', $data);
    }

    /**
     * @group api_1
     */
    public function testNewSessionLearningMaterialUpdatesSessionStamp()
    {
        $dataLoader = $this->container->get('ilioscore.dataloader.sessionlearningmaterial');
        $data = $dataLoader->create();
        $this->relatedTimeStampPostTest(1, ['updatedAt'], 'sessionlearningmaterials', $data);
    }

    /**
     * @group api_1
     */
    public function testUpdatingSessionLearningMaterialUpdatesSessionStamp()
    {
        $dataLoader = $this->container->get('ilioscore.dataloader.sessionlearningmaterial');
        $data = $dataLoader->getOne();
        $data['required'] = !$data['required'];
        $this->relatedTimeStampUpdateTest(1, ['updatedAt'], 'sessionlearningmaterials', $data);
    }

    /**
     * @group api_1
     */
    public function testDeletingSessionLearningMaterialUpdatesSessionStamp()
    {
        $dataLoader = $this->container->get('ilioscore.dataloader.sessionlearningmaterial');
        $data = $dataLoader->getOne();
        $this->relatedTimeStampDeleteTest(1, ['updatedAt'], 'sessionlearningmaterials', $data['id']);
    }

    /**
     * @group api_1
     */
    public function testDeletingSessionDescriptionUpdatesSessionStamp()
    {
        $dataLoader = $this->container->get('ilioscore.dataloader.sessiondescription');
        $data = $dataLoader->getOne();
        $this->relatedTimeStampDeleteTest($data['session'], ['updatedAt'], 'sessiondescriptions', $data['id']);
    }

    /**
     * @group api_1
     */
    public function testUpdatingSessionDescriptionUpdatesSessionStamp()
    {
        $dataLoader = $this->container->get('ilioscore.dataloader.sessiondescription');
        $data = $dataLoader->getOne();
        $data['description'] = 'new description';
        $this->relatedTimeStampUpdateTest($data['session'], ['updatedAt'], 'sessiondescriptions', $data);
    }

}
