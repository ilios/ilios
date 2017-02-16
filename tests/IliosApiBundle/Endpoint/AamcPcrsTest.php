<?php

namespace Tests\IliosApiBundle\Endpiont;

use Tests\IliosApiBundle\Endpoint\AbstractTest;

/**
 * AamcPcrsTest controller Test.
 * @package Ilios\CoreBundle\Test\Controller;
 */
class AamcPcrsTest extends AbstractTest
{
    /**
     * @inheritdoc
     */
    protected function getFixtures()
    {
        return [
            'Tests\CoreBundle\Fixture\LoadAamcPcrsData',
            'Tests\CoreBundle\Fixture\LoadCompetencyData'
        ];
    }

    protected function getDataLoader()
    {
        return $this->container->get('ilioscore.dataloader.aamcPcrs');
    }

    /**
     * @inheritDoc
     */
    protected function getPluralName()
    {
        return 'aamcpcrs';
    }

    protected function getSingular($pluralized)
    {
        return 'aamcpcrs';
    }

    /**
     * @group api_1
     */
    public function testGetOneAamcPcrs()
    {
        $this->getOneTest();
    }

    /**
     * @group api_1
     */
    public function testGetAllAamcPcrss()
    {
        $this->getAllTest();
    }

    /**
     * @group api_1
     */
    public function testPostAamcPcrs()
    {
        $dataLoader = $this->getDataLoader();
        $data = $dataLoader->create();
        $postData = $data;
        $this->postTest($data, $postData);
    }

    /**
     * @group api_1
     */
    public function testPostBadAamcPcrs()
    {
        $dataLoader = $this->getDataLoader();
        $data = $dataLoader->createInvalid();
        $this->badPostTest($data);
    }

    /**
     * @group api_1
     */
    public function testPutAamcPcrs()
    {
        $dataLoader = $this->getDataLoader();
        $data = $dataLoader->getOne();
        $data['description'] = 'new';

        $postData = $data;
        $this->putTest($data, $postData);
    }

    /**
     * @group api_1
     */
    public function testDeleteAamcPcrs()
    {
        $dataLoader = $this->getDataLoader();
        $data = $dataLoader->getOne();
        $this->deleteTest($data['id']);
    }

    /**
     * @group api_1
     */
    public function testAamcPcrsNotFound()
    {
        $this->notFoundTest(99);
    }

    /**
     * @group api_1
     */
    public function testPostCompetencyAamcPcrs()
    {
        $dataLoader = $this->getDataLoader();
        $data = $dataLoader->create();
        $postData = $data;
        $responseData = $this->postTest($data, $postData);

        $newId = $responseData['id'];
        foreach ($postData['competencies'] as $id) {
            $competency = $this->getOne('competencies', $id);
            $this->assertTrue(in_array($newId, $competency['aamcPcrses']));
        }
    }

    /**
     * @group api_1
     */
    public function testFilterByCompetencies()
    {
        $dataLoader = $this->getDataLoader();
        $all = $dataLoader->getAll();
        $expectedData[] = $all[0];
        $filters = ['filters[competencies]' => [1]];
        $this->filterTest($filters, $expectedData);
    }
}
