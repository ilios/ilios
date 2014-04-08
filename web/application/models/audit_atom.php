<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

include_once "ilios_base_model.php";

/**
 * Data Access Object (DAO) to the audit atom table.
 */
class Audit_Atom extends Ilios_Base_Model
{
    /**
     * Constructor.
     */
    public function __construct ()
    {
        parent::__construct('audit_atom', array('audit_atom_id'));
    }

    /**
     * Saves a given list of audit atoms related to actions taken by a given user.
     *
     * @param array $wrappedAtomArray An array of assoc. arrays, each sub-array
     *     as returned by the Ilios_Model_AuditUtils::wrapAuditAtom() method.
     * @param int $userId The user id.
     * @return boolean TRUE on success, FALSE on failure.
     * @see Ilios_Model_AuditUtils::wrapAuditAtom()
     */
    public function saveAuditEvent ($wrappedAtomArray, $userId)
    {
        $dtTimeStamp = new DateTime('now', new DateTimeZone('UTC'));
        $createdAt = $dtTimeStamp->format('Y-m-d H:i:s');

        foreach ($wrappedAtomArray as $wrappedAtom) {
            $newRow = array();
            $newRow['audit_atom_id'] = null;
            $newRow['table_row_id'] = $wrappedAtom['table_row_id'];
            $newRow['table_column'] = $wrappedAtom['table_column'];
            $newRow['table_name'] = $wrappedAtom['table_name'];
            $newRow['event_type'] = $wrappedAtom['event_type'];
            $newRow['created_at'] = $createdAt;
            $newRow['created_by'] = $userId;

            $this->db->insert('audit_atom', $newRow);

            $newId = $this->db->insert_id();
            if (! $newId) {
                return false;
            }
        }
        return true;
    }

    /**
     * Retrieves audit events
     * @param string $from
     * @param string $to
     * @return array a nested array of events
     */
    public function getAuditEvents ($from = false, $to = false)
    {
        $events = array();
        $fromTimeStamp = new DateTime($from?$from:'0000-00-00', new DateTimeZone('UTC'));
        $toTimeStamp = new DateTime($to?$to:'now', new DateTimeZone('UTC'));
        
        $this->db->select('*');
        $this->db->from($this->getTableName());
        $this->db->join('user', $this->getTableName() . '.created_by = user.user_id', 'left');
        $this->db->where('created_at >', $fromTimeStamp->format('c'));
        $this->db->where('created_at <', $toTimeStamp->format('c'));
        $this->db->order_by("created_at", "asc");

        $query = $this->db->get();
        foreach ($query->result_array() as $row) {
            $row['nice_event_type'] = $this->_niceEventType($row['event_type']);
            $events[] = $row;
        }
        $query->free_result();
        return $events;
    }

    /**
     * Remove old audit events
     * @param string $to
     * @return array a nested array of events
     */
    public function removeEventsOlderThan ($to)
    {
        $events = array();
        $toTimeStamp = new DateTime($to, new DateTimeZone('UTC'));
        $this->db->where('created_at <', $toTimeStamp->format('c'));

        $query = $this->db->delete($this->getTableName());
        return $this->db->affected_rows();
    }
    
    protected function _niceEventType($type)
    {
        switch($type){
            case Ilios_Model_AuditUtils::CREATE_EVENT_TYPE:
                $string = 'Created';
                break;
            case Ilios_Model_AuditUtils::UPDATE_EVENT_TYPE:
                $string = 'Updated';
                break;
            case Ilios_Model_AuditUtils::DELETE_EVENT_TYPE:
                $string = 'Deleted';
                break;
            default:
                $string = '';
                break;
        }
        return $string;
    }
}
