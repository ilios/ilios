<?php
require_once dirname(__FILE__) . '/TestCase.php';

/**
 * Test case for the user-password utils.
 * @see Ilios_PasswordUtils
 */
class Ilios_PasswordUtilsTest extends Ilios_TestCase
{
    /**
     * Data provider function for <code>Ilios_JsonTest::testCheckPasswordStrength()</code>.
     * Returns a nested array of arrays, where in each sub-array
     * - the first element holds the password to be tested
     * - the second element holds the bitmask expected to be returned from the method under test.
     * @return array
     */
    public function providerTestCheckPasswordStrength ()
    {
        return array(
            array('th1Si$OK', Ilios_PasswordUtils::PASSWORD_STRENGTH_CHECK_OK),
            array('2Short_', Ilios_PasswordUtils::PASSWORD_STRENGTH_CHECK_TOO_SHORT),
            array('muchmuchmuch_T00long', Ilios_PasswordUtils::PASSWORD_STRENGTH_CHECK_TOO_LONG),
            array('too_long$_and_digit*Missing', Ilios_PasswordUtils::PASSWORD_STRENGTH_CHECK_TOO_LONG ^ Ilios_PasswordUtils::PASSWORD_STRENGTH_CHECK_DIGIT_MISSING),
            array('fail', Ilios_PasswordUtils::PASSWORD_STRENGTH_CHECK_TOO_SHORT ^ Ilios_PasswordUtils::PASSWORD_STRENGTH_CHECK_DIGIT_MISSING ^ Ilios_PasswordUtils::PASSWORD_STRENGTH_CHECK_UPPERCASE_CHAR_MISSING ^ Ilios_PasswordUtils::PASSWORD_STRENGTH_CHECK_SPECIAL_CHAR_MISSING),
            array('NoSpecia1Chr', Ilios_PasswordUtils::PASSWORD_STRENGTH_CHECK_SPECIAL_CHAR_MISSING),
            array('INv4lid*^Chr', Ilios_PasswordUtils::PASSWORD_STRENGTH_CHECK_INVALID_CHARS)
        );
    }

    /**
     * Data provider function for <code>Ilios_JsonTest::testHashPassword()</code>.
     * Returns a nested array of arrays, where in each sub-array
     * - the first element holds the password to be tested
     * - the second element holds the test salt
     * - the third elment holds the password hash expected to be returned from the method under test.
     * @return array
     */
    public function providerTestHashPassword ()
    {
        return array(
            array('$33m$_L3GiT', null, '43c1025d49e41618302dffa7adbf7b1d43a6e941fee1a1f0cdb67b75895609eb'),
            array('$33m$_L3GiT', 'trololol', 'f8e25ed0ff7dc135c2a9f84ae2e61187cb265cda8622bfa7789339c92fe0e5bc'),
            array('P4$$w0rd', null, '51c235b349dd4af59b9f2ae219cae37263dcf084599cb8537beec0cf19d8b82b'),
            array('P4$$w0rd', 'trololol', 'b91d0c271cc76f809b02d4b2d157c732a74409c460e1497480503e15bbb2f016')
        );
    }

    /**
     * @test
     * @covers Ilios_PasswordUtils::checkPasswordStrength
     * @dataProvider providerTestCheckPasswordStrength
     * @param string $password
     * @param int $expected
     * @group ilios2
     * @group authn
     */
    public function testCheckPasswordStrength ($password, $expected)
    {
        $actual = Ilios_PasswordUtils::checkPasswordStrength($password);
        $this->assertEquals($expected, $actual);
    }

    /**
     * @test
     * @covers Ilios_PasswordUtils::generateRandomPassword
     * @group ilios2
     * @group authn
     */
    public function testGenerateRandomPassword ()
    {
        // running this damned test one hundred times should be enough to prove the point
        for ($i = 0; $i < 100; $i++) {
            $password = Ilios_PasswordUtils::generateRandomPassword();
            $testResult = Ilios_PasswordUtils::checkPasswordStrength($password);
            $this->assertEquals($testResult, Ilios_PasswordUtils::PASSWORD_STRENGTH_CHECK_OK, "Password {$password} failed the strength test.");
        }
    }

    /**
     * @test
     * @covers Ilios_PasswordUtils::hashPassword
     * @dataProvider providerTestHashPassword
     * @param string $password
     * @param string|null $salt
     * @param string $expected
     * @group ilios2
     * @group authn
     */
    public function testHashPassword ($password, $salt, $expected)
    {
        $actual = Ilios_PasswordUtils::hashPassword($password, $salt);
        $this->assertEquals($actual, $expected);
    }
}
