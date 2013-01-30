<?php

/**
 * Implementation of the external user source interface.
 * Operates on user data which is passed directly to the source object
 * as nested array during instantiation.
 */
class Ilios_UserSync_UserSource_Array implements Ilios_UserSync_UserSource
{
    /**
     * @var array the internal data store of user records.
     */
    protected $_users = array();

    /**
     * Constructor.
     * @param array $config
     * @see Ilios_UserSync_UserSource::__construct()
     * @throws Ilios_UserSync_Exception if user data input is missing.
     *
     * The user data is expected to be passed in as nested array alongside the configuration.
     * It is assumed that it can be found under <code>$config['array']['users']</code>.
     */
    public function __construct (array $config = array())
    {
        if (! @is_array($config['array']['users'])) {
            throw new Ilios_UserSync_Exception('No user data provided to test user source during instantiation.');
        }
        $this->_users = $config['array']['users'];
    }

    /**
     * Returns a list of student records.
     * @return Ilios_UserSync_ExternalUser_Iterator_Array
     * @see Ilios_UserSync_UserSource::getAllStudentRecords()
     */
    public function getAllStudentRecords ()
    {
        $students = array();
        foreach ($this->_users as $user) {
            if (true === $user['is_student']) {
                $students[] = $user;
            }
        }
        return new Ilios_UserSync_ExternalUser_Iterator_Array(
                    new Ilios_UserSync_ExternalUser_Factory_Array(), $students);
    }
    /**
     * @param string $email
     * @return Ilios_UserSync_ExternalUser_Iterator_Array
     * @see Ilios_UserSync_UserSource::getUserByEmail()
     */
	public function getUserByEmail ($email)
	{
	    $users = array();
	    foreach ($this->_users as $user) {
	        if (0 === strcasecmp($email, $user['email'])) { // case-insensitive comparison
	            $users[] = $user;
	        }
	    }
	    return new Ilios_UserSync_ExternalUser_Iterator_Array(
                    new Ilios_UserSync_ExternalUser_Factory_Array(), $users);
	}

	/**
     * @param string $uid
     * @return Ilios_UserSync_ExternalUser_Iterator_Array
	 * @see Ilios_UserSync_UserSource::getUserByUid()
	 */
	public function getUserByUid ($uid)
	{
	    $users = array();
	    foreach ($this->_users as $user) {
	        if (0 === strcasecmp($uid, $user['uid'])) { // case-insensitive comparison
	            $users[] = $user;
	        }
	    }
	    return new Ilios_UserSync_ExternalUser_Iterator_Array(
                    new Ilios_UserSync_ExternalUser_Factory_Array(), $users);
	}

	/**
	 * @param string $uid
	 * @return boolean
	 * @see Ilios_UserSync_UserSource::hasStudent()
	 */
	public function hasStudent ($uid)
	{
	    foreach ($this->_users as $user) {
	        if (0 === strcasecmp($uid, $user['uid']) && true === $user['is_student']) {
	            return true; // student with UID found!
	        }
	    }
	    return false; // no student found
	}

	/**
	 * @param string $uid
	 * @return boolean
	 * @see Ilios_UserSync_UserSource::hasUser()
	 */
	public function hasUser ($uid)
	{
	    foreach ($this->_users as $user) {
	        if (0 === strcasecmp($uid, $user['uid'])) {
	            return true; // user with UID found!
	        }
	    }
	    return false; // no user found
	}


}
