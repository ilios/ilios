<?php
require_once 'Ilios/TestCase.php';

/**
 * Test case for the JSON utils.
 * @see Ilios_JSON
 */
class Ilios_JsonTest extends Ilios_TestCase
{
    /**
     * Data provider function for <code>Ilios_JsonTest::testEncodeForJavascriptEmbedding()</code>.
     * Returns a nested array of arrays, where in each sub-array
     * - the first element holds the test value
     * - the second argument hold the bitmask of JS-escaping options
     * - the third element hold the expected JSON-encoded/JS-escaped representation of the first element.
     * @return array
     */
    public function providerTestEncodeForJavascriptEmbedding ()
    {
        return array(
            array(array("foo" => "bar"), 0, '{"foo":"bar"}'),
            array(array("foo" => "bar\nbaz"), 0, '{"foo":"bar\\\nbaz"}'),
            array(array("foo" => "bar\r\nbaz"), 0, '{"foo":"bar\\\r\\\nbaz"}'),
            array(array("foo" => "bar"), Ilios_Json::JSON_ENC_SINGLE_QUOTES, '{"foo":"bar"}'),
            array(array("foo" => "'bar'"), Ilios_Json::JSON_ENC_SINGLE_QUOTES, '{"foo":"\\\'bar\\\'"}'),
            array(array("foo" => '"bar"'), Ilios_Json::JSON_ENC_SINGLE_QUOTES, '{"foo":"\\\"bar\\\""}'),
            array(array("foo" => "bar"), Ilios_Json::JSON_ENC_DOUBLE_QUOTES, '{\"foo\":\"bar\"}'),
            array(array("foo" => "'bar'"), Ilios_Json::JSON_ENC_DOUBLE_QUOTES, '{\"foo\":\"\'bar\'\"}'),
            array(array("foo" => '"bar"'), Ilios_Json::JSON_ENC_DOUBLE_QUOTES, '{\"foo\":\"\\\\\"bar\\\\\"\"}')
        );
    }

    /**
     * Data provider function for <code>Ilios_JsonTest::testDeserializeJsonArray()</code>.
     * Returns a nested array of arrays, where in each sub-array
     * - the first element holds the JSON-serialized test value
     * - the second, third and fourth arguments hold function arguments 2-4 for Ilios_Json::deserializeJsonArray()
     * - the fifth element hold the expected deserialized array.
     * @return array
     */
    public function providerTestDeserializeJsonArray ()
    {
        return array(
            array('[]', false, true, true, array()),
            array('["foo", "bar"]', false, true, true, array("foo", "bar")),
        );
    }

    /**
     * @test
     * @covers Ilios_Json::testEncodeForJavascriptEmbedding
     * @dataProvider providerTestEncodeForJavascriptEmbedding
     * @param mixed $value
     * @param int $options
     * @param string $expected
     * @group ilios
     * @group json
     */
    public function testEncodeForJavascriptEmbedding ($value, $options, $expected)
    {
        $actual = Ilios_Json::encodeForJavascriptEmbedding($value, $options);
        $this->assertEquals($expected, $actual);
    }

    /**
     * @test
     * @covers Ilios_Json::deserializeJsonArray
     * @dataProvider providerTestDeserializeJsonArray
     * @param string $json
     * @param boolean $assoc
     * @param boolean $convertToUtf8
     * @param boolean $utf8urlDecode
     * $param array $expected
     * @group ilios
     * @group json
     */
    public function testDeserializeJsonArray ($json, $assoc, $convertToUtf8, $utf8urlDecode, $expected)
    {
        $this->assertEquals(Ilios_Json::deserializeJson($json, $assoc, $convertToUtf8, $utf8urlDecode), $expected);
    }

    /**
     * @test
     * @covers Ilios_Json::deserializeJsonArray
     * @expectedException Ilios_Exception
     * @group ilios
     * @group json
     */
    public function testDeserializeJsonArrayTypeMismatchFailure ()
    {
        Ilios_Json::deserializeJsonArray('"deserializes not as an array but as string"');
    }

    /**
     * @test
     * @covers Ilios_Json::deserializeJsonArray
     * @expectedException Ilios_Exception
     * @group ilios
     * @group json
     */
    public function testDeserializeJsonArrayDecodingFailure ()
    {
        Ilios_Json::deserializeJsonArray('["missing_closing_bracket"');
    }
}
