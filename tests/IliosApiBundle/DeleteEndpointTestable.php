<?php

namespace Tests\IliosApiBundle;

/**
 * Trait DeleteEndpointTestable
 * @package Tests\IliosApiBundle
 */
trait DeleteEndpointTestable
{
    /**
     * @see DeleteEndpointTestInterface::testDelete
     */
    public function testDelete()
    {
        $dataLoader = $this->getDataLoader();
        $data = $dataLoader->getOne();
        $this->deleteTest($data['id']);
    }
}
