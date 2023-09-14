<?php

declare(strict_types=1);

namespace App\Tests\Endpoints;

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
