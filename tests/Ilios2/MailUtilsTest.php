<?php
require_once dirname(__FILE__) . '/TestCase.php';

/**
 * Test case for the mail utils.
 * @see Ilios2_MailUtils
 */
class Ilios2_MailUtilsTest extends Ilios2_TestCase
{
    /**
     * Data provider function for <code>Ilios2_MailUtilsTest::testImplodeListForMail()</code>.
     * Returns a nested array of arrays, where in each sub-array
     * - the first, second and third elements hold input to the function under test.
     * - the fourth element holds the expected output from the function under test.
     * @return array
     */
    public function providerTestImplodeListForMail ()
    {
        return array(
            array(array(), ',', 100,  ''),
            array(array('foo', 'bar'), ',', 100, 'foo,bar'),
            array(array('abcde', 'abcde', 'abcde'), ';', 12, 'abcde;abcde;' . PHP_EOL . 'abcde'),
            array(array('abcde', 'abcde', 'abcde'), '-', 10, 'abcde-' . PHP_EOL . 'abcde-' . PHP_EOL . 'abcde'),
            array(array('1234567', '8', '90'), ',', 8, '1234567,' . PHP_EOL . '8,90'),
            array(array('1234567', '8', '90'), ',', 9, '1234567,' . PHP_EOL . '8,90'),
            array(array('1234567', '8', '90'), ',', 10, '1234567,8,' . PHP_EOL . '90')
        );
    }

    /**
     * @test
     * @covers Ilios2_MailUtils::implodeListForMail()
     * @dataProvider providerTestImplodeListForMail
     * @param array $list test input to function under test
     * @param string $separator test input to function under test
     * @param $lineLengthLimit test input to function under test
     * @param $expected expected output from function under test
     * @see Ilios2_MailUtils::implodeListForMail()
     * @group ilios2
     * @group mail
     */
    public function testImplodeListForMail (array $list, $separator, $lineLengthLimit, $expected)
    {
        $this->assertEquals($expected, Ilios2_MailUtils::implodeListForMail($list, $separator, $lineLengthLimit));
    }
}
