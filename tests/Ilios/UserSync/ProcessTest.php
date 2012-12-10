<?php
require_once dirname(dirname(__FILE__)) . '/CI/TestCase.php';

/**
 * Base class for user synchronization test cases.
 * @see Ilios_UserSync_Process
 */
abstract class Ilios_UserSync_ProcessTest extends Ilios_CI_TestCase
{
    /**
     * @var Ilios_Logger
     */
    protected $_logger;
	/**
	 * @var User
	 */
	protected $_userDao;

	/**
	 * @var School
	 */
	protected $_schoolDao;

	/**
	 * @var User_Sync_Exception
	 */
	protected $_syncExceptionDao;

	/* (non-PHPdoc)
	 * @see PHPUnit_Framework_TestCase::setUp()
	 */
	protected function setUp()
	{
	    parent::setUp();

	    $this->_logger = Ilios_Logger::getInstance(ILIOS_TEST_USER_SYNC_LOG_FILE_PATH); // @see tests/phpunit.xml
	    $this->_controller->load->model('User_Sync_Exception', 'user_sync_exception', true);
	    $this->_userDao = $this->_controller->user;
	    $this->_schoolDao = $this->_controller->school;
	    $this->_syncExceptionDao = $this->_controller->user_sync_exception;
	    $this->_config = $this->_getConfig();
	}


	/* (non-PHPdoc)
	 * @see PHPUnit_Extensions_Database_TestCase::getDataSet()
	 */
	protected function getDataSet() {
		$resourcePath = $this->_getResourcePath();
	    return $this->_getDataSet(array(
	        $resourcePath . '/pre/user.xml',
	    	$resourcePath . '/pre/user_x_user_role.xml',
	    	$resourcePath . '/pre/user_sync_exception.xml',
	    ));
	}

	/**
	 * Returns configuration parameters for the "Array" implementation of an external data source.
	 * The external user records are return in the array under ['array']['users']
	 * @return array a nested array of config params.
	 * @see Ilios_UserSync_UserSource_Array
	 */
	protected function _getConfig()
	{
	    $config = array();
	    $config['array']['users'] = $this->_getTestUserData();
	    return $config;
	}

	/**
	 * Returns an list of external user records, which can be fed into an array data source.
	 * @return array a nested array representing external user data
	 * @see Ilios_UserSync_UserSource_Array
	 */
	protected function _getTestUserData ()
	{
	    $externalUsers = array();
	    $fixturesFilePath = $this->_getResourcePath() . '/external_users.php';
	    include $fixturesFilePath;
	    return $externalUsers;
	}

	/**
	 * Loads and returns a data set representing the expected state of
	 * a given database table after the user sync process has run.
	 * @param string $tableName
	 * @return PHPUnit_Extensions_Database_DataSet_ITable
	 */
	protected function _getPostSyncExpectedDataTable ($tableName)
	{
	    $path = $this->_getResourcePath() . "/post/{$tableName}.xml";
	    return $this->createXMLDataSet($path)->getTable($tableName);
	}

	/**
	 * Compares the state of a given table by comparing
	 * an expected state (retrieved from an XML data set) with the actual
	 * state (retrieved from the test DB via a given SQL query.
	 * @param string $tableName table name
	 * @param string $query SQL query
	 * @link http://www.phpunit.de/manual/3.5/en/database.html#asserting-the-state-of-a-table
 	 */
	protected function _checkTableState ($tableName, $query)
	{
	    $expectedTable = $this->_getPostSyncExpectedDataTable($tableName);
	    $actualTable = $this->getConnection()->createQueryTable($tableName, $query);
	    $this->assertTablesEqual($expectedTable, $actualTable);
	}

	/**
	 * Returns the path to the fixtures directory for this process.
	 * @return string
	 */
	abstract protected function _getResourcePath ();
}
