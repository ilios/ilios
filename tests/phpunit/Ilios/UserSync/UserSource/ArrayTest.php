<?php
require_once 'Ilios/TestCase.php';

/**
 * Test case for the external user source client using arrays as data source.
 * @see Ilios_UserSync_UserSource_Array
 */
class Ilios_UserSync_UserSource_ArrayTest extends Ilios_TestCase
{
    /**
     * user data
     * @var array
     */
    protected $_fixtures = array(
        // student records
        'students' => array(
            array(
                'first_name' => 'student',
                'last_name' => 'a',
                'middle_name' => '',
                'email' => 'a.student@test.com',
                'phone' => '111-111-1111',
                'uid' => 'test111111',
                'is_student' => true
            ),
            array(
                'first_name' => 'student',
                'last_name' => 'b',
                'middle_name' => '',
                'email' => 'b.student@test.com',
                'phone' => '111-111-1112',
                'uid' => 'test111112',
                'is_student' => true
            ),
            array(
                'first_name' => 'STUDENT',
                'last_name' => 'DUPLICATE',
                'middle_name' => 'g',
                'email' => 'DUPLICATE.STUDENT@TEST.COM',
                'phone' => '111-111-3333',
                'uid' => 'TEST111333',
                'is_student' => true
            ),
            array(
                'first_name' => 'student',
                'last_name' => 'duplicate',
                'middle_name' => 'g',
                'email' => 'duplicate.student@test.com',
                'phone' => '111-111-3333',
                'uid' => 'TEST111333',
                'is_student' => true
            )
        ),
        // non-student records
        'non_students' => array(
            array(
                'first_name' => 'nonstudent',
                'last_name' => 'a',
                'middle_name' => '',
                'email' => 'a.nonstudent@test.com',
                'phone' => '222-222-2222',
                'uid' => 'test222222',
                'is_student' => false
            ),
            array(
                'first_name' => 'nonstudent',
                'last_name' => 'b',
                'middle_name' => '',
                'email' => 'b.nonstudent@test.com',
                'phone' => '222-222-2223',
                'uid' => 'test222223',
                'is_student' => false
            )
        )
    );

    /**
     * Data provider function.
     * Returns a nested array of arrays, where in each sub-array
     * * the first element hold the total count of student records given within the second element
     * * the second element holds a nested array of arrays, where each sub-array represents a user record
     * @return array
     */
    public function providerGetAllStudentRecords ()
    {
        return array(
            array(0, array()),
            array(0, $this->_fixtures['non_students']),
            array(count($this->_fixtures['students']), $this->_fixtures['students']),
            array(count($this->_fixtures['students']), array_merge($this->_fixtures['students'], $this->_fixtures['non_students']))
        );
    }

    /**
     * @test
     * @expectedException Ilios_UserSync_Exception
     * @covers Canned_Queries::__construct
     * @group ilios
     * @group usersync
     */
    public function testConstructorWithMissingUserData ()
    {
        // this should throw an exception
        $userSource = new Ilios_UserSync_UserSource_Array(array());
    }

    /**
     * @test
     * @covers Ilios_UserSync_UserSource_Array::getAllStudentRecords
     * @dataProvider providerGetAllStudentRecords
     * @group ilios
     * @group usersync
     */
    public function testGetAllStudentRecords ($studentCount, $users)
    {
        $config = array();
        $config['array']['users'] = $users;
        $userSource = new Ilios_UserSync_UserSource_Array($config);
        $students = $userSource->getAllStudentRecords();
        $this->assertEquals($studentCount, count($students));
    }

    /**
     * @test
     * @covers Ilios_UserSync_UserSource_Array::getUserByEmail
     * @group ilios
     * @group usersync
     */
    public function testGetUserByEmail ()
    {
        $config = array();
        $config['array']['users'] = $this->_fixtures['students'];
        $userSource = new Ilios_UserSync_UserSource_Array($config);

        // user not found
        $users = $userSource->getUserByEmail('doesnotexist@test.com');
        $this->assertTrue($users instanceof Ilios_UserSync_ExternalUser_Iterator_Array);
        $this->assertEquals(0, count($users));

        // user exists - check email
        $users = $userSource->getUserByEmail('a.student@test.com');
        $this->assertTrue($users instanceof Ilios_UserSync_ExternalUser_Iterator_Array);
        $this->assertEquals(1, count($users));

        // check again with different case
        $users = $userSource->getUserByEmail('A.Student@Test.Com');
        $this->assertTrue($users instanceof Ilios_UserSync_ExternalUser_Iterator_Array);
        $this->assertEquals(1, count($users));

        // check duplicate user - multiple records should be returned
        $users = $userSource->getUserByEmail('duplicate.student@test.com');
        $this->assertTrue($users instanceof Ilios_UserSync_ExternalUser_Iterator_Array);
        $this->assertEquals(2, count($users));
    }


    /**
     * @test
     * @covers Ilios_UserSync_UserSource_Array::getUserByUid
     * @group ilios
     * @group usersync
     */
    public function testGetUserByUid ()
    {
        $config = array();
        $config['array']['users'] = $this->_fixtures['students'];
        $userSource = new Ilios_UserSync_UserSource_Array($config);

        // user not found
        $users = $userSource->getUserByUid('XXXXXXXXX');
        $this->assertTrue($users instanceof Ilios_UserSync_ExternalUser_Iterator_Array);
        $this->assertEquals(0, count($users));

        // user exists
        $users = $userSource->getUserByUid('test111111');
        $this->assertTrue($users instanceof Ilios_UserSync_ExternalUser_Iterator_Array);
        $this->assertEquals(1, count($users));


        // check again with different case
        $users = $userSource->getUserByUid('Test111111');
        $this->assertTrue($users instanceof Ilios_UserSync_ExternalUser_Iterator_Array);
        $this->assertEquals(1, count($users));

        // check duplicate user - multiple records should be returned
        $users = $userSource->getUserByUid('test111333');
        $this->assertTrue($users instanceof Ilios_UserSync_ExternalUser_Iterator_Array);
        $this->assertEquals(2, count($users));
    }

    /**
     * @test
     * @covers Ilios_UserSync_UserSource_Array::hasStudent
     * @group ilios
     * @group usersync
     */
    public function hasStudent ()
    {
        $config = array();
        $config['array']['users'] = array_merge($this->_fixtures['students'], $this->_fixtures['non_students']);
        $userSource = new Ilios_UserSync_UserSource_Array($config);

        // user exists and is student
        $hasStudent = $userSource->hasStudent('test111111');
        $this->assertTrue($hasStudent);

        // user exists and is NOT as student
        $hasStudent = $userSource->hasStudent('test222222');
        $this->assertFalse($hasStudent);

        // user does not exist
        $hasStudent = $userSource->hasStudent('XXXXXXXXX');
        $this->assertFalse($hasStudent);
    }

    /**
     * @test
     * @covers Ilios_UserSync_UserSource_Array::hasUser
     * @group ilios
     * @group usersync
     */
    public function hasUser ()
    {
        $config = array();
        $config['array']['users'] = array_merge($this->_fixtures['students'], $this->_fixtures['non_students']);
        $userSource = new Ilios_UserSync_UserSource_Array($config);

        // user exists and is student
        $hasUser = $userSource->hasUser('test111111');
        $this->assertTrue($hasUser);

        // user exists and is NOT as student
        $hasUser = $userSource->hasUser('test222222');
        $this->assertTrue($hasUser);

        // user does not exist
        $hasUser = $userSource->hasUser('XXXXXXXXX');
        $this->assertFalse($hasUser);
    }


}
