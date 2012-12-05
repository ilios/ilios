<?php

/**
 * Iterator implementation that wraps around and operates on a given LDAP iterator and
 * creates/returns external user objects using a given external user factory.
 *
 * @see Ilios2_Ldap_Iterator
 * @see Ilios2_UserSync_ExternalUser_Factory
 */
class Ilios2_UserSync_ExternalUser_Iterator_Ldap implements Iterator, Countable
{
    /**
     * @var Ilios2_Ldap_Iterator
     */
    protected $_innerIterator;

    /**
     * @var Ilios2_UserSync_ExternalUser_Factory
     */
    protected $_userFactory;


    /**
     * Constructor.
     * @param Ilios2_Ldap_Iterator $innerIterator
     * @param Ilios2_UserSync_ExternalUser_Factory $userFactory
     */
    public function __construct (Ilios2_Ldap_Iterator $innerIterator, Ilios2_UserSync_ExternalUser_Factory $userFactory)
	{
	    $this->_innerIterator = $innerIterator;
	    $this->_userFactory = $userFactory;
	}


	/**
	 * Converts and returns the current LDAP result set entry as external user object.
	 * @return Ilios2_UserSync_ExternalUser|null
     * @see Ilios2_Ldap_Iterator::current()
     * @throws Ilios2_Ldap_Exception
     * @throws Ilios2_UserSync_Exception
     */
    public function current ()
    {
        return $this->_userFactory->createUser($this->_innerIterator->current());
    }

	/**
     * @see Ilios2_Ldap_Iterator::next()
     * @throws Ilios2_Ldap_Exception
     */
    public function next ()
    {
        return $this->_innerIterator->next();
    }

    /**
     * @return string|null
     * @see Ilios2_Ldap_Iterator::key()
     * @throws Ilios2_Ldap_Exception
     */
    public function key()
    {
        return $this->_innerIterator->key();
    }

	/**
	 * @return boolean
     * @see Ilios2_Ldap_Iterator::key()
     * @throws Ilios2_Ldap_Exception
     */
    public function valid ()
    {
        return $this->_innerIterator->valid();
    }

	/**
     * @throws Ilios2_Ldap_Exception
     * @see Ilios2_Ldap_Iterator::rewind()
     */
    public function rewind ()
    {
        $this->_innerIterator->rewind();
    }

	/**
	 * @return int
     * @see Ilios2_Ldap_Iterator::count()
     */
    public function count ()
    {
        return $this->_innerIterator->count();
    }
}
