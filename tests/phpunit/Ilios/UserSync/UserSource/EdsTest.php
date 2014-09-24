<?php
require_once 'Ilios/TestCase.php';

/**
 * Test-case for the EDS/LDAP user source client.
 * @see Ilios_UserSync_UserSource_Eds
 */

class Ilios_UserSync_UserSource_EdsTest extends Ilios_TestCase
{
    /**
     * Returns a nested array of EDS user source config params.
     * The keys are
     * ['ldap']['host']
     * ['ldap']['port']
     * ['ldap']['bind_dn']
     * ['ldap']['password']
     * @return array
     * @see Ilios_TestUtils::getEdsTestConfiguration()
     */
    protected function _getUserSourceConfiguration ()
    {
        $config = array();
        $config['ldap'] = Ilios_TestUtils::getEdsTestConfiguration();
        return $config;
    }

    /**
     * @test
     * @group ilios
     * @group ldap
     * @group usersync
     * @group integration
     * @covers Ilios_UserSync_UserSource_Eds::getActiveStudentRecords
     */
    public function testGetActiveStudentRecords ()
    {
        $config = $this->_getUserSourceConfiguration();
        $userSource = new Ilios_UserSync_UserSource_Eds($config);
        // retrieve 10 students from EDS
        $students = $userSource->getActiveStudentRecords(10); // check correct count
        $this->assertEquals(10, count($students));
        $i = 0;
        foreach ($students as $student) { // check that each student is
            $this->assertTrue($student->isStudent());
            $i++;
        }
        $this->assertEquals(10, $i); // check count again
    }

    /**
     * @test
     * @group ilios
     * @group ldap
     * @group usersync
     * @group integration
     * @covers Ilios_UserSync_UserSource_Eds::hasStudent
     */
    public function testHasStudent ()
    {
        $config = $this->_getUserSourceConfiguration();
        $userSource = new Ilios_UserSync_UserSource_Eds($config);
        $students = $userSource->getActiveStudentRecords(10); // retrieve a bunch of students
        // for each retrieved student, check back in EDS by UID
        $tested = false;
        foreach ($students as $student) {
            $uid = $student->getUid();
            if (! empty($uid)) {
                $this->assertTrue($userSource->hasStudent($uid));
                $tested = true;
            }
        }
        if (! $tested) {
            $this->fail('Unable to perform test since none of the retrieved students have an UID.');
        }

        // check EDS with a bogus UID
        // this should fail since no record ever should be found
        $bogusUid = 'TOTALLY_NON_EXISTENT_UID';
        $this->assertFalse($userSource->hasStudent($bogusUid));
    }

    /**
     * @test
     * @group ilios
     * @group ldap
     * @group usersync
     * @group integration
     * @covers Ilios_UserSync_UserSource_Eds::hasUser
     */
    public function testHasUser ()
    {
        $config = $this->_getUserSourceConfiguration();
        $userSource = new Ilios_UserSync_UserSource_Eds($config);
        $students = $userSource->getActiveStudentRecords(10); // retrieve a bunch of students
        // for each retrieved student, check back in EDS by UID
        $tested = false;
        foreach ($students as $student) {
            $uid = $student->getUid();
            if (! empty($uid)) {
                $this->assertTrue($userSource->hasUser($uid));
                $tested = true;
            }
        }
        if (! $tested) {
            $this->fail('Unable to perform test since none of the retrieved users have an UID.');
        }

        // check EDS with a bogus UID
        // this should fail since no record ever should be found
        $bogusUid = 'TOTALLY_NON_EXISTENT_UID';
        $this->assertFalse($userSource->hasUser($bogusUid));
    }



    /**
     * @test
     * @group ilios
     * @group ldap
     * @group usersync
     * @group integration
     * @covers Ilios_UserSync_UserSource_Eds::getUserByUid
     */
    public function testGetUserByUid ()
    {

        $config = $this->_getUserSourceConfiguration();
        $userSource = new Ilios_UserSync_UserSource_Eds($config);

        // get some user that have uids
        $filter = '(&(objectClass=person)(ucsfEduIDNumber=*))';
        $ldap = $userSource->getLdap();
        $results = $ldap->search(Ilios_UserSync_UserSource_Eds::EDS_BASE_DN, $filter,
                            Ilios_Ldap::LDAP_SCOPE_SUBTREE, array(), false, 10);
        $resultIterator = new Ilios_Ldap_Iterator($ldap, $results);
        $factory = new Ilios_UserSync_ExternalUser_Factory_Eds();
        foreach ($resultIterator as $result) {
            $user = $factory->createUser($result);
            // check EDS for user by Eds
            $uid = $user->getUid();
            $users = $userSource->getUserByUid($uid);
            foreach ($users as $user2) {
                $this->assertTrue(0 === strcasecmp($uid, $user2->getUid()));
            }
        }
    }

    /**
     * @test
     * @group ilios
     * @group ldap
     * @group usersync
     * @group integration
     * @covers Ilios_UserSync_UserSource_Eds::getUserByEmail
     */
    public function testGetUserByEmail ()
    {

        $config = $this->_getUserSourceConfiguration();
        $userSource = new Ilios_UserSync_UserSource_Eds($config);

        // get some user that have uids
        $filter = '(&(objectClass=person)(mail=*))';
        $ldap = $userSource->getLdap();
        $results = $ldap->search(Ilios_UserSync_UserSource_Eds::EDS_BASE_DN, $filter,
                            Ilios_Ldap::LDAP_SCOPE_SUBTREE, array(), false, 10);
        $resultIterator = new Ilios_Ldap_Iterator($ldap, $results);
        $factory = new Ilios_UserSync_ExternalUser_Factory_Eds();
        foreach ($resultIterator as $result) {
            $user = $factory->createUser($result);
            // check EDS for user by Eds
            $email = $user->getEmail();
            $users = $userSource->getUserByEmail($email);
            foreach ($users as $user2) {
                $this->assertTrue(0 === strcasecmp($email, $user2->getEmail()));
            }
        }
    }
}
