<?php

namespace Tests\IliosApiBundle;

/**
 * Trait PostEndpointTestable
 * @package Tests\IliosApiBundle
 */
trait PostEndpointTestable
{
    /**
     * @see PostEndpointTestInterface::testPostOne()
     */
    public function testPostOne()
    {
        $dataLoader = $this->getDataLoader();
        $data = $dataLoader->create();
        $postData = $data;
        $this->postTest($data, $postData);
    }

    /**
     * @see PostEndpointTestInterface::testPostBad()
     */
    public function testPostBad()
    {
        $dataLoader = $this->getDataLoader();
        $data = $dataLoader->createInvalid();
        $this->badPostTest($data);
    }

    /**
     * @see PostEndpointTestInterface::testPostMany()
     */
    public function testPostMany()
    {
        $dataLoader = $this->getDataLoader();
        $data = $dataLoader->createMany(51);
        $this->postManyTest($data);
    }
}
