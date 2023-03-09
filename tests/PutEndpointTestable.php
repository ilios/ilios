<?php

declare(strict_types=1);

namespace App\Tests;

use PHPUnit\Framework\Attributes\DataProvider;

/**
 * Trait PutEndpointTestable
 * @package App\Tests
 */
trait PutEndpointTestable
{
    /**
     * @see PutEndpointTestInterface::testPut()
     */
    #[DataProvider('putsToTest')]
    public function testPut($key, $value, $skipped = false)
    {
        if ($skipped) {
            $this->markTestSkipped();
        }
        $dataLoader = $this->getDataLoader();
        $data = $dataLoader->getOne();
        if (array_key_exists($key, $data) and $data[$key] === $value) {
            $this->fail(
                "This value is already set for {$key}. " .
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
        $this->putTest($data, $postData, $id);
    }

    /**
     * @see PutEndpointTestInterface::testPutForAllData()
     */
    public function testPutForAllData()
    {
        $putsToTest = $this->putsToTest();
        $firstPut = array_shift($putsToTest);
        $changeKey = $firstPut[0];
        $changeValue = $firstPut[1];
        $dataLoader = $this->getDataLoader();
        $all = $dataLoader->getAll();
        foreach ($all as $data) {
            $data[$changeKey] = $changeValue;

            $this->putTest($data, $data, $data['id']);
        }
    }

    /**
     * @see PutEndpointTestInterface::testPutReadOnly()
     */
    #[DataProvider('readOnlyPropertiesToTest')]
    public function testPutReadOnly($key = null, $id = null, $value = null, $skipped = false)
    {
        if ($skipped) {
            $this->markTestSkipped();
        }
        if (
            null != $key &&
            null != $id &&
            null != $value
        ) {
            $dataLoader = $this->getDataLoader();
            $data = $dataLoader->getOne();
            if (array_key_exists($key, $data) and $data[$key] == $value) {
                $this->fail(
                    "This value is already set for {$key}. " .
                    "Modify " . $this::class . '::readOnlyPropertiesToTest'
                );
            }
            $postData = $data;
            $postData[$key] = $value;

            //nothing should change
            $this->putTest($data, $postData, $id);
        }
    }
}
