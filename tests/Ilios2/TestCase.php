<?php
/*
 * Test environment defaults.
 * These can be overwritten in the PHPUnit configuration file.
 * @see tests/phpunit.xml
 */
if (! defined('ILIOS2_TEST_ROOT_DIR')) {
	define('ILIOS2_TEST_ROOT_DIR', '/web/ilios/htdocs/tests');
}

// define some base paths
if (! defined('ILIOS2_WEB_ROOT')) {
    define('ILIOS2_WEB_ROOT',  dirname(ILIOS2_TEST_ROOT_DIR) . '/web');
}

if (! defined('APPPATH')) {
    define('APPPATH',  ILIOS2_WEB_ROOT . '/system/application/');
}

// load and run autoloader
// do this only once!
if (! class_exists('Ilios2_Hooks', false)) {
    require_once APPPATH . 'hooks/Ilios2/Hooks.php';
    $hooks = new Ilios2_Hooks();
    $hooks->registerAutoloader();
}

// load PHP Unit classes.
require_once 'PHPUnit/Extensions/Database/TestCase.php';

// load test utils
require_once ILIOS2_TEST_ROOT_DIR . '/Ilios2/TestUtils.php';

/**
 * Base class for Ilios unit test cases.
 *
 * Subclass from here if you need to test components in the Ilios2 code library that
 * do not have dependencies on CodeIgniter classes and therefore do not need
 * CI to be fully instantiated.
 */
abstract class Ilios2_TestCase extends PHPUnit_Framework_TestCase
{
}
