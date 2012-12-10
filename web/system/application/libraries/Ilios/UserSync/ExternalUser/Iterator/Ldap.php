<?php

/**
 * Iterator implementation that wraps around and operates on a given LDAP iterator and
 * creates/returns external user objects using a given external user factory.
 *
 * @see Ilios_Ldap_Iterator
 * @see Ilios_UserSync_ExternalUser_Factory
 */
class Ilios_UserSync_ExternalUser_Iterator_Ldap implements Iterator, Countable
{
    /**
     * @var Ilios_Ldap_Iterator
     */
    protected $_innerIterator;

    /**
     * @var Ilios_UserSync_ExternalUser_Factory
     */
    protected $_userFactory;


    /**
     * Constructor.
     * @param Ilios_Ldap_Iterator $innerIterator
     * @param Ilios_UserSync_ExternalUser_Factory $userFactory
     */
    public function __construct (Ilios_Ldap_Iterator $innerIterator, Ilios_UserSync_ExternalUser_Factory $userFactory)
	{
	    $this->_innerIterator = $innerIterator;
	    $this->_userFactory = $userFactory;
	}


	/**
	 * Converts and returns the current LDAP result set entry as external user object.
	 * @return Ilios_UserSync_ExternalUser|null
     * @see Ilios_Ldap_Iterator::current()
     * @throws Ilios_Ldap_Exception
     * @throws Ilios_UserSync_Exception
     */
    public function current ()
    {
        return $this->_userFactory->createUser($this->_innerIterator->current());
    }

	/**
     * @see Ilios_Ldap_Iterator::next()
     * @throws Ilios_Ldap_Exception
     */
    public function next ()
    {
        return $this->_innerIterator->next();
    }

    /**
     * @return string|null
     * @see Ilios_Ldap_Iterator::key()
     * @throws Ilios_Ldap_Exception
     */
    public function key()
    {
        return $this->_innerIterator->key();
    }

	/**
	 * @return boolean
     * @see Ilios_Ldap_Iterator::key()
     * @throws Ilios_Ldap_Exception
     */
    public function valid ()
    {
        return $this->_innerIterator->valid();
    }

	/**
     * @throws Ilios_Ldap_Exception
     * @see Ilios_Ldap_Iterator::rewind()
     */
    public function rewind ()
    {
        $this->_innerIterator->rewind();
    }

	/**
	 * @return int
     * @see Ilios_Ldap_Iterator::count()
     */
    public function count ()
    {
        return $this->_innerIterator->count();
    }
}
