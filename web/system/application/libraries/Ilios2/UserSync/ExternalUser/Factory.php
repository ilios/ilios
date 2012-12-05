<?php
/**
 * Defines an interface for external user factories.
 *
 * Classes implementing this interface will provide the functionality to create objects representing
 * external users from given user-data as array input.
 *
 * @see Ilios2_UserSync_ExternalUser
 * @see Ilios2_UserSync_UserSource
 */
interface Ilios2_UserSync_ExternalUser_Factory
{
    /**
     * @param array $properties
     * @return Ilios2_UserSync_ExternalUser
     */
    public function createUser(array $properties);
}
