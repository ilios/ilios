<?php
/*
 * Test environment defaults.
 * These can be overwritten in the PHPUnit configuration file.
 * @see tests/phpunit.xml
 */
if (! defined('ILIOS_TEST_ROOT_DIR')) {
	define('ILIOS_TEST_ROOT_DIR', '/web/ilios/htdocs/tests');
}

// define some base paths
if (! defined('ILIOS_WEB_ROOT')) {
    define('ILIOS_WEB_ROOT',  dirname(ILIOS_TEST_ROOT_DIR) . '/web');
}

if (! defined('APPPATH')) {
    define('APPPATH',  ILIOS_WEB_ROOT . '/system/application/');
}

// load and run autoloader
// do this only once!
if (! class_exists('Ilios_Hooks', false)) {
    require_once APPPATH . 'hooks/Ilios/Hooks.php';
    $hooks = new Ilios_Hooks();
    $hooks->registerAutoloader();
}

// load PHP Unit classes.
require_once 'PHPUnit/Extensions/Database/TestCase.php';

// load test utils
require_once ILIOS_TEST_ROOT_DIR . '/Ilios/TestUtils.php';

/**
 * Base class for Ilios unit test cases.
 *
 * Subclass from here if you need to test components in the ILIOS code library that
 * do not have dependencies on CodeIgniter classes and therefore do not need
 * CI to be fully instantiated.
 */
abstract class Ilios_TestCase extends PHPUnit_Framework_TestCase
{
}
