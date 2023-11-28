<?php

declare(strict_types=1);

namespace App\Tests\Endpoints;

/**
 * Trait PostEndpointTestable
 * @package App\Tests
 */
trait PostEndpointTestable
{
    protected bool $enablePostTestsWithServiceToken = true;

    /**

     * @see PostEndpointTestInterface::testPostOne()
     */
    public function testPostOne(): void
    {
        $jwt = $this->createJwtForRootUser($this->kernelBrowser);
        $this->runPostTest($jwt);
    }

    /**
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
     * @see PostEndpointTestInterface::testPostBad()
     */
    public function testPostBad(): void
    {
        $jwt = $this->createJwtForRootUser($this->kernelBrowser);
        $this->runBadPostTest($jwt);
    }

    /**
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
     * @see PostEndpointTestInterface::testPostMany()
     */
    public function testPostMany(): void
    {
        $jwt = $this->createJwtForRootUser($this->kernelBrowser);
        $this->runPostManyTest($jwt);
    }

    /**
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
     * @see PostEndpointTestInterface::testPostOneJsonApi()
     */
    public function testPostOneJsonApi(): void
    {
        $jwt = $this->createJwtForRootUser($this->kernelBrowser);
        $this->runPostOneJsonApiTest($jwt);
    }

    /**
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
     * @see PostEndpointTestInterface::testPostManyJsonApi()
     */
    public function testPostManyJsonApi(): void
    {
        $jwt = $this->createJwtForRootUser($this->kernelBrowser);
        $this->runPostManyJsonApiTest($jwt);
    }

    /**
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
     * @see PostEndpointTestInterface::testPostAnonymousAccessDenied()
     */
    public function testPostAnonymousAccessDenied(): void
    {
        $dataLoader = $this->getDataLoader();
        $data = $dataLoader->create();
        $this->anonymousDeniedPostTest($data);
    }

    protected function runPostTest(string $jwt): void
    {
        $dataLoader = $this->getDataLoader();
        $data = $dataLoader->create();
        $postData = $data;
        $this->postTest($data, $postData, $jwt);
    }

    protected function runBadPostTest(string $jwt): void
    {
        $dataLoader = $this->getDataLoader();
        $data = $dataLoader->createInvalid();
        $this->badPostTest($data, $jwt);
    }

    protected function runPostManyTest(string $jwt): void
    {
        $dataLoader = $this->getDataLoader();
        $data = $dataLoader->createMany(51);
        $this->postManyTest($data, $jwt);
    }

    protected function runPostOneJsonApiTest(string $jwt): void
    {
        $dataLoader = $this->getDataLoader();
        $data = $dataLoader->create();
        $jsonApiData = $dataLoader->createJsonApi($data);
        $this->postJsonApiTest($jsonApiData, $data, $jwt);
    }

    protected function runPostManyJsonApiTest(string $jwt): void
    {
        $dataLoader = $this->getDataLoader();
        $data = $dataLoader->createMany(51);
        $jsonApiData = $dataLoader->createBulkJsonApi($data);
        $this->postManyJsonApiTest($jsonApiData, $data, $jwt);
    }
}
