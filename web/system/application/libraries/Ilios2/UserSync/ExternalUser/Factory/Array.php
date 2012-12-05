<?php
/**
 * Implementation of the external user factory interface.
 *
 * Creates external user objects from given property arrays.
 *
 */
class Ilios2_UserSync_ExternalUser_Factory_Array implements Ilios2_UserSync_ExternalUser_Factory
{
	/**
	 * Creates and returns a new external user objects from given user properties.
	 * @param array $properties associative array of user properties.
     * @return Ilios2_UserSync_ExternalUser
     * @see Ilios2_UserSync_ExternalUser_Factory::createUser()
     */
    public function createUser(array $properties)
    {
        return new Ilios2_UserSync_ExternalUser(
            array_key_exists('first_name', $properties) ? $properties['first_name'] : null,
            array_key_exists('last_name', $properties) ? $properties['last_name']  : null,
            array_key_exists('middle_name', $properties) ? $properties['middle_name']  : null,
            array_key_exists('email', $properties) ? $properties['email']  : null,
            array_key_exists('phone', $properties) ? $properties['phone']  : null,
            array_key_exists('is_student', $properties) ? $properties['is_student'] : null,
            array_key_exists('school_id', $properties) ? $properties['school_id'] : null,
            array_key_exists('graduation_year', $properties) ? $properties['graduation_year'] : null,
            array_key_exists('uid', $properties) ? $properties['uid'] : null);
    }
}
