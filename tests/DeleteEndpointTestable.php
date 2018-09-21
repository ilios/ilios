<?php

namespace App\Tests;

/**
 * Trait DeleteEndpointTestable
 * @package App\Tests
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
