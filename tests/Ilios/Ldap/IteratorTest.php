<?php
require_once dirname(dirname(__FILE__)) . '/TestCase.php';

/**
 * Test case for the LDAP search result iterator.
 * @see Ilios_Ldap_Iterator
 */
class Ilios_Ldap_IteratorTest extends Ilios_TestCase
{
    /**
     * @var Ilios_Ldap
     */
    protected $_ldap;

	/* (non-PHPdoc)
     * @see PHPUnit_Framework_TestCase::setUp()
     */
    protected function setUp() {
        // instantiate an LDAP client, then connect and bind to the server
        $this->_ldap = new Ilios_Ldap(Ilios_TestUtils::getLdapTestConfiguration());
        $this->_ldap->bind();

    }

	/* (non-PHPdoc)
     * @see PHPUnit_Framework_TestCase::tearDown()
     */
    protected function tearDown() {
        // cleanup - disconnect from the LDAP server
        $this->_ldap->disconnect();

    }

    /**
     * @test
     * @covers Ilios_Ldap_Iterator
     * @group ilios2
     * @group ldap
     * @group user_sync
     */
    public function testIterate ()
    {
        $filter = '(objectClass=*)';

        $limit = 10;
        $result = $this->_ldap->search(Ilios_UserSync_UserSource_Eds::EDS_BASE_DN, $filter,
                                Ilios_Ldap::LDAP_SCOPE_ONELEVEL,array(), false, $limit);
        // instantiate the iterator
        $records = new Ilios_Ldap_Iterator($this->_ldap, $result);

        // test if Countable interface is implemented correctly
        $this->assertEquals($limit, count($records));

        $i = 0; // loop counter
        foreach ($records as $record) { // test iteration
            $this->assertNotNull($record);
            $i++;
        }
        // check if we iterated over all elements
        $this->assertEquals($limit, $i);
    }
}
