<?php
require_once dirname(__FILE__) . '/TestCase.php';

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
     * @test
     * @covers Ilios_Json::testEncodeForJavascriptEmbedding
     * @dataProvider providerTestEncodeForJavascriptEmbedding
     * @param mixed $value
     * @param int $options
     * @param string $expected
     * @group ilios2
     * @group json
     */
    public function testEncodeForJavascriptEmbedding ($value, $options, $expected)
    {
        $actual = Ilios_Json::encodeForJavascriptEmbedding($value, $options);
        $this->assertEquals($expected, $actual);
    }
}
