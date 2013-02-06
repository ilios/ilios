<?php
// bootstrap CodeIgniter, always!
// amongst other things, this will register the autoloader responsible for loading
// the Ilios code library within the application path.
require_once 'ci_bootstrap.php';

// load PHP Unit classes.
require_once 'PHPUnit/Extensions/Database/TestCase.php';

// load test utils
require_once 'Ilios/TestUtils.php';

/**
 * Base class for Ilios unit test cases.
 * Subclass from here if you need to test components in the Ilios code library
 * that do not have dependencies on CI components.
 *
 * @see Ilios/CI/TestCase.php, a testcase boilerplate class for test involving CI components.
 */
abstract class Ilios_TestCase extends PHPUnit_Framework_TestCase
{
}
