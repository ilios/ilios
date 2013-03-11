<?php

include_once "abstract_ilios_model.php";

/**
 * Data Access Object (DAO) to the audit tables.
 */
class Audit_Event extends Abstract_Ilios_Model
{
    /**
     * @deprecated
     * @see Ilios_Model_AuditUtils::DELETE_EVENT_TYPE
     * @var int
     */
    static public $DELETE_EVENT_TYPE = Ilios_Model_AuditUtils::DELETE_EVENT_TYPE;

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
    public function wrapAtom ($tableId, $tableColumn, $tableName, $type, $rootAtom = 0,
                       $serializedBlob = null) {
        $rhett = array();

        $rhett['table_row_id'] = $tableId;
        $rhett['table_column'] = $tableColumn;
        $rhett['table_name'] = $tableName;
        $rhett['event_type'] = $type;
        $rhett['root_atom'] = $rootAtom;
        $rhett['blob'] = $serializedBlob;

        return $rhett;
    }

    /**
     * Saves a given list of audit atoms.
     * Transactions are handled within this method.
     *
     * @param array $wrappedAtomArray an array of assoc. arrays, each sub-array
     *     as returned by the Ilios_Model_AuditUtils::wrapAtom() method.
     * @param int $userId
     * @return boolean
     * @todo refactor user session related code out of this function
     * @todo improve code docs
     */
    public function saveAuditEvent ($wrappedAtomArray, $userId = null)
    {
        $this->startTransaction();

        if ($userId == null) {
            $userId = $this->session->userdata('uid'); // @todo get this outta here.
        }

        $newRow = array();
        $newRow['audit_event_id'] = null;

        $dtTimeStamp = new DateTime('now', new DateTimeZone('UTC'));
        $newRow['time_stamp'] = $dtTimeStamp->format('Y-m-d H:i:s');
        $newRow['user_id'] = $userId;

        $this->db->insert($this->databaseTableName, $newRow);

        $newEventId = $this->db->insert_id();
        if ((! $newEventId) || ($newEventId == 0)) {
            $this->rollbackTransaction();

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
                $this->rollbackTransaction();

                return false;
            }
            else if (isset($wrappedAtom['blob']) && (! is_null($wrappedAtom['blob']))) {
                $newRow = array();
                $newRow['audit_atom_id'] = $newId;
                $newRow['serialized_state_event'] = $wrappedAtom['blob'];

                $this->db->insert('audit_content', $newRow);

                if ($this->db->affected_rows() == 0) {
                    $this->rollbackTransaction();

                    return false;
                }
            }
        }

        $this->commitTransaction();

        return true;
    }
}
