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
            if ((! $newId) || ($newId == 0)) {
                return false;
            }
        }
        return true;
    }
}
