<?php

/**
 * This exception represents an error encountered while processing (e.g. validating)
 * Ilios user records.
 *
 * E.g. a student is found in Ilios that does not exist in the external user store.
 * This defines a process violation, and a corresponding user exception must be raised.
 */
class Ilios_UserSync_Process_UserException extends Ilios_UserSync_Exception
{
    /**
     * @var string the name of a mismatched attribute
     */
    protected $_mismatchedAttributeName;

    /**
     * @var string the value of a mismatched attribute
     */
    protected $_mismatchedAttributeValue;

    /**
     * Constructor.
     * @param string $message the exception message
     * @param int $code the exception code
     * @param string $mismatchedAttributeName the name of a mismatched attribute
     * @param string $mismatchedAttributeValue the value of a mismatched attribute
     */
    public function __construct($message, $code, $mismatchedAttributeName = null, $mismatchedAttributeValue = null)
    {
        parent::__construct($message, $code);
        $this->_mismatchedAttributeName = $mismatchedAttributeName;
        $this->_mismatchedAttributeValue = $mismatchedAttributeValue;
    }

    /*  error codes */

    // student sync
    /**
     * @var int indicates a mismatch between an Ilios user record and an external user record on email
     */
    const STUDENT_SYNC_EMAIL_MISMATCH = 201;
    /**
     * @var int indicates a mismatch between an Ilios user record and an external user record on student status,
     * where Ilios lists the user as student but the external user store does not.
     */
    const STUDENT_SYNC_STATUS_MISMATCH_IN_EXTERNAL_USER_STORE = 202;
    /**
     * @var int indicates the absence of an Ilios student record in the external user store
     */
    const STUDENT_SYNC_NOT_IN_EXTERNAL_USER_SOURCE = 203;
    /**
     * @var int indicates a mismatch between an Ilios user record and an external user record on student status,
     * where the external user store lists the user as student but Ilios does not.
     */
    const STUDENT_SYNC_STATUS_MISMATCH_IN_ILIOS = 204;
    /**
     * @var int indicates an "unknown error" in the student user sync process
     */
    const STUDENT_SYNC_UNKNOWN_ERROR = 299;

    // non-student sync
    /**
     * @var int indicates a mismatch between an Ilios user and an external user record on email
     */
    const NON_STUDENT_SYNC_EMAIL_MISMATCH = 301;
    /**
     * @var int indicates a mismatch between an Ilios user and an external user record on UID
     */
    const NON_STUDENT_SYNC_UID_MISMATCH = 302;
    /**
     * @var int indicates a duplication of an Ilios user record on one or more attributes
     */
    const NON_STUDENT_SYNC_USER_DUPLICATES = 303;
    /**
     * @var int indicates a partial mismatch between the Ilios record and an external user record
     */
    const NON_STUDENT_SYNC_PARTIAL_MISMATCH = 304;
    /**
     * @var int indicates an "unknown error" in the non-student user sync process
     */
    const NON_STUDENT_SYNC_UNKNOWN_ERROR = 399;

    /* getter methods */

    /**
     * @return string returns the name of a mismatched attribute
     */
    public function getMismatchedAttributeName ()
    {
        return $this->_mismatchedAttributeName;
    }

    /**
     * @return string returns the value of a mismatched attribute
     */
    public function getMismatchedAttributeValue ()
    {
        return $this->_mismatchedAttributeValue;
    }
}
