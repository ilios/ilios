<?php
/**
 * Static class providing auditing utilities and constants.
 */
class Ilios2_Model_AuditUtils
{
    /**
     * Used to indicate a create-event in the audit trail.
     * @var int
     */
    const CREATE_EVENT_TYPE = 1;

    /**
     * Used to indicate an update-event in the audit trail.
     * @var int
     */
    const UPDATE_EVENT_TYPE = 2;

    /**
     * Used to indicate a delete-event in the audit trail.
     * @var int
     */
    const DELETE_EVENT_TYPE = 3;


    /**
     * This function prepares a data structure which can
     * later be handed to the saveEvents(...) method which then performs the database inserts.
     *
     * @todo improve code docs
     * @param int $tableId
     * @param string $tableColumn
     * @param string $tableName
     * @param int $type audit event type
     * @param int $rootAtom
     * @param string $serializedBlob
     * @return array an associative array with values keyed off by
     *   'table_row_id' ... (int)
     *   'table_column' ... (string)
     *   'table_name' ... (string)
     *   'event_type' ... (int)
     *   'root_atom' ... (int)
     *   'blob' ... (string|NULL)
     */
    public static function wrapAuditAtom ($tableId, $tableColumn, $tableName,
            $type, $rootAtom = 0, $serializedBlob = null)
    {
    	$rhett = array();

    	$rhett['table_row_id'] = $tableId;
    	$rhett['table_column'] = $tableColumn;
    	$rhett['table_name'] = $tableName;
    	$rhett['event_type'] = $type;
    	$rhett['root_atom'] = $rootAtom;
    	$rhett['blob'] = $serializedBlob;

    	return $rhett;
    }
}
