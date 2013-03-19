<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

include_once "ilios_base_model.php";

/**
 * Data Access Object for the "alert" and "alert_*" tables in the Ilios database.
 */
class Alert extends Ilios_Base_Model
{
    /*
     * These constanct should be used to communicate via addOrUpdateAlert what change types
     *  should be associated with the alert.
     *
     * This assumes the database has been populated via the 'alert_change_type_data.sql' file.
     */
    /**
     * Indicates a course director change.
     * @var int
     */
    const CHANGE_TYPE_COURSE_DIRECTOR = 5;
    /**
     * Indicates a course instructor change.
     * @var int
     */
    const CHANGE_TYPE_INSTRUCTOR = 4;
    /**
     * Indicates a learning material change.
     * @var int
     */
    const CHANGE_TYPE_LEARNING_MATERIAL = 3;
    /**
     * Indicates a learner group change.
     * @var int
     */
    const CHANGE_TYPE_LEARNER_GROUP = 6;
    /**
     * Indicates a location change.
     * @var int
     */
    const CHANGE_TYPE_LOCATION = 2;
    /**
     * Indicates the addition of a new offering.
     * @var int
     */
    const CHANGE_TYPE_NEW_OFFERING = 7;
    /**
     * Indicates a session's publication status change.
     * @var int
     */
    const CHANGE_TYPE_SESSION_PUBLISH = 8;
    /**
     * Indicates a time change.
     * @var int
     */
    const CHANGE_TYPE_TIME = 1;

    /**
     * Constructor.
     */
    public function __construct ()
    {
        parent::__construct('alert', array('alert_id'));
        $this->load->model('Audit_Event', 'auditEvent', TRUE);
        $this->load->model('User', 'user', TRUE);
    }

    /**
     * @todo add code docs
     */
    public function getAllUndispatchedAlerts ()
    {
        $rhett = array();

        $this->db->where('dispatched', 0);
        $queryResults = $this->db->get($this->databaseTableName);
        foreach ($queryResults->result_array() as $row) {
            array_push($rhett, $row);
        }

        return $rhett;
    }

    /**
     * Retrieves all undisplatched change alerts for a given school and table.
     * @param int $schoolId
     * @param string $tableName
     * @return array a nested array of alerts
     */
    public function getUndispatchedAlertsBySchoolAndTable ($schoolId, $tableName)
    {
        $rhett = array();

        $this->db->distinct();
        $this->db->select('alert.*');
        $this->db->join('alert_recipient', 'alert_recipient.alert_id = alert.alert_id');
        $this->db->where('alert.table_name', $tableName);
        $this->db->where('alert_recipient.school_id', $schoolId);
        $this->db->where('alert.dispatched', 0);

        $queryResults = $this->db->get($this->databaseTableName);

        foreach ($queryResults->result_array() as $row) {
        	array_push($rhett, $row);
        }

        return $rhett;
    }

    /**
     * @todo add code docs
     * @param int $tableId
     * @param string $tableName
     * @return array|NULL
     */
    public function getUndispatchedAlertForTable ($tableId, $tableName)
    {
        $rhett = null;

        $this->db->where('table_row_id', $tableId);
        $this->db->where('table_name', $tableName);
        $this->db->where('dispatched', 0);
        $queryResults = $this->db->get($this->databaseTableName);
        if ($queryResults->num_rows() > 0) {
            $rhett = $this->convertStdObjToArray($queryResults->first_row());
        }
        return $rhett;
    }

    /**
     * @todo add code docs
     * @param int $alertId
     * @return array
     */
    public function getChangeTypesForAlert ($alertId)
    {
        $rhett = array();

        $this->db->where('alert_id', $alertId);
        $queryResults = $this->db->get('alert_change');
        foreach ($queryResults->result_array() as $row) {
            $change_type = $this->getRow('alert_change_type', 'alert_change_type_id',
                                         $row['alert_change_type_id']);
            $rhett[] = $change_type->title;
        }

        return $rhett;
    }

    /**
     * Retrieves a change history for a given alert by referencing the
     * audit and user tables.
     * @param int $alertId
     * @return array a nested array of arrays, each sub-array containing data keyed off by the following
     *     'time_stamp' ... timestamp of when the change was made
     *     'user_id'     ... id of the user who made the change
     *     'first_name'  ... first name of the user who made the change
     *     'last_name'   ... last name of the user who made the change
     */
    public function getChangeHistoryForAlert ($alertId)
    {
        $rhett = array();
        $clean = array();
        $clean['alert_id'] = (int) $alertId;
        $sql =<<< EOL
SELECT DISTINCT
ae.time_stamp,
u.user_id,
u.last_name,
u.first_name
FROM alert a
JOIN audit_atom aa ON aa.table_row_id = a.alert_id
JOIN audit_event ae ON ae.audit_event_id = aa.audit_event_id
JOIN user u ON u.user_id = ae.user_id
WHERE aa.table_name = 'alert'
AND a.alert_id = {$clean['alert_id']}
EOL;
        $query = $this->db->query($sql);
        if (0 < $query->num_rows()) {
            foreach ($query->result_array() as $row) {
                $rhett[] = $row;
            }
        }
        $query->free_result();
        return $rhett;
    }

    /**
     * @todo add code docs
     * @param int $alertId
     * @return array
     */
    public function getRecipientsForAlert ($alertId)
    {
        $rhett = array();

        $this->db->where('alert_id', $alertId);
        $queryResults = $this->db->get('alert_recipient');
        foreach ($queryResults->result_array() as $row) {
            $rhett[] = $this->convertStdObjToArray($this->user->getRowForPrimaryKeyId($row['user_id']));
        }
        return $rhett;
    }

    /**
     * Transactionality will be handled in this method
     *
     * @param int $alertId
     * @todo improve code docs
     */
    public function markAlertAsDispatched ($alertId)
    {
        $this->startTransaction();

        $this->db->where('alert_id', $alertId);

        $updateRow = array();
        $updateRow['dispatched'] = 1;
        $this->db->update($this->databaseTableName, $updateRow);

        $this->commitTransaction();
    }

    /**
     * Marks a given list of alerts as "dispatched".
     * @param array $alertIds an array of alert ids.
     */
    public function markAlertsAsDisplatched(array $alertIds)
    {
        if (empty($alertIds)) {
            return;
        }

        $this->startTransaction();

        $updateRow = array();
        $updateRow['dispatched'] = 1;

        $this->db->where_in('alert_id', $alertIds);
        $this->db->update($this->databaseTableName, $updateRow);

        $this->commitTransaction();
    }


    /**
     * Transactionality will be handled in this method
     *
     * Saves a change alert for a given application entity.
     * @param int $tableId the record id of the changed entity
     * @param string $tableName the database table name where the changed entity is stored
     * @param int $userId the current user id
     * @param array $school an assoc. array representing a school record
     * @param array $changeTypes an array containing one or more of the CHANGE_TYPE_* constants defined in this class.
     * @return string|NULL an error message of NULL if everything went fine
     */
    public function addOrUpdateAlert ($tableId, $tableName, $userId, $school = array(), $changeTypes = array())
    {
        $preExisting = $this->getUndispatchedAlertForTable($tableId, $tableName);

        $this->startTransaction();

        if (! is_null($preExisting)) {
            $alertId = $preExisting['alert_id'];

            $eventType = Ilios_Model_AuditUtils::UPDATE_EVENT_TYPE;
        } else {
            $newRow = array();
            $newRow['alert_id'] = null;

            $newRow['table_row_id'] = $tableId;
            $newRow['table_name'] = $tableName;
            $newRow['dispatched'] = 0;

            $this->db->insert($this->databaseTableName, $newRow);

            $alertId = $this->db->insert_id();

            $eventType = Ilios_Model_AuditUtils::CREATE_EVENT_TYPE;
        }

        if ($alertId == -1) {
            $this->rollbackTransaction();

            $lang = $this->getLangToUse();
            $msg = $this->languagemap->getI18NString('general.error.db_insert', $lang);

            return $msg;
        }

        $count = 0;
        $affectedCount = 0;

        $count++;
        $affectedCount += $this->insertSingleCrossTablePair('alert_instigator', 'alert_id',
                                                            $alertId, 'user_id', $userId);

        if ($count == $affectedCount) {
            $count++;
            $affectedCount += $this->insertSingleCrossTablePair('alert_recipient',
                                                                'alert_id', $alertId,
                                                            'school_id', $school['school_id']);
        }

        if ($count == $affectedCount) {
            foreach ($changeTypes as $changeType) {
                if ($count != $affectedCount) {
                    break;
                }

                $count++;
                $affectedCount += $this->insertSingleCrossTablePair('alert_change',
                                                                'alert_id', $alertId,
                                                                'alert_change_type_id',
                                                                $changeType);
            }
        }

        if ($count != $affectedCount) {
            $this->rollbackTransaction();

            $lang = $this->getLangToUse();
            $msg = $this->languagemap->getI18NString('general.error.db_insert', $lang);

            return $msg;
        }

        $this->commitTransaction();

        $atoms = array();
        array_push($atoms, $this->auditEvent->wrapAtom($alertId, 'alert_id', 'alert',
                                                       Ilios_Model_AuditUtils::CREATE_EVENT_TYPE, 1));
        $this->auditEvent->saveAuditEvent($atoms, $userId);

        return null;
    }
}
