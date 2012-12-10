<?php
/**
 * Utility class for supporting Ilios unit tests.
 *
 * @package
 * @author Stefan Topfstedt <stefan.topfstedt@ucsf.edu>
 * @copyright Copyright (c) 2010, UCSF Center for Knowledge Management
 */

class Ilios2_TestUtils
{
    /**
     * Returns a nested array containing LDAP connection parameters for EDS, as defined in
     * your PHPUnit configuration file.
     * The values in the returned array are keyed-off as following:
     * ['host'] ... server URL
     * ['port'] ... server port
     * ['bind_dn'] ... bind DN
     * ['password'] ... password
     * @return array
     */
    public static function getEdsTestConfiguration ()
    {
        $config = array();
        if (defined('ILIOS2_TEST_USER_SYNC_EDS_HOST')) {
            $config['host'] = ILIOS2_TEST_USER_SYNC_EDS_HOST;
        }
        if (defined('ILIOS2_TEST_USER_SYNC_EDS_PORT')) {
            $config['port'] = (int) ILIOS2_TEST_USER_SYNC_EDS_PORT;
        }
        if (defined('ILIOS2_TEST_USER_SYNC_EDS_BIND_DN')) {
            $config['bind_dn'] = ILIOS2_TEST_USER_SYNC_EDS_BIND_DN;
        }
        if (defined('ILIOS2_TEST_USER_SYNC_EDS_PASSWORD')) {
            $config['password'] = ILIOS2_TEST_USER_SYNC_EDS_PASSWORD;
        }
        return $config;
    }

    /**
     * Returns a nested array containing LDAP connection parameters as defined in your
     * unit test configuration.
     * For now, this is just an alias for the getEdsTestConfiguration() function.
     * @see Ilios2_TestUtils::getEdsTestConfiguration
     * @return array
     */
    public static function getLdapTestConfiguration ()
    {
        return Ilios2_TestUtils::getEdsTestConfiguration();
    }
}
