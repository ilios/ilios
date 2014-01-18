<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

include_once "ilios_base_model.php";

/**
 * Data Access Object (DAO) to the audit tables.
 */
class Audit_Event extends Ilios_Base_Model
{
    /**
     * Constructor.
     */
    public function __construct ()
    {
        parent::__construct('audit_event', array('audit_event_id'));
    }

    /**
     * @deprecated
     * Use <code>Ilios_Model_AuditUtils::wrapAtom() instead.
     * @see Ilios_Model_AuditUtils::wrapAtom()
     */
    public function wrapAtom ($tableId, $tableColumn, $tableName, $type)
    {
        $rhett = array();

        $rhett['table_row_id'] = $tableId;
        $rhett['table_column'] = $tableColumn;
        $rhett['table_name'] = $tableName;
        $rhett['event_type'] = $type;
        return $rhett;
    }

    /**
     * Saves a given list of audit atoms related to actions taken by a given user.
     *
     * @param array $wrappedAtomArray An array of assoc. arrays, each sub-array
     *     as returned by the Ilios_Model_AuditUtils::wrapAtom() method.
     * @param int $userId The user id.
     * @return boolean TRUE on success, FALSE on failure.
     * @see Ilios_Model_AuditUtils::wrapAtom()
     */
    public function saveAuditEvent ($wrappedAtomArray, $userId)
    {
        $newRow = array();
        $newRow['audit_event_id'] = null;

        $dtTimeStamp = new DateTime('now', new DateTimeZone('UTC'));
        $newRow['time_stamp'] = $dtTimeStamp->format('Y-m-d H:i:s');
        $newRow['user_id'] = $userId;

        $this->db->insert($this->databaseTableName, $newRow);

        $newEventId = $this->db->insert_id();
        if ((! $newEventId) || ($newEventId == 0)) {
            return false;
        }

        foreach ($wrappedAtomArray as $wrappedAtom) {
            $newRow = array();
            $newRow['audit_atom_id'] = null;

            $newRow['table_row_id'] = $wrappedAtom['table_row_id'];
            $newRow['table_column'] = $wrappedAtom['table_column'];
            $newRow['table_name'] = $wrappedAtom['table_name'];
            $newRow['event_type'] = $wrappedAtom['event_type'];
            $newRow['root_atom'] = $wrappedAtom['root_atom'];
            $newRow['audit_event_id'] = $newEventId;

            $this->db->insert('audit_atom', $newRow);

            $newId = $this->db->insert_id();
            if ((! $newId) || ($newId == 0)) {
                return false;
            }
        }
        return true;
    }
}
