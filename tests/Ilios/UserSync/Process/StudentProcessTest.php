<?php
require_once dirname(dirname(__FILE__)) . '/ProcessTest.php';

/**
 * Test case for the student-user synchronization process.
 * @see Ilios2_UserSync_Process_StudentProcess
 */
class Ilios2_UserSync_Process_StudentProcessTest extends Ilios2_UserSync_ProcessTest
{
	/**
	 * @test
	 * @covers Ilios2_UserSync_Process_StudentProcess
	 * @group ilios2
     * @group user_sync
	 */
	public function testRun ()
	{
	    // ------------------------------
	    // set-up of the process environment
	    // -------------------------------
	    $processId = 1082703600;

	    // instantiate a external user source
	    // we use the "Array" source
	    $userSource = new Ilios2_UserSync_UserSource_Array($this->_config);
	    $process = new Ilios2_UserSync_Process_StudentProcess($userSource,
	    $this->_userDao, $this->_schoolDao, $this->_syncExceptionDao);

	    // -------------------------------
	    // run the process
	    // -------------------------------
	    $process->run($processId, $this->_logger);

	    // -------------------------------
	    // post-run, compare the state of the test database with the expected state
	    // -------------------------------

	    // 1. check the state of the user table
	    $query =<<<EOL
SELECT
user_id,
last_name,
first_name,
middle_name,
phone,
email,
added_via_ilios,
enabled,
uc_uid,
other_id,
primary_school_id,
examined,
user_sync_ignore
FROM `user`
ORDER BY user_id
EOL;
	    $this->_checkTableState('user', $query);

	    // 2. check the state of the user_x_user_role table
	    $query =<<<EOL
SELECT
user_id,
user_role_id
FROM
`user_x_user_role`
ORDER BY
user_id, user_role_id
EOL;
	    $this->_checkTableState('user_x_user_role', $query);


	    // 3. check the state of the user_sync_exception table
	    $query =<<<EOL
SELECT
exception_id,
process_id,
process_name,
user_id,
exception_code,
mismatched_property_name,
mismatched_property_value
FROM
`user_sync_exception`
WHERE
process_id = {$processId}
ORDER BY exception_id
EOL;
	    $this->_checkTableState('user_sync_exception', $query);
	}

	/**
	 * (non-PHPdoc)
	 * @see Ilios2_UserSync_ProcessTest::_getResourcePath()
	 */
	protected function _getResourcePath ()
	{
	    return ILIOS2_TEST_ROOT_DIR . '/_datasets/user_sync/student_process';
	}
}
