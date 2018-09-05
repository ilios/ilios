<?php

namespace Tests\AppBundle;

/**
 * Class ReadWriteEndpointTest
 * @package Tests\AppBundle
 */
abstract class ReadWriteEndpointTest extends ReadEndpointTest
{
    /**
     * @return array [field, value]
     * field / value pairs to modify
     * field: readonly property name on the entity
     * value: something to set it to
     * the key for each item is reflected in the failure message
     * each one will be separately tested in a PUT request
     */
    abstract public function putsToTest();

    /**
     * @return array [field, value, id]
     *
     * field / value / id sets that are readOnly
     * field: readonly property name on the entity
     * value: something to set it to
     * id: the ID of the object we want to test.  The has to be provided separately
     * because we can't extract it from the $data without invalidating this test
     *
     * the key for each item is reflected in the failure message
     * each one will be separately tested in a PUT request
     */
    abstract public function readOnlyPropertiesToTest();

    /**
     * Test posting a single object
     */
    public function testPostOne()
    {
        $dataLoader = $this->getDataLoader();
        $data = $dataLoader->create();
        $postData = $data;
        $this->postTest($data, $postData);
    }

    /**
     * Test a failure when posting an object
     */
    public function testPostBad()
    {
        $dataLoader = $this->getDataLoader();
        $data = $dataLoader->createInvalid();
        $this->badPostTest($data);
    }

    /**
     * Test POST several of this type of object
     */
    public function testPostMany()
    {
        $dataLoader = $this->getDataLoader();
        $data = $dataLoader->createMany(51);
        $this->postManyTest($data);
    }

    /**
     * @param string $key
     * @param mixed $value
     * @param bool $skipped
     *
     * @dataProvider putsToTest
     */
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
                "Modify " . get_class($this) . '::putsToTest'
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
     * Test PUTing each test data item to ensure
     * they all are saved as we would expect
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
     * @param string|null $key
     * @param mixed|null $id
     * @param mixed|null $value
     * @param bool $skipped
     *
     * @dataProvider readOnlyPropertiesToTest
     */
    public function testPutReadOnly($key = null, $id = null, $value = null, $skipped = false)
    {
        if ($skipped) {
            $this->markTestSkipped();
        }
        if (null != $key &&
            null != $id &&
            null != $value
        ) {
            $dataLoader = $this->getDataLoader();
            $data = $dataLoader->getOne();
            if (array_key_exists($key, $data) and $data[$key] == $value) {
                $this->fail(
                    "This value is already set for {$key}. " .
                    "Modify " . get_class($this) . '::readOnlyPropertiesToTest'
                );
            }
            $postData = $data;
            $postData[$key] = $value;

            //nothing should change
            $this->putTest($data, $postData, $id);
        }
    }

    /**
     * Test deleting data
     */
    public function testDelete()
    {
        $dataLoader = $this->getDataLoader();
        $data = $dataLoader->getOne();
        $this->deleteTest($data['id']);
    }
}
