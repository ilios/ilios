<?php
/**
 * Unit test bootstrap PHP file.
 * Runs before the tests.
 */

// load the autoloader
require_once dirname(__FILE__) . '/vendor/autoload.php';

// add the tests base directory to the includes path
set_include_path(get_include_path() . PATH_SEPARATOR . dirname(__FILE__));
