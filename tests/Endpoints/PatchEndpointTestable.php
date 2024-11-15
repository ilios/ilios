<?php

declare(strict_types=1);

namespace App\Tests\Endpoints;

use PHPUnit\Framework\Attributes\DataProvider;

trait PatchEndpointTestable
{
    protected bool $enablePatchTestsWithServiceToken = true;

    abstract public static function putsToTest(): array;

    /**
     * @see PatchEndpointTestInterface::testPatchJsonApi()
     */
    #[DataProvider('putsToTest')]
    public function testPatchJsonApi(string $key, mixed $value): void
    {
        $jwt = $this->createJwtForRootUser($this->kernelBrowser);
        $this->runPatchJsonApiTest($key, $value, $jwt);
    }

    /**
     * @see PatchEndpointTestInterface::testPatchJsonApiWithServiceToken()
     */
    #[DataProvider('putsToTest')]
    public function testPatchJsonApiWithServiceToken(string $key, mixed $value): void
    {
        if (!$this->enablePatchTestsWithServiceToken) {
            $this->markTestSkipped('Patch to JSON:API test with service token skipped for this endpoint.');
        }
        $jwt = $this->createJwtFromServiceTokenWithWriteAccessInAllSchools($this->kernelBrowser, $this->fixtures);
        $this->runPatchJsonApiTest($key, $value, $jwt);
    }

    /**
     * @see PatchEndpointTestInterface::testPatchForAllDataJsonApi()
     */
    public function testPatchForAllDataJsonApi(): void
    {
        $jwt = $this->createJwtForRootUser($this->kernelBrowser);
        $this->runPatchForAllDataJsonApiTest($jwt);
    }

    /**
     * @see PatchEndpointTestInterface::testPatchForAllDataJsonApiWithServiceToken()
     */
    public function testPatchForAllDataJsonApiWithServiceToken(): void
    {
        if (!$this->enablePatchTestsWithServiceToken) {
            $this->markTestSkipped('Patch for all data to JSON:API test with service token skipped for this endpoint.');
        }
        $jwt = $this->createJwtFromServiceTokenWithWriteAccessInAllSchools($this->kernelBrowser, $this->fixtures);
        $this->runPatchForAllDataJsonApiTest($jwt);
    }


    /**
     * @see PatchEndpointTestInterface::testPatchAnonymousAccessDenied()
     */
    public function testPatchAnonymousAccessDenied(): void
    {
        $dataLoader = $this->getDataLoader();
        $data = $dataLoader->getOne();
        $this->anonymousDeniedPatchTest($data);
    }

    protected function runPatchForAllDataJsonApiTest(string $jwt): void
    {
        $putsToTest = $this->putsToTest();
        $firstPut = array_shift($putsToTest);
        $changeKey = $firstPut[0];
        $changeValue = $firstPut[1];
        $dataLoader = $this->getDataLoader();
        $all = $dataLoader->getAll();
        foreach ($all as $data) {
            $data[$changeKey] = $changeValue;
            $jsonApiData = $dataLoader->createJsonApi($data);

            $this->patchJsonApiTest($data, $jsonApiData, $jwt);
        }
    }

    protected function runPatchJsonApiTest(string $key, mixed $value, string $jwt): void
    {
        $dataLoader = $this->getDataLoader();
        $data = $dataLoader->getOne();
        $data[$key] = $value;
        $jsonApiData = $dataLoader->createJsonApi($data);

        //When we remove a value in a test we shouldn't expect it back
        if (null === $value) {
            unset($data[$key]);
        }
        $this->patchJsonApiTest($data, $jsonApiData, $jwt);
    }
}
