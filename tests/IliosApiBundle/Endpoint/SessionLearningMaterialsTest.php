<?php

namespace Tests\IliosApiBundle\Endpiont;

use Tests\IliosApiBundle\Endpoint\AbstractTest;

/**
 * SessionLearningMaterial controller Test.
 * @package Ilios\CoreBundle\Test\Controller;
 */
class SessionLearningMaterialsTest extends AbstractTest
{
    /**
     * @inheritdoc
     */
    protected function getFixtures()
    {
        return [
            'Tests\CoreBundle\Fixture\LoadSessionLearningMaterialData',
            'Tests\CoreBundle\Fixture\LoadSessionData',
            'Tests\CoreBundle\Fixture\LoadLearningMaterialData',
            'Tests\CoreBundle\Fixture\LoadMeshDescriptorData',
        ];
    }

    protected function getDataLoader()
    {
        return $this->container->get('ilioscore.dataloader.sessionlearningmaterial');
    }

    /**
     * @group api_1
     */
    public function testGetOneSessionLearningMaterial()
    {
        $this->getOneTest('sessionlearningmaterials');
    }

    /**
     * @group api_1
     */
    public function testGetAllSessionLearningMaterials()
    {
        $this->getAllTest('sessionlearningmaterials');
    }

    /**
     * @group api_1
     */
    public function testPostSessionLearningMaterial()
    {
        $dataLoader = $this->getDataLoader();
        $data = $dataLoader->create();
        $postData = $data;
        $this->postTest('sessionlearningmaterials', $data, $postData);
    }

    /**
     * @group api_1
     */
    public function testPostBadSessionLearningMaterial()
    {
        $dataLoader = $this->getDataLoader();
        $data = $dataLoader->createInvalid();
        $this->badPostTest('sessionlearningmaterials', $data);
    }

    /**
     * @group api_1
     */
    public function testPutSessionLearningMaterial()
    {
        $dataLoader = $this->getDataLoader();
        $data = $dataLoader->getOne();
        $data['notes'] = 'new';

        $postData = $data;
        $this->putTest('sessionlearningmaterials', $data, $postData);
    }

    /**
     * @group api_1
     */
    public function testDeleteSessionLearningMaterial()
    {
        $dataLoader = $this->getDataLoader();
        $data = $dataLoader->getOne();
        $this->deleteTest('sessionlearningmaterials', $data['id']);
    }

    /**
     * @group api_1
     */
    public function testSessionLearningMaterialNotFound()
    {
        $this->notFoundTest('sessionlearningmaterials', 99);
    }
}
