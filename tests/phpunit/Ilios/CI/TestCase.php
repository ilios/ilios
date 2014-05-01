<?php

// load DbUnit dependencies
require_once 'PHPUnit/Extensions/Database/TestCase.php';
require_once 'PHPUnit/Extensions/Database/DataSet/CompositeDataSet.php';

// load DbUnit customizations
require_once dirname(dirname(__FILE__)) . '/PHPUnit/Extensions/Database/Operation/ResetAutoincrement.php';
/**
 * Base class for Ilios/CodeIgniter unit test cases.
 *
 * Use this as a base for testing CI components, such as Model classes, within the Ilios application.
 *
 * Provided boilerplate code for dealing with populating the test database and cleaning it up
 * afterwards again.
 */
abstract class Ilios_CI_TestCase extends PHPUnit_Extensions_Database_TestCase
{

    /**
     * Database client.
     * @var PDO
     */
    static protected $_pdo = null;

    /**
     * test controller
     * @var Test_Controller
     */
    protected $_controller;

    /**
     * only instantiate PHPUnit_Extensions_Database_DB_IDatabaseConnection once per test
     * @var PHPUnit_Extensions_Database_DB_DefaultDatabaseConnection
     */
    protected $_conn = null;


    protected function setUp ()
    {
        parent::setUp();
        $this->ci_config = get_config();
        $this->_controller = get_instance();
    }

    /* (non-PHPdoc)
      * @see PHPUnit_Extensions_Database_TestCase::getConnection()
      */
    protected function getConnection ()
    {
        // shared DB client/connection across all implementing sub classes
        // only instantiate it once!
        // @link http://www.phpunit.de/manual/3.5/en/database.html#tip:-use-your-own-abstract-database-testcase
        if (is_null($this->_conn)) {
            $db = $this->_getCodeIgniterActiveTestDbConfiguration();
            if (self::$_pdo == null) {
                $dsn = sprintf("%s:dbname=%s;host=%s", 'mysqli' == $db['dbdriver'] ? 'mysql' : $db['dbdriver'], $db['database'], $db['hostname']);
                self::$_pdo = new PDO($dsn, $db['username'], $db['password']);
            }
            $this->_conn = $this->createDefaultDBConnection(self::$_pdo, $db['database']);
        }

        return $this->_conn;
    }

    /**
     * Returns the DB configuration params for the test database.
     * @return array
     * @see system/application/config/database.php
     */
    protected function _getCodeIgniterActiveTestDbConfiguration ()
    {
        // define these two vars to suppress these pesky 'undefined variable' warnings from Zend Studio
        $active_group = null;
        $db = array();

        include APPPATH . 'config/database' . EXT; // load in the CI database configuration
        /*
         * Sanity check:
         * Ensure that CI and PHPUnit are configured to use the same test database.
         */
        if ($active_group !== ILIOS_TEST_DB_ACTIVE_GROUP) {
            throw new Exception ("Test environment misconfiguration:\nThe application's configured active database group does not match the specified unit-test database.");
        }
        return $db[$active_group];
    }

    /**
     * Utility method for loading sets of test data from XML files.
     *
     * Provide one or more paths to the XML data sets in the $dataSetFilePaths argument.
     *
     * Note:
     * If a given file path is "absolute" (leading slash) then the it used to load the resource as-is.
     * However, if a file path is "relative" (no leading slash) then it is assumed that this is a path
     * relative to the ILIOS_WEB_ROOT/tests/_datasets directory.
     *
     * Usage Example:
     *
     * <code>
     * protected function getDataSet () {
     *   return $this->_getDataSet(array(
     *       'foo/user.xml',
     *       '/tmp/foo/user_x_user_group.xml'
     *   ));
     * }
     * </code>
     *
     * In is example, the test data sets from the files
     *
     * ILIOS_WEB_ROOT/tests/_datasets/foo/user.xml
     *
     * and
     *
     * /tmp/foo/user_x_user_group.xml
     *
     * are loaded.
     *
     * @param array $dataSetFilePaths
     * @return PHPUnit_Extensions_Database_DataSet_IDataSet
     */
    protected function _getDataSet (array $dataSetFilePaths = array())
    {
        if (empty($dataSetFilePaths)) {
            return new PHPUnit_Extensions_Database_DataSet_DefaultDataSet();
        }

        $dataSets = array();
        $dataSetsDirBasePath =  '_datasets';
        foreach ($dataSetFilePaths as $filePath) {
            if (0 !== strpos($filePath, '/')) {
                // prepend relative paths as described above.
                $filePath = $dataSetsDirBasePath . '/' . $filePath;
            }
            $dataSets[] = $this->createXMLDataSet($filePath);
        }
        $compositeDs = new PHPUnit_Extensions_Database_DataSet_CompositeDataSet($dataSets);
        return $compositeDs;
    }

    /**
     * (non-PHPdoc)
     * @see PHPUnit_Extensions_Database_TestCase::getTearDownOperation()
     */
    protected function getTearDownOperation ()
    {
        // Clean up after ourselves
        return new PHPUnit_Extensions_Database_Operation_Composite(array(
            PHPUnit_Extensions_Database_Operation_Factory::DELETE_ALL(), // 1. delete all records from table
            new Ilios_PHPUnit_Extensions_Database_Operation_ResetAutoincrement() // 2. reset auto increment value
        ));
    }

    /**
     * (non-PHPdoc)
     * @see PHPUnit_Extensions_Database_TestCase::getSetUpOperation ()
     */
    protected function getSetUpOperation ()
    {
        return new PHPUnit_Extensions_Database_Operation_Composite(array(
                PHPUnit_Extensions_Database_Operation_Factory::DELETE_ALL(), // 1. delete all records from table
                new Ilios_PHPUnit_Extensions_Database_Operation_ResetAutoincrement(), // 2. reset auto increment value
                PHPUnit_Extensions_Database_Operation_Factory::INSERT() // 3. insert new records
        ));
    }
}
