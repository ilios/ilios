<?php

namespace Tests\App;

/**
 * Interface PutEndpointTestInterface
 * @package Tests\AppBundle
 */
interface PutEndpointTestInterface
{
    /**
     * @return array [field, value]
     * field / value pairs to modify
     * field: readonly property name on the entity
     * value: something to set it to
     * the key for each item is reflected in the failure message
     * each one will be separately tested in a PUT request
     */
    public function putsToTest();

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
    public function readOnlyPropertiesToTest();

    /**
     * @param string $key
     * @param mixed $value
     * @param bool $skipped
     *
     * @dataProvider putsToTest
     */
    public function testPut($key, $value, $skipped = false);

    /**
     * Test PUTing each test data item to ensure
     * they all are saved as we would expect
     */
    public function testPutForAllData();

    /**
     * @param string|null $key
     * @param mixed|null $id
     * @param mixed|null $value
     * @param bool $skipped
     *
     * @dataProvider readOnlyPropertiesToTest
     */
    public function testPutReadOnly($key = null, $id = null, $value = null, $skipped = false);
}
