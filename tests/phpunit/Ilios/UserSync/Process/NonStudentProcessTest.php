<?php
require_once 'Ilios/UserSync/ProcessTest.php';

/**
 * Test case for the non-student-user synchronization process.
 * @see Ilios_UserSync_Process_NonStudentProcess
 */
class Ilios_UserSync_Process_NonStudentProcessTest extends Ilios_UserSync_ProcessTest
{
	/**
	 * @test
	 * @covers Ilios_UserSync_Process_NonStudentProcess
	 * @group non_student_sync
	 * @group ilios
     * @group user_sync
	 */
	public function testRun ()
	{
	    // ------------------------------
	    // set-up of the process environment
	    // -------------------------------
	    $processId = 1319576361;

	    // instantiate a external user source
	    // we use the "Array" source
	    $userSource = new Ilios_UserSync_UserSource_Array($this->_config);
	    $process = new Ilios_UserSync_Process_NonStudentProcess($userSource,
	    $this->_userDao, $this->_syncExceptionDao);

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
	 * @see Ilios_UserSync_ProcessTest::_getResourcePath()
	 */
	protected function _getResourcePath ()
	{
	    return dirname(__FILE__) . '/_datasets/non_student_process';
	}
}
