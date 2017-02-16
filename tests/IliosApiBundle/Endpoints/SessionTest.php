<?php

namespace Tests\IliosApiBundle\Endpoints;

/**
 * Session API endpoint Test.
 * @package Tests\IliosApiBundle\Endpoints
 * @group api_1
 */
class SessionTest extends AbstractTest
{
    protected $testName =  'session';

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
            'publishedAsTbd' => ['publishedAsTbd', true],
            'published' => ['published', false],
            'sessionType' => ['sessionType', 2],
            'course' => ['course', 2],
            'ilmSession' => ['ilmSession', 1],
            'terms' => ['terms', [1]],
            'objectives' => ['objectives', [1]],
//            'meshDescriptors' => ['meshDescriptors', [1]],
            'learningMaterials' => ['learningMaterials', [2]],
            'offerings' => ['offerings', [1]],
            'administrators' => ['administrators', [2]],
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
            'id' => [[0], ['filters[id]' => 1]],
            'ids' => [[0, 2], ['filters[id]' => [1, 3]]],
            'title' => [[0], ['filters[title]' => 'session1Title']],
            'attireRequired' => [[1], ['filters[attireRequired]' => true]],
            'equipmentRequired' => [[1], ['filters[equipmentRequired]' => true]],
            'supplemental' => [[1, 2], ['filters[supplemental]' => true]],
            'publishedAsTbd' => [[1], ['filters[publishedAsTbd]' => true]],
            'published' => [[0, 1], ['filters[published]' => true]],
            'sessionType' => [[1, 2, 3], ['filters[sessionType]' => 2]],
            'supplementalAndSessionType' => [[1, 2], ['filters[supplemental]' => true, 'filters[sessionType]' => 2]],
            'course' => [[3], ['filters[course]' => 4]],
            'multipeCourse' => [[0, 1, 3], ['filters[course]' => [1, 4]]],
//            'ilmSession' => [[0], ['filters[ilmSessions]' => [1]]],
            'terms' => [[1], ['filters[terms]' => 1]],
            'termsMultiple' => [[0], ['filters[terms]' => [2, 5]]],
//            'objectives' => [[0], ['filters[objectives]' => [1]]],
            'meshDescriptors' => [[2], ['filters[meshDescriptors]' => ['abc2']]],
            'meshDescriptorsMultiple' => [[2, 3], ['filters[meshDescriptors]' => ['abc2', 'abc3']]],
//            'sessionDescription' => [[0], ['filters[sessionDescription]' => 1]],
            'learningMaterials' => [[0], ['filters[learningMaterials]' => [1]]],
//            'offerings' => [[0], ['filters[offerings]' => 1]],
//            'administrators' => [[0], ['filters[administrators]' => [1]]],
            'supplementalAndAttireRequired' => [[1], ['filters[supplemental]' => true, 'filters[attireRequired]' => true]],
            'programs' => [[3], ['filters[programs]' => [2]]],
            'instructors' => [[0, 1, 4, 5, 6], ['filters[instructors]' => [2]]],
            'instructorGroups' => [[0, 4], ['filters[instructorGroups]' => [1]]],
            'competencies' => [[0, 3], ['filters[competencies]' => [1]]],
            'schools' => [[3], ['filters[schools]' => [2]]],
        ];
    }

    protected function getTimeStampFields()
    {
        return ['updatedAt'];
    }

    public function testUpdatingIlmUpdatesSessionStamp()
    {
        $dataLoader = $this->container->get('ilioscore.dataloader.ilmsession');
        $data = $dataLoader->getOne();
        $data['instructors'] = ["1", "2"];
        $this->relatedTimeStampUpdateTest($data['session'], ['updatedAt'], 'ilmsessions', $data);
    }

    public function testUpdatingIlmInstructorGroupsUpdatesSessionStamp()
    {
        $dataLoader = $this->container->get('ilioscore.dataloader.ilmsession');
        $data = $dataLoader->getOne();
        $data['instructors'] = ["1", "2"];
        $this->relatedTimeStampUpdateTest($data['session'], ['updatedAt'], 'ilmsessions', $data);
    }

    public function testUpdatingIlmLearnerGroupsUpdatesSessionStamp()
    {
        $dataLoader = $this->container->get('ilioscore.dataloader.ilmsession');
        $data = $dataLoader->getOne();
        $data['learnerGroups'] = ["1", "2"];
        $this->relatedTimeStampUpdateTest($data['session'], ['updatedAt'], 'ilmsessions', $data);
    }

    public function testUpdatingIlmLearnersUpdatesSessionStamp()
    {
        $dataLoader = $this->container->get('ilioscore.dataloader.ilmsession');
        $data = $dataLoader->getOne();
        $data['learners'] = ["1", "2"];
        $this->relatedTimeStampUpdateTest($data['session'], ['updatedAt'], 'ilmsessions', $data);
    }

    public function testUpdatingLearningMaterialUpdatesSessionStamp()
    {
        $dataLoader = $this->container->get('ilioscore.dataloader.learningmaterial');
        $data = $dataLoader->getOne();
        $data['status'] = '1';
        $this->relatedTimeStampUpdateTest(1, ['updatedAt'], 'learningmaterials', $data);
    }

    public function testNewSessionLearningMaterialUpdatesSessionStamp()
    {
        $dataLoader = $this->container->get('ilioscore.dataloader.sessionlearningmaterial');
        $data = $dataLoader->create();
        $this->relatedTimeStampPostTest(1, ['updatedAt'], 'sessionlearningmaterials', $data);
    }

    public function testUpdatingSessionLearningMaterialUpdatesSessionStamp()
    {
        $dataLoader = $this->container->get('ilioscore.dataloader.sessionlearningmaterial');
        $data = $dataLoader->getOne();
        $data['required'] = !$data['required'];
        $this->relatedTimeStampUpdateTest(1, ['updatedAt'], 'sessionlearningmaterials', $data);
    }

    public function testDeletingSessionLearningMaterialUpdatesSessionStamp()
    {
        $dataLoader = $this->container->get('ilioscore.dataloader.sessionlearningmaterial');
        $data = $dataLoader->getOne();
        $this->relatedTimeStampDeleteTest(1, ['updatedAt'], 'sessionlearningmaterials', $data['id']);
    }

    public function testDeletingSessionDescriptionUpdatesSessionStamp()
    {
        $dataLoader = $this->container->get('ilioscore.dataloader.sessiondescription');
        $data = $dataLoader->getOne();
        $this->relatedTimeStampDeleteTest($data['session'], ['updatedAt'], 'sessiondescriptions', $data['id']);
    }

    public function testUpdatingSessionDescriptionUpdatesSessionStamp()
    {
        $dataLoader = $this->container->get('ilioscore.dataloader.sessiondescription');
        $data = $dataLoader->getOne();
        $data['description'] = 'new description';
        $this->relatedTimeStampUpdateTest($data['session'], ['updatedAt'], 'sessiondescriptions', $data);
    }

}