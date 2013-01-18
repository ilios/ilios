<?php
require_once 'Ilios/TestCase.php';

/**
 * Test case for the LDAP client.
 * @see Ilios_Ldap
 */
class Ilios_LdapTest extends Ilios_TestCase
{
    /**
     * LDAP test configuration params.
     * @var array
     */
    protected $_ldapConfig;

    /**
     * @see PHPUnit_Framework_TestCase::setUp()
     */
    protected function setUp ()
    {
        $this->_ldapConfig = Ilios_TestUtils::getLdapTestConfiguration();
    }


    /**
     * @test
     * @covers Ilios_Ldap::connect
     * @group ilios
     * @group ldap
     * @group user_sync
     */
    public function testConnectSuccess ()
    {
        $ldap = new Ilios_Ldap($this->_ldapConfig);
        $ldap->connect();
        $conn = $ldap->getResource();
        $this->assertTrue(is_resource($conn));
        $ldap->disconnect();
    }

    /**
     * @test
     * @covers Ilios_Ldap::connect
     * @expectedException Ilios_Ldap_Exception
     * @group ilios
     * @group ldap
     * @group user_sync
     */
    public function testConnectFailure ()
    {
        $borkedConfig = $this->_ldapConfig;
        $borkedConfig['host'] = '';
        // trying to instantiate the ldap client
        // without providing a host-name/url
        // should trigger an exception
        $ldap = new Ilios_Ldap($borkedConfig);
        $ldap->connect();
    }

    /**
     * @test
     * @covers Ilios_Ldap::bind
     * @group ilios
     * @group ldap
     * @group user_sync
     */
    public function testBindSuccess ()
    {
        $ldap = new Ilios_Ldap($this->_ldapConfig);
        $ldap->bind();
        $conn = $ldap->getResource();
        $this->assertTrue(is_resource($conn));
        $ldap->disconnect();
    }

    /**
     * @test
     * @covers Ilios_Ldap::connect
     * @expectedException Ilios_Ldap_Exception
     * @group ilios
     * @group ldap
     * @group user_sync
     */
    public function testBindFailure ()
    {
        $borkedConfig = $this->_ldapConfig;
        $borkedConfig['password'] = 'INCORRECTPASSWORD';
        // trying to bind to the LDAP server
        // with an incorrect password
        // should trigger an exception
        $ldap = new Ilios_Ldap($borkedConfig);
        $ldap->bind();
    }

    /**
     * @test
     * @covers Ilios_Ldap::disconnect
     * @group ilios
     * @group ldap
     * @group user_sync
     */
    public function testDisconnect ()
    {
        $ldap = new Ilios_Ldap($this->_ldapConfig);
        $ldap->bind(); // connect + bind
        $this->assertTrue(is_resource($ldap->getResource())); // first, i was like THIS ...
        $ldap->disconnect(); //disconnect
        $this->assertNull($ldap->getResource()); // ... but then i was like THAT.
    }

    /**
     * @test
     * @covers Ilios_Ldap::search
     * @group ilios
     * @group ldap
     * @group user_sync
     */
    public function testSearchSuccess ()
    {
        $ldap = new Ilios_Ldap($this->_ldapConfig);
	    $filter = '(objectClass=*)';
        $ldap->bind();
        $search = $ldap->search(Ilios_UserSync_UserSource_Eds::EDS_BASE_DN, $filter,
                        Ilios_Ldap::LDAP_SCOPE_SUBTREE, array(), false, 1, 0);
        $this->assertTrue(is_resource($search));
        $results = ldap_get_entries($ldap->getResource(), $search);
        $this->assertTrue(is_array($results));
        $ldap->disconnect();
    }

    /**
     * @test
     * @covers Ilios_Ldap::search
     * @expectedException Ilios_Ldap_Exception
     * @group ilios
     * @group ldap
     * @group user_sync
     */
    public function testSearchFailure ()
    {
        $ldap = new Ilios_Ldap($this->_ldapConfig);
	    $brokenFilter = '(objectClass=*'; // missing closing parenthesis
        $ldap->bind();
        // calling search() with the broken filter will barf up an exception
        $search = $ldap->search(Ilios_UserSync_UserSource_Eds::EDS_BASE_DN, $brokenFilter,
                        Ilios_Ldap::LDAP_SCOPE_SUBTREE, array(), false, 1, 0);
    }
}
