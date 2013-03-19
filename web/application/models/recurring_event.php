<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

include_once "ilios_base_model.php";

/**
 * Data Access Object (DAO) to the "recurring event" table.
 */
class Recurring_Event extends Ilios_Base_Model {

    public function __construct ()
    {
        parent::__construct('recurring_event', array('recurring_event_id'));
    }

    /**
     * Transactionality assumed to be handled outside this code
     *
     * @param $recurringEvent this is assumed to be the de-JSON'd version of the javascript land
     *                          model
     * @return the recurring_event_id due to the save (will be the one in the passed object if this
     *              is an update)
     */
    public function saveRecurringEvent ($recurringEvent, &$auditAtoms)
    {
        $recurringEventId = $recurringEvent['dbId'];
        $events = $recurringEvent['eventDays'];

        $rowData = array();
        $rowData['on_sunday'] = $events['0'];
        $rowData['on_monday'] = $events['1'];
        $rowData['on_tuesday'] = $events['2'];
        $rowData['on_wednesday'] = $events['3'];
        $rowData['on_thursday'] = $events['4'];
        $rowData['on_friday'] = $events['5'];
        $rowData['on_saturday'] = $events['6'];

        $rowData['end_date'] = $recurringEvent['mysqldEndDate'];

        if ($recurringEvent['endDateSetExplicitly'] != 1) {
            $rowData['repetition_count'] = $recurringEvent['repetitionCount'];
        }
        else {
            $rowData['repetition_count'] = 0;
        }

        if ($recurringEventId == -1) {
            $rowData['recurring_event_id'] = null;

            $this->db->insert($this->databaseTableName, $rowData);

            $recurringEventId = $this->db->insert_id();

            if (! $recurringEventId) {
                return -1;
            }
            else {
                array_push($auditAtoms,
                           $this->auditEvent->wrapAtom($recurringEventId, 'recurring_event_id',
                                                       $this->databaseTableName,
                                                       Ilios_Model_AuditUtils::CREATE_EVENT_TYPE));
            }
        }
        else {
            $this->db->where('recurring_event_id', $recurringEventId);
            $this->db->update($this->databaseTableName, $rowData);

            array_push($auditAtoms, $this->auditEvent->wrapAtom($recurringEventId,
                                                                'recurring_event_id',
                                                                $this->databaseTableName,
                                                                Ilios_Model_AuditUtils::UPDATE_EVENT_TYPE));
        }

        return $recurringEventId;
    }
}
