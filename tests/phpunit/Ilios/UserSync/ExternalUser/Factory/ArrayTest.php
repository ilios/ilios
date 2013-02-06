<?php
require_once 'Ilios/TestCase.php';

/**
 * Test Case for the external user array factory.
 *
 * @see Ilios_UserSync_ExternalUser_Factory_Array
 */
class Ilios_UserSync_UserSource_Factory_ArrayTest extends Ilios_TestCase
{

    /**
     * Data provider function.
     * Returns a nested array of arrays, where in each sub-array
     * the first element represents a user record.
     * @return array
     */
    public function provider ()
    {
        $users = array(
            array(array()), // no user data whatsoever
            array(array( // empty props
            	'first_name' => null,
                'last_name' => null,
                'middle_name' => null,
                'email' => null,
                'phone' => null,
                'is_student' => null,
                'school_id' => null,
                'graduation_year' => null,
                'uid' => null
            )),
            array(array( // fully populated student record
            	'first_name' => 'student',
                'last_name' => 'a',
                'middle_name' => '',
                'email' => 'a.student@test.com',
                'phone' => '111-111-1111',
                'is_student' => true,
                'school_id' => 2,
                'graduation_year' => 2012,
                'uid' => '1234567890'
            )),
            array(array( // non-student
            	'first_name' => 'foo',
                'last_name' => 'bar',
                'middle_name' => 'z',
                'email' => 'foo.bar@test.com',
                'phone' => '111-555-1111',
                'is_student' => false,
                'school_id' => 7,
                'graduation_year' => 2022,
                'uid' => 'ucsf111111111'
            ))
        );

        return $users;
    }

    /**
     * @test
     * @covers Ilios_UserSync_ExternalUser_Factory_ArrayTest::createUser
     * @dataProvider provider
     * @group ilios
     * @group user_sync
     * @param array $user a nested array representing a user record
     */
    public function testCreateUser ($user)
    {
        $factory = new Ilios_UserSync_ExternalUser_Factory_Array();
        $externalUser = $factory->createUser($user);
        // 1. check the type of the returned object
        $this->assertTrue($externalUser instanceof Ilios_UserSync_ExternalUser);
        // 2. check user attributes
        $attributeNameToGetterFunctionNameMap = array( // ham-fisted mapping
        	'first_name' => 'getFirstName',
        	'last_name' => 'getLastName',
            'middle_name' => 'getMiddleName',
            'email' => 'getEmail',
            'is_student' => 'isStudent',
            'school_id' => 'getSchoolId',
            'graduation_year' => 'getGraduationYear',
            'uid' => 'getUid'
        );
        foreach ($attributeNameToGetterFunctionNameMap as $attrName => $fnName) {
            if (! array_key_exists($attrName, $user)) {
                $this->assertNull($externalUser->$fnName());
            } else {
                $this->assertEquals($user[$attrName], $externalUser->$fnName());
            }
        }
    }
}
