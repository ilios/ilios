<?php

namespace Tests\IliosApiBundle\Endpiont;

use Tests\IliosApiBundle\Endpoint\AbstractTest;

/**
 * Aamcmethod controller Test.
 * @package Ilios\CoreBundle\Test\Controller;
 */
class AamcmethodsTest extends AbstractTest
{
    /**
     * @inheritdoc
     */
    protected function getFixtures()
    {
        return [
            'Tests\CoreBundle\Fixture\LoadAamcMethodData',
            'Tests\CoreBundle\Fixture\LoadSessionTypeData'
        ];
    }

    protected function getDataLoader()
    {
        return $this->container->get('ilioscore.dataloader.aamcmethod');
    }

    /**
     * @group api_1
     */
    public function testGetOneAamcmethod()
    {
        $this->getOneTest('aamcmethods');
    }

    /**
     * @group api_1
     */
    public function testGetAllAamcmethods()
    {
        $this->getAllTest('aamcmethods');
    }

    /**
     * @group api_1
     */
    public function testPostAamcmethod()
    {
        $dataLoader = $this->getDataLoader();
        $data = $dataLoader->create();
        $postData = $data;
        $this->postTest('aamcmethods', $data, $postData);
    }

    /**
     * @group api_1
     */
    public function testPostBadAamcmethod()
    {
        $dataLoader = $this->getDataLoader();
        $data = $dataLoader->createInvalid();
        $this->badPostTest('aamcmethods', $data);
    }

    /**
     * @group api_1
     */
    public function testPutAamcmethod()
    {
        $dataLoader = $this->getDataLoader();
        $data = $dataLoader->getOne();
        $data['description'] = ['new'];

        $postData = $data;
        $this->putTest('aamcmethods', $data, $postData);
    }

    /**
     * @group api_1
     */
    public function testDeleteAamcmethod()
    {
        $dataLoader = $this->getDataLoader();
        $data = $dataLoader->getOne();
        $this->deleteTest('aamcmethods', $data['id']);
    }

    /**
     * @group api_1
     */
    public function testAamcmethodNotFound()
    {
        $this->notFoundTest('aamcmethods', 99);
    }

    /**
     * @group api_1
     */
    public function testFilterBySessionTypes()
    {
        $dataLoader = $this->getDataLoader();
        $all = $dataLoader->getAll();
        $expectedData[] = $all[0];
        $filters = ['filters[sessionTypes]' => [1]];
        $this->filterTest('aamcmethods', $filters, $expectedData);
    }
}
