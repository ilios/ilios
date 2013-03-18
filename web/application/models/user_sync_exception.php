<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

include_once 'abstract_ilios_model.php';

/**
 * Data Access Object to the "user_sync_exception" database table.
 */
class User_Sync_Exception extends Abstract_Ilios_Model
{
    /**
     * Constructor
     */
    public function __construct ()
    {
        parent::__construct('user_sync_exception', array('exception_id'));
    }

    /**
     * Adds a new new exception record to the datatabase table.
     * @param int $processId process run id
     * @param string $processName name of the process in which the exception occurred
     * @param int $userId record id of the user for whom this exception got raised
     * @param int $exceptionCode the user exception code
     * @param string $mismatchedPropertyName
     * @param mixed $mismatchedPropertyValue
     * @return int|bool the new record id or FALSE on failure
     * @see Ilios_UserSync_Process_UserException
     */
    public function addException ($processId, $processName, $userId, $exceptionCode,
                        $mismatchedPropertyName = null, $mismatchedPropertyValue = null)
    {
        $newRow = array();
        $newRow['process_id'] = (int) $processId;
        $newRow['process_name'] = $processName;
        $newRow['user_id'] = (int) $userId;
        $newRow['exception_code'] = (int) $exceptionCode;
        if (isset($mismatchedPropertyName)) {
            $newRow['mismatched_property_name'] = $mismatchedPropertyName;
            if (isset($mismatchedPropertyValue)) {
                $newRow['mismatched_property_value'] = $mismatchedPropertyValue;
            }
        }
        $this->db->insert($this->databaseTableName, $newRow);

        return $this->db->insert_id();
    }

    /**
     * Returns all recorded sync exceptions for users within a given school.
     *
     * @param int $schoolId
     * @return array
     */
    public function getExceptions ($schoolId)
    {
        $clean = array();
        $clean['school_id'] = (int) $schoolId;
        $sql =<<< EOL
SELECT ex.*
FROM
`user_sync_exception` ex
JOIN `user` u
ON u.`user_id` = ex.`user_id`
WHERE u.`primary_school_id` = {$clean['school_id']}
ORDER BY u.`last_name`, u.`first_name`, u.`user_id`
EOL;
        $query = $this->db->query($sql);
        return $query->result_array();
    }

    /**
     * Deletes all recorded sync exceptions generated from a given process.
     * @param string $processName
     */
    public function deleteExceptions ($processName)
    {
        // input validation
        // process name must be given
        if ('' == $processName) {
            return false;
        }
        $this->db->where('process_name', $processName);
        $this->db->delete($this->databaseTableName);
    }

    /**
     * Checks if enabled, non-flagged users within a given school have sync exceptions
     * that were generated via the non-student sync process.
     * @param int $schoolId the school id
     * @return boolean TRUE if sync exceptions were found, FALSE otherwise
     */
    public function hasNonStudentSyncExceptions ($schoolId)
    {
        return $this->_hasSyncExceptions($schoolId, Ilios_UserSync_Process_StudentProcess::SIGNATURE);
    }

    /**
     * Checks if enabled, non-flagged users within a given school have sync exceptions
     * that were generated via the student sync process.
     * @param int $schoolId the school id
     * @return boolean TRUE if sync exceptions were found, FALSE otherwise
     */
    public function hasStudentSyncExceptions ($schoolId)
    {
        return $this->_hasSyncExceptions($schoolId, Ilios_UserSync_Process_NonStudentProcess::SIGNATURE);
    }

    /**
     * Checks if enabled, non-flagged users within a given school have sync exceptions
     * that were generated via a given process.
     * @param int $schoolId the school id
     * @param string $processName the name of the process
     * @return boolean TRUE if sync exceptions were found, FALSE otherwise
     */
    protected function _hasSyncExceptions ($schoolId, $processName)
    {
        $rhett = false;
        $clean = array();
        $clean['school_id'] = (int) $schoolId;
        $clean['process_name'] = $this->db->escape($processName);

        $sql =<<<EOL
SELECT COUNT(*) AS c
FROM `user_sync_exception` ex
JOIN `user` u ON u.`user_id` = ex.`user_id`
WHERE u.enabled = 1
AND u.user_sync_ignore = 0
AND u.`primary_school_id` = {$clean['school_id']}
AND ex.`process_name` = {$clean['process_name']}
EOL;
        $query = $this->db->query($sql);
        if (0 < $query->num_rows()) {
            $row = $query->row_array();
            $rhett = 0 < (int) $row['c'] ? true : false; // check the count
        }
        $query->free_result();
        return $rhett;
    }
}
