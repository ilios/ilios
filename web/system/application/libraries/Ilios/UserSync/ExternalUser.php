<?php

/**
 * Model representing an external user record,
 * as retrieved from an external user store (such as a campus directory).
 *
 * Do not directly instantiate objects of this class,
 * instead use an external-user factory.
 *
 * @see Ilios_UserSync_ExternalUser_Factory
 */
class Ilios_UserSync_ExternalUser
{
    /**
     * @var string
     */
    protected $_firstName;
    /**
     * @var string
     */
    protected $_lastName;
    /**
     * @var string
     */
    protected $_middleName;
    /**
     * @var string
     */
    protected $_email;
    /**
     * @var string
     */
    protected $_phone;
    /**
     * @var boolean
     */
    protected $_isStudent;
    /**
     * @var string
     */
    protected $_schoolId;
    /**
     * @var int
     */
    protected $_graduationYear;
    /**
     * @var string
     */
    protected $_uid;

    /**
     *
     * @param string $firstName
     * @param string $lastName
     * @param string $middleName
     * @param string $email
     * @param string $phone
     * @param boolean $isStudent
     * @param string $schoolId
     * @param int $graduationYear
     * @param string $uid
     */
    public function __construct ($firstName, $lastName, $middleName,
        $email, $phone, $isStudent, $schoolId, $graduationYear, $uid)
    {
         $this->_firstName = $firstName;
         $this->_lastName = $lastName;
         $this->_middleName = $middleName;
         $this->_email = $email;
         $this->_phone = $phone;
         $this->_isStudent = $isStudent;
         $this->_schoolId = $schoolId;
         $this->_graduationYear = $graduationYear;
         $this->_uid = $uid;
    }
    /**
     * @return string
     */
    public function getFirstName ()
    {
        return $this->_firstName;
    }

    /**
     * @return string
     */
    public function getLastName ()
    {
        return $this->_lastName;
    }

    /**
     * @return string
     */
    public function getMiddleName ()
    {
        return $this->_middleName;
    }

    /**
     * @return string
     */
    public function getEmail ()
    {
        return $this->_email;
    }

    /**
     * @return string
     */
    public function getPhone ()
    {
        return $this->_phone;
    }

    /**
     * @return boolean
     */
    public function isStudent ()
    {
        return $this->_isStudent;
    }


    /**
     * @return string
     */
    public function getSchoolId ()
    {
        return $this->_schoolId;
    }

    /**
     * @return int
     */
    public function getGraduationYear ()
    {
        return $this->_graduationYear;
    }

    /**
     * @return string
     */
    public function getUid ()
    {
        return $this->_uid;
    }

    /**
     * Returns a one-liner containing all user properties.
     * @return string
     */
    public function __toString ()
    {
        $rhett = $this->getLastName() . ', ' . $this->getFirstName();
        $middleName = $this->getMiddleName();
        if ('' != $middleName) {
            $rhett .= ' ' . $middleName;
        }
        $rhett .= ' (' . $this->getEmail() . ' / ' . $this->getPhone() . ') ';
        $rhett .= ($this->isStudent() ? '' : 'Non-') . 'Student with UID: ' . $this->getUid();
        return $rhett;
    }
}
