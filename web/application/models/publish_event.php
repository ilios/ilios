<?php

include_once "abstract_ilios_model.php";

/**
 * Data Access Object (DAO) to the "publish event" table.
 */
class Publish_Event extends Abstract_Ilios_Model {

    public function __construct ()
    {
        parent::__construct('publish_event', array('publish_event_id'));
    }

    /**
     * @return the id of the newly inserted row or 0 on failure
     */
    public function addPublishEvent ($tableName, $tableRowId, $machineIP, &$auditAtoms)
    {
        $newRow = array();
        $newRow['publish_event_id'] = null;

        $newRow['administrator_id'] = $this->session->userdata('uid');
        $newRow['machine_ip'] = $machineIP;

        $dtTimeStamp = new DateTime('now', new DateTimeZone('UTC'));
        $newRow['time_stamp'] = $dtTimeStamp->format('Y-m-d H:i:s');

        $newRow['table_name'] = $tableName;
        $newRow['table_row_id'] = $tableRowId;

        $this->db->insert($this->databaseTableName, $newRow);

        $newId = $this->db->insert_id();

        array_push($auditAtoms, $this->auditEvent->wrapAtom($newId, 'publish_event_id',
                                                            $this->databaseTableName,
                                                            Audit_Event::$CREATE_EVENT_TYPE));

        return $newId;
    }

    /*
     * We do no audit event atom creation here because an update is only ever done in certain cases
     *  as a pair to an add (which always creates an atom).
     */
    public function updatePublishEventTableRowIdColumn ($publishId, $newColumnValue)
    {
        $updateRow = array();
        $updateRow['table_row_id'] = $newColumnValue;

        $this->db->where('publish_event_id', $publishId);
        $this->db->update($this->databaseTableName, $updateRow);
    }

}
