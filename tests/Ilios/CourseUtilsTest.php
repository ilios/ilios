<?php
require_once dirname(__FILE__) . '/TestCase.php';

/**
 * Test case for the course utils.
 * @see Ilios_CourseUtils
 */
class Ilios_CourseUtilsTest extends Ilios_TestCase
{
    /**
     * Data provider function for <code>Ilios_CourseUtilsTest::testGenerateHashFromCourseId</code>.
     * Returns a nested array of arrays, where in each sub-array
     * - the first element holds the course id as test input.
     * - the second element hold the expected generated hash.
     * @return array
     */
    public function providerTestGenerateHashFromCourseId ()
    {
        return array(
            array(0, 'ILIOS0'),
            array(10, 'ILIOSLFLS'),
            array(500, 'ILIOSTRO8W'),
            array(20000, 'ILIOSX2QXVK'),
            array(100000, 'ILIOS4LDQPDS'),
            array(1234567890, 'ILIOS17RF9KLZK0'),
        );
    }


    /**
     * Data provider function for <code>Ilios_CourseUtilsTest::testExtractCourseIdFromHash</code>.
     * Returns a nested array of arrays, where in each sub-array
     * - the first element holds expected extracted course id
     * - the second element hold the hash as test input
     * @return array
     */
    public function providerTestExtractCourseIdFromHash ()
    {
        return  $this->providerTestGenerateHashFromCourseId();
    }

    /**
     * @test
     * @covers Ilios_CourseUtils::generateHashFromCourseId()
     * @dataProvider providerTestGenerateHashFromCourseId
     * @param int $courseId test input to function under test
     * @param $expected expected output from function under test
     * @see Ilios_CourseUtils::generateHashFromCourseId()
     * @group ilios2
     * @group course
     */
    public function testGenerateHashFromCourseId ($courseId, $expected)
    {
        $this->assertEquals($expected, Ilios_CourseUtils::generateHashFromCourseId($courseId));
    }

    /**
     * @test
     * @covers Ilios_CourseUtils::extractCourseIdFromHash()
     * @dataProvider providerTestExtractCourseIdFromHash
     * @param $expected expected output from function under test
     * @param string $hash test input to function under test
     * @see Ilios_CourseUtils::extractCourseIdFromHash()
     * @group ilios2
     * @group course
     */
    public function testExtractCourseIdFromHash ($expected, $hash)
    {
    	$this->assertEquals($expected, Ilios_CourseUtils::extractCourseIdFromHash($hash));
    }
}
