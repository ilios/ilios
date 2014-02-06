<?php

/**
 * Implementation of an iterator operating on a given nested array, returning external user objects.
 */
class Ilios_UserSync_ExternalUser_Iterator_Array implements Iterator, Countable
{
    /**
     * @var int internal array counter
     */
    protected $_index = 0;

    /**
     * @var int length of the internal array
     */
    protected $_count = 0;

    /**
     * @var array the currently selected result item
     */
	protected $_current;

	/**
	 * @var array
	 */
	protected $_userData;

	/**
	 * @var Ilios_UserSync_ExternalUser_Factory
	 */
	protected $_userFactory;

	/**
	 * Constructor.
	 * @param Ilios_UserSync_ExternalUser_Factory $userFactory
	 * @param array $userData
	 */
	public function __construct (Ilios_UserSync_ExternalUser_Factory $userFactory, array $userData)
	{
	    $this->_userFactory = $userFactory;
	    $this->_userData = $userData;
	    $this->_count = count($this->_userData);
	    $this->_index = 0;
	}

	/**
	 * @return Ilios_UserSync_ExternalUser
     * @see Iterator::current()
     */
    public function current()
    {
        if (! is_array($this->_current)) {
            $this->rewind();
        }
        if (! is_array($this->_current)) {
            return null;
        }
        return $this->_userFactory->createUser($this->_current);

    }

	/* (non-PHPdoc)
     * @see Iterator::next()
     */
    public function next()
    {
        ++$this->_index;
        if ($this->valid()) {
            $this->_current = $this->_userData[$this->_index];
        }
    }

	/* (non-PHPdoc)
     * @see Iterator::key()
     */
    public function key()
    {
        return $this->_index;

    }

	/* (non-PHPdoc)
     * @see Iterator::valid()
     */
    public function valid()
    {
        return ($this->_index < $this->_count);

    }

	/* (non-PHPdoc)
     * @see Iterator::rewind()
     */
    public function rewind()
    {
        $this->_index = 0;
        if ($this->valid()) {
            $this->_current = $this->_userData[$this->_index];
        }

    }
	/* (non-PHPdoc)
     * @see Countable::count()
     */
    public function count()
    {
        return $this->_count;
    }
}
