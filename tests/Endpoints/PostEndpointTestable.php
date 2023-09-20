<?php

declare(strict_types=1);

namespace App\Tests\Endpoints;

use Exception;

/**
 * Trait PostEndpointTestable
 * @package App\Tests
 */
trait PostEndpointTestable
{
    protected bool $enablePostTestsWithServiceToken = true;

    /**
     * @throws Exception
     * @see PostEndpointTestInterface::testPostOne()
     */
    public function testPostOne(): void
    {
        $jwt = $this->createJwtForRootUser($this->kernelBrowser);
        $this->runPostTest($jwt);
    }

    /**
     * @throws Exception
     * @see PostEndpointTestInterface::testPostOneWithServiceToken()
     */
    public function testPostOneWithServiceToken(): void
    {
        if (!$this->enablePostTestsWithServiceToken) {
            $this->markTestSkipped('Post one test with service token skipped for this endpoint.');
        }
        $jwt = $this->createJwtFromServiceTokenWithWriteAccessInAllSchools($this->kernelBrowser, $this->fixtures);
        $this->runPostTest($jwt);
    }

    /**
     * @throws Exception
     * @see PostEndpointTestInterface::testPostBad()
     */
    public function testPostBad(): void
    {
        $jwt = $this->createJwtForRootUser($this->kernelBrowser);
        $this->runBadPostTest($jwt);
    }

    /**
     * @throws Exception
     * @see PostEndpointTestInterface::testPostBadWithServiceToken()
     */
    public function testPostBadWithServiceToken(): void
    {
        if (!$this->enablePostTestsWithServiceToken) {
            $this->markTestSkipped('Bad post test with service token skipped for this endpoint.');
        }
        $jwt = $this->createJwtFromServiceTokenWithWriteAccessInAllSchools($this->kernelBrowser, $this->fixtures);
        $this->runBadPostTest($jwt);
    }

    /**
     * @throws Exception
     * @see PostEndpointTestInterface::testPostMany()
     */
    public function testPostMany(): void
    {
        $jwt = $this->createJwtForRootUser($this->kernelBrowser);
        $this->runPostManyTest($jwt);
    }

    /**
     * @throws Exception
     * @see PostEndpointTestInterface::testPostManyWithServiceToken()
     */
    public function testPostManyWithServiceToken(): void
    {
        if (!$this->enablePostTestsWithServiceToken) {
            $this->markTestSkipped('Post many test with service token skipped for this endpoint.');
        }
        $jwt = $this->createJwtFromServiceTokenWithWriteAccessInAllSchools($this->kernelBrowser, $this->fixtures);
        $this->runPostManyTest($jwt);
    }

    /**
     * @throws Exception
     * @see PostEndpointTestInterface::testPostOneJsonApi()
     */
    public function testPostOneJsonApi(): void
    {
        $jwt = $this->createJwtForRootUser($this->kernelBrowser);
        $this->runPostOneJsonApiTest($jwt);
    }

    /**
     * @throws Exception
     * @see PostEndpointTestInterface::testPostOneJsonApiWithServiceToken()
     */
    public function testPostOneJsonApiWithServiceToken(): void
    {
        if (!$this->enablePostTestsWithServiceToken) {
            $this->markTestSkipped('Post one to JSON:API test with service token skipped for this endpoint.');
        }
        $jwt = $this->createJwtFromServiceTokenWithWriteAccessInAllSchools($this->kernelBrowser, $this->fixtures);
        $this->runPostOneJsonApiTest($jwt);
    }

    /**
     * @throws Exception
     * @see PostEndpointTestInterface::testPostManyJsonApi()
     */
    public function testPostManyJsonApi(): void
    {
        $jwt = $this->createJwtForRootUser($this->kernelBrowser);
        $this->runPostManyJsonApiTest($jwt);
    }

    /**
     * @throws Exception
     * @see PostEndpointTestInterface::testPostManyJsonApiWithServiceToken()
     */
    public function testPostManyJsonApiWithServiceToken(): void
    {
        if (!$this->enablePostTestsWithServiceToken) {
            $this->markTestSkipped('Post many to JSON:API test with service token skipped for this endpoint.');
        }
        $jwt = $this->createJwtFromServiceTokenWithWriteAccessInAllSchools($this->kernelBrowser, $this->fixtures);
        $this->runPostManyJsonApiTest($jwt);
    }

    /**
     * @throws Exception
     * @see PostEndpointTestInterface::testPostAnonymousAccessDenied()
     */
    public function testPostAnonymousAccessDenied(): void
    {
        $dataLoader = $this->getDataLoader();
        $data = $dataLoader->create();
        $this->anonymousDeniedPostTest($data);
    }

    /**
     * @throws Exception
     */
    protected function runPostTest(string $jwt): void
    {
        $dataLoader = $this->getDataLoader();
        $data = $dataLoader->create();
        $postData = $data;
        $this->postTest($data, $postData, $jwt);
    }

    /**
     * @throws Exception
     */
    protected function runBadPostTest(string $jwt): void
    {
        $dataLoader = $this->getDataLoader();
        $data = $dataLoader->createInvalid();
        $this->badPostTest($data, $jwt);
    }

    /**
     * @throws Exception
     */
    protected function runPostManyTest(string $jwt): void
    {
        $dataLoader = $this->getDataLoader();
        $data = $dataLoader->createMany(51);
        $this->postManyTest($data, $jwt);
    }

    /**
     * @throws Exception
     */
    protected function runPostOneJsonApiTest(string $jwt): void
    {
        $dataLoader = $this->getDataLoader();
        $data = $dataLoader->create();
        $jsonApiData = $dataLoader->createJsonApi($data);
        $this->postJsonApiTest($jsonApiData, $data, $jwt);
    }

    /**
     * @throws Exception
     */
    protected function runPostManyJsonApiTest(string $jwt): void
    {
        $dataLoader = $this->getDataLoader();
        $data = $dataLoader->createMany(51);
        $jsonApiData = $dataLoader->createBulkJsonApi($data);
        $this->postManyJsonApiTest($jsonApiData, $data, $jwt);
    }
}
