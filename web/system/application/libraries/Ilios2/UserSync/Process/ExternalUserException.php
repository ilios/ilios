<?php
/**
 * This exception represents an error encountered while processing (e.g. validating)
 * user records from an external user store.
 *
 * E.g. the extnernal user's email address is missing or invalid.
 */
class Ilios2_UserSync_Process_ExternalUserException extends Ilios2_UserSync_Exception
{
    /* error codes */
    const INVALID_PRIMARY_SCHOOL_ID = 101;
    const INVALID_EMAIL = 102;
    const INVALID_UID = 103;
}
