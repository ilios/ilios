<?php

/**
 * User-password utility class.
 *
 * @category Ilios2
 * @package Ilios2
 * @copyright Copyright (c) 2010-2012 The Regents of the University of California.
 * @license http://www.iliosproject.org/license GNU GPL v3
 */

/**
 * User-password utility class.
 *
 * @category Ilios2
 * @package Ilios2
 * @copyright Copyright (c) 2010-2012 The Regents of the University of California.
 * @license http://www.iliosproject.org/license GNU GPL v3
 */
class Ilios2_PasswordUtils
{
    /**
     * Minimum password length.
     * @var int
     */
    const MIN_PASSWORD_LENGTH = 8;

    /**
     * Maximum password length.
     * @var int
     */
    const MAX_PASSWORD_LENGTH = 12;

    /**
     * Password strength check return value.
     * Indicates that the given password passes the check.
     * @var int
     */
    const PASSWORD_STRENGTH_CHECK_OK = 0;

    /**
     * Password strength check return value.
     * Indicates that the given password is too short.
     * @var int
     */
    const PASSWORD_STRENGTH_CHECK_TOO_SHORT = 1;

    /**
     * Password strength check return value.
     * Indicates that the password is too long.
     * @var int
     */
    const PASSWORD_STRENGTH_CHECK_TOO_LONG = 2;

    /**
     * Password strength check return value.
     * Indicates that the given password contains invalid characters.
     * @var int
     */
    const PASSWORD_STRENGTH_CHECK_INVALID_CHARS = 4;

    /**
     * Password strength check return value.
     * Indicates that the given password does not contain at least one digit.
     * @var int
     */
    const PASSWORD_STRENGTH_CHECK_DIGIT_MISSING = 8;

    /**
     * Password strength check return value.
     * Indicates that the given password does not contain at least one lowercase character.
     * @var int
     */
    const PASSWORD_STRENGTH_CHECK_LOWERCASE_CHAR_MISSING = 16;

    /**
     * Password strength check return value.
     * Indicates that the given password does not contain at least one uppercase character.
     * @var int
     */
    const PASSWORD_STRENGTH_CHECK_UPPERCASE_CHAR_MISSING = 32;

    /**
     * Password strength check return value.
     * Indicates that the given password does not contain at least one 'special' character.
     * @var int
     */
    const PASSWORD_STRENGTH_CHECK_SPECIAL_CHAR_MISSING = 64;

    /**
     * Generates a random password string that complies with our password strength requirements.
     * @return string the generated password.
     */
    public static function generateRandomPassword ()
    {
        $ok = false;
        $rhett = '';

        $chars = array(
            '0123456789',
            'bcdfghjklmnpqrstvwxyz',
            'BCDFGHJKLMNPQRSTVWXYZ',
            '$*_-');

        $charGroupCount = count($chars);

        $rhett = '';
        // get a random character from each group, one at a time.
        // this adds a deterministic element to the otherwise random nature of this
        // routine, but it ensures that the generated password will pass
        // our own password-strength check in any case.
        for ($i = 0; $i < $charGroupCount; $i++) {
            $randPos = mt_rand(0, strlen($chars[$i]) - 1); // random char position
            $rhett .= substr($chars[$i], $randPos, 1);
        }

        // further randomize the remainder.
        for ($i = 0, $n = Ilios2_PasswordUtils::MAX_PASSWORD_LENGTH - $charGroupCount; $i < $n; $i++) {
            $randGroup = mt_rand(0, $charGroupCount - 1); // random char group
            $randPos = mt_rand(0, strlen($chars[$randGroup]) - 1); // random char position
            $rhett .= substr($chars[$randGroup], $randPos, 1);
        }

        return $rhett;
    }
    /**
     * Verifies that a given password complies to various password-strength requirements.
     * @param string the password to check
     * @return int Bitmask comprised of PASSWORD_STRENGTH_CHECK_* constants.
     * @todo spell out the validation rules.
     */
    public static function checkPasswordStrength ($password)
    {
        $rhett = Ilios2_PasswordUtils::PASSWORD_STRENGTH_CHECK_OK;
        $len = strlen($password);
        // check password length
        if (Ilios2_PasswordUtils::MIN_PASSWORD_LENGTH > $len) {
            $rhett = $rhett ^ Ilios2_PasswordUtils::PASSWORD_STRENGTH_CHECK_TOO_SHORT;
        } elseif (Ilios2_PasswordUtils::MAX_PASSWORD_LENGTH < $len) {
            $rhett = $rhett ^ Ilios2_PasswordUtils::PASSWORD_STRENGTH_CHECK_TOO_LONG;
        }

        // check for invalid chars
        if (! preg_match('/^[0-9a-zA-Z$*_-]+$/', $password)) {
            $rhett = $rhett ^ Ilios2_PasswordUtils::PASSWORD_STRENGTH_CHECK_INVALID_CHARS;
        }

        // check for at least one number
        if (! preg_match('/[0-9]/', $password)) {
            $rhett = $rhett ^ Ilios2_PasswordUtils::PASSWORD_STRENGTH_CHECK_DIGIT_MISSING;
        }

        // check for at least one lowercase character
        if (! preg_match('/[a-z]/', $password)) {
            $rhett = $rhett ^ Ilios2_PasswordUtils::PASSWORD_STRENGTH_CHECK_LOWERCASE_CHAR_MISSING;
        }
        // check for at least one lowercase character
        if (! preg_match('/[A-Z]/', $password)) {
            $rhett = $rhett ^ Ilios2_PasswordUtils::PASSWORD_STRENGTH_CHECK_UPPERCASE_CHAR_MISSING;
        }
        // check for at least one special character
        if (! preg_match('/[$*_-]/', $password)) {
            $rhett = $rhett ^ Ilios2_PasswordUtils::PASSWORD_STRENGTH_CHECK_SPECIAL_CHAR_MISSING;
        }
        return $rhett;
    }

    /**
     * Hashes a given password, optionally with a given salt.
     * @param string $password the unencrypted password
     * @param string $salt the salt
     * @return string the hashed password as lowercase hexits
     */
    public static function hashPassword ($password, $salt = null)
    {
        if ($salt) {
            $password .= $salt;
        }
        return hash('sha256', $password);
    }
}
