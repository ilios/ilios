<?php

declare(strict_types=1);

namespace App\Tests\Endpoints;

use Exception;

/**
 * Trait DeleteEndpointTestable
 * @package App\Tests
 */
trait DeleteEndpointTestable
{
    protected bool $enableDeleteTestsWithServiceToken = true;

    /**
     * @throws Exception
     * @see DeleteEndpointTestInterface::testDelete
     */
    public function testDelete(): void
    {
        $jwt = $this->createJwtForRootUser($this->kernelBrowser);
        $this->runDeleteTest($jwt);
    }

    /**
     * @throws Exception
     * @see DeleteEndpointTestInterface::testDeleteWithServiceToken
     */
    public function testDeleteWithServiceToken(): void
    {
        if (!$this->enableDeleteTestsWithServiceToken) {
            $this->markTestSkipped('Delete test with service token skipped for this endpoint.');
        }
        $jwt = $this->createJwtFromServiceTokenWithWriteAccessInAllSchools($this->kernelBrowser, $this->fixtures);
        $this->runDeleteTest($jwt);
    }

    /**
     * @throws Exception
     */
    protected function runDeleteTest(string $jwt): void
    {
        $dataLoader = $this->getDataLoader();
        $data = $dataLoader->getOne();
        $this->deleteTest($data['id'], $jwt);
    }
}
