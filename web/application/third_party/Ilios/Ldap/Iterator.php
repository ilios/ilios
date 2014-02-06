<?php
/**
 * Implementation of an iterator operating on a given LDAP result set.
 */
class Ilios_Ldap_Iterator implements Iterator, Countable
{
    /**
     * LDAP client
     * @var Ilios_Ldap
     */
    protected $_ldap;

    /**
     * Current result identifier (the result set handle)
     * @var resource
     */
    protected $_currentResult;

    /**
     * Current LDAP result set entry
     * @var resource
     */
    protected $_currentEntry;

    /**
     * Number of items in query result
     * @var integer
     */
    protected $_count = false;

    /**
     * Constructor
     * @param resource $ldap LDAP bind handle
     * @param resource $currentResult LDAP result-set handle
     */
    public function __construct (Ilios_Ldap $ldap, $currentResult)
	{
	    $this->_ldap = $ldap;
	    $this->_currentResult = $currentResult;
	    $this->_count = @ldap_count_entries($this->_ldap->getResource(), $this->_currentResult); // count the results
	    // throw an error if we couldn't get a proper count
	    if (false === $this->_count) {
	        throw new Ilios_Ldap_Exception('Failed counting entries in LDAP search result set.', @ldap_errno($this->_ldap->getResource()));
	    }
	}

	/**
	 * Destructor.
	 */
	public function __destruct ()
	{
	    // cleanup:
	    // close result set
	    if (is_resource($this->_currentResult)) {
	        @ldap_free_result($this->_currentResult);
	    }
        $this->_currentEntry = null;
        $this->_currentResult = null;
	}

	/**
	 * Returns the attributes of a given LDAP result set entry.
	 * @return array
     * @see Iterator::current()
     * @throws Ilios_Ldap_Exception
     */
    public function current ()
    {
        if (! is_resource($this->_currentEntry)) {
            $this->rewind();
        }
        if (! is_resource($this->_currentEntry)) {
            return null;
        }
        $attributes = @ldap_get_attributes($this->_ldap->getResource(), $this->_currentEntry);
        if (false === $attributes) {
            throw new Ilios_Ldap_Exception('Failed to retrieve attributes from current LDAP search result entry.', @ldap_errno($this->_ldap->getResource()));
        }
        return $attributes;
    }

	/**
	 * Move on to the next search result item.
     * @see Iterator::next()
     * @throws Ilios_Ldap_Exception
     */
    public function next ()
    {
        if (is_resource($this->_currentEntry)) {

            $this->_currentEntry = @ldap_next_entry($this->_ldap->getResource(), $this->_currentEntry);
            if (false === $this->_currentEntry) {
                // check if we reached the end of the line
                if (0x04 == @ldap_errno($this->_ldap->getResource())) {
                    return;
                } else if (0x00 < @ldap_errno($this->_ldap->getResource())) {
                    throw new Ilios_Ldap_Exception('Failed to get next entry from LDAP result set ', @ldap_errno($this->_ldap->getResource()));
                }
            }
        }
    }

    /**
     * Returns the result item key.
     * @return string|null
     * @see Iterator::key()
     */
    public function key()
    {
        if (!is_resource($this->_currentEntry)) {
            $this->rewind();
        }
        if (is_resource($this->_currentEntry)) {
            $dn = @ldap_get_dn($this->_ldap->getResource(), $this->_currentEntry);
            if (false === $dn) {
                throw new Ilios_Ldap_Exception('Failed to get DN for LDAP search result entry.', @ldap_errno($this->_ldap->getResource()));
            }
            return $dn;
        } else {
            return null;
        }
    }

	/**
	 * Verifies if there the internal current result item exists.
     * @return boolean
     * @see Iterator::valid()
     */
    public function valid ()
    {
        return (is_resource($this->_currentEntry));
    }

	/**
	 * Rewinds the Iterator to the first result item.
     * @throws Ilios_Ldap_Exception
     * @see Iterator::rewind()
     */
    public function rewind ()
    {
        if ($this->_currentResult) {
            $this->_currentEntry = @ldap_first_entry($this->_ldap->getResource(), $this->_currentResult);
            if (false === $this->_currentEntry && 0x00 < @ldap_errno($this->_ldap->getResource)) {
                throw new Ilios_Ldap_Exception('Failed to get first entry from LDAP result set.', @ldap_errno($this->_ldap->getResource()));
            }
        }
    }

	/**
	 * Results the total number of items in the result set.
	 * @return int
     * @see Countable::count()
     */
    public function count ()
    {
        return $this->_count;
    }
}
