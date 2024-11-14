<?php

declare(strict_types=1);

namespace App\Tests\Endpoints;

/**
 * Trait PutEndpointTestable
 * @package App\Tests
 */
trait PutEndpointTestable
{
    protected bool $enablePutTestsWithServiceToken = true;

    abstract public static function putsToTest(): array;
    abstract public static function readOnlyPropertiesToTest(): array;

    /**
     * @see PutEndpointTestInterface::testPut()
     */
    #[\PHPUnit\Framework\Attributes\DataProvider('putsToTest')]
    public function testPut(string $key, mixed $value): void
    {
        $jwt = $this->createJwtForRootUser($this->kernelBrowser);
        $this->runPutTest($key, $value, $jwt);
    }

    /**
     * @see PutEndpointTestInterface::testPutWithServiceToken()
     */
    #[\PHPUnit\Framework\Attributes\DataProvider('putsToTest')]
    public function testPutWithServiceToken(string $key, mixed $value): void
    {
        if (!$this->enablePutTestsWithServiceToken) {
            $this->markTestSkipped('Put test with service token skipped for this endpoint.');
        }
        $jwt = $this->createJwtFromServiceTokenWithWriteAccessInAllSchools($this->kernelBrowser, $this->fixtures);
        $this->runPutTest($key, $value, $jwt);
    }

    /**
     * @see PutEndpointTestInterface::testPutForAllData()
     */
    public function testPutForAllData(): void
    {
        $jwt = $this->createJwtForRootUser($this->kernelBrowser);
        $this->runPutForAllDataTest($jwt);
    }

    /**
     * @see PutEndpointTestInterface::testPutForAllDataWithServiceToken()
     */
    public function testPutForAllDataWithServiceToken(): void
    {
        if (!$this->enablePutTestsWithServiceToken) {
            $this->markTestSkipped('Put for all data test with service token skipped for this endpoint.');
        }
        $jwt = $this->createJwtFromServiceTokenWithWriteAccessInAllSchools($this->kernelBrowser, $this->fixtures);
        $this->runPutForAllDataTest($jwt);
    }

    /**
     * @see PutEndpointTestInterface::testPutReadOnly()
     */
    #[\PHPUnit\Framework\Attributes\DataProvider('readOnlyPropertiesToTest')]
    public function testPutReadOnly(
        ?string $key = null,
        mixed $id = null,
        mixed $value = null,
        bool $skipped = false
    ): void {
        if ($skipped) {
            $this->markTestSkipped();
        }
        $jwt = $this->createJwtForRootUser($this->kernelBrowser);
        $this->runPutReadOnlyTest($jwt, $key, $id, $value);
    }

    /**
     * @see PutEndpointTestInterface::testPutReadOnlyWithServiceToken()
     */
    #[\PHPUnit\Framework\Attributes\DataProvider('readOnlyPropertiesToTest')]
    public function testPutReadOnlyWithServiceToken(
        ?string $key = null,
        mixed $id = null,
        mixed $value = null,
        bool $skipped = false
    ): void {
        if (!$this->enablePutTestsWithServiceToken) {
            $this->markTestSkipped('Put read only test with service token skipped for this endpoint.');
        }
        $jwt = $this->createJwtFromServiceTokenWithWriteAccessInAllSchools($this->kernelBrowser, $this->fixtures);
        $this->runPutReadOnlyTest($jwt, $key, $id, $value);
    }

    /**
     * @see PutEndpointTestInterface::testPutAnonymousAccessDenied()
     */
    public function testPutAnonymousAccessDenied(): void
    {
        $dataLoader = $this->getDataLoader();
        $data = $dataLoader->getOne();

        $this->anonymousDeniedPutTest($data);
    }

    protected function runPutTest(string $key, mixed $value, string $jwt): void
    {
        $dataLoader = $this->getDataLoader();
        $data = $dataLoader->getOne();
        if (array_key_exists($key, $data) and $data[$key] === $value) {
            $this->fail(
                "This value is already set for $key. " .
                "Modify " . $this::class . '::putsToTest'
            );
        }
        //extract the ID before changing anything in case
        // the key we are changing is the ID
        $id = $data['id'];
        $data[$key] = $value;

        $postData = $data;

        //When we remove a value in a test we shouldn't expect it back
        if (null === $value) {
            unset($data[$key]);
        }
        $this->putTest($data, $postData, $id, $jwt);
    }

    protected function runPutForAllDataTest(string $jwt): void
    {
        $putsToTest = $this->putsToTest();
        $firstPut = array_shift($putsToTest);
        $changeKey = $firstPut[0];
        $changeValue = $firstPut[1];
        $dataLoader = $this->getDataLoader();
        $all = $dataLoader->getAll();
        foreach ($all as $data) {
            $data[$changeKey] = $changeValue;
            $this->putTest($data, $data, $data['id'], $jwt);
        }
    }

    protected function runPutReadOnlyTest(
        string $jwt,
        ?string $key = null,
        mixed $id = null,
        mixed $value = null,
    ): void {
        if (
            null != $key &&
            null != $id &&
            null != $value
        ) {
            $dataLoader = $this->getDataLoader();
            $data = $dataLoader->getOne();
            if (array_key_exists($key, $data) and $data[$key] == $value) {
                $this->fail(
                    "This value is already set for $key. " .
                    "Modify " . $this::class . '::readOnlyPropertiesToTest'
                );
            }
            $postData = $data;
            $postData[$key] = $value;

            //nothing should change
            $this->putTest($data, $postData, $id, $jwt);
        }
    }
}
