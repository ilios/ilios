<?php

include_once "abstract_ilios_model.php";

/**
 * Data Access Object (DAO) to the "permission" table.
 */
class Permission extends Abstract_Ilios_Model
{
    /**
     * Constructor
     */
    public function __construct ()
    {
        parent::__construct('permission', array('permission_id'));
        $this->load->model('Course', 'course', TRUE);
        $this->load->model('Program', 'program', TRUE);
        $this->load->model('School', 'school', TRUE);
    }

    /**
     * Retrieves a permission object by its id.
     * @param int $permissionId the permission id
     * @return array will return an associative array representing a permissions object
     *          . permission_id
     *          . can_read
     *          . can_write
     *          . object
     *          . object_name ("course", "program" or "school")
     *          . ... attributes that one would expect for a shallow representation of that object
     */
    public function getPermissionObjectForRowId ($permissionId)
    {
        $row = $this->convertStdObjToArray($this->getRowForPrimaryKeyId($permissionId));
        return $this->_getPermissionObjectForRow($row);
    }

    /**
     * Retrieves a list of permissions for a given user, user/table or user/table/row combination.
     * @param int $userId user id
     * @param string $tableName table name (optional)
     * @param int $tableRowId row id (optional)
     * @return boolean TRUE on success, FALSE on failure
     * @return array will return a list of associative arrays, each representing a permission object:
     *          . permission_id
     *          . can_read
     *          . can_write
     *          . object
     *          . object_name ("course", "program" or "school")
     *          . ... attributes that one would expect for a shallow representation of that object
     */
    public function getPermissionsForUser ($userId, $tableName = null, $tableRowId = null)
    {
        $rhett = array();

        $this->db->where('user_id', $userId);

        if (! is_null($tableName)) {
            $this->db->where('table_name', $tableName);

            if (! is_null($tableRowId)) {
                $this->db->where('table_row_id', $tableRowId);
            }
        }

        $queryResults = $this->db->get($this->databaseTableName);

        foreach ($queryResults->result_array() as $row) {
            array_push($rhett, $this->_getPermissionObjectForRow($row));
        }
        return $rhett;
    }

    /**
     * Remove permissions for a given user or user/table or user/table/row combination.
     * @param int $userId user id
     * @param string $tableName table name (optional)
     * @param int $tableRowId row id (optional)
     * @return boolean TRUE on success, FALSE on failure
     */
    public function deletePermissionsForUser ($userId, $tableName = null, $tableRowId = null)
    {
        $this->db->where('user_id', $userId);

        if (!is_null($tableName)) {
            $this->db->where('table_name', $tableName);

            if (! is_null($tableRowId)) {
                $this->db->where('table_row_id', $tableRowId);
            }
        }
        $this->db->delete($this->databaseTableName);
        return $this->transactionAtomFailed();
    }

    /**
     * Adds or updates given permissions for a given user/table/row combination.
     * @param int $userId the user id
     * @param string $tableName the table name
     * @param int $tableRowId the row id
     * @param boolean $canRead the read-permission flag
     * @param boolean $canWrite the write-permission flag
     * @return int|boolean the newly-added/updated permission record id, or FALSE on failure
     */
    public function setPermissionsForUser ($userId, $tableName, $tableRowId, $canRead, $canWrite)
    {
        $existingPermissions = $this->getPermissionsForUser($userId, $tableName, $tableRowId);
        switch (count($existingPermissions)) {
            case 0 :  // no existing perms - add new ones
                $permissionId = $this->addPermissionForUser($userId, $tableName, $tableRowId, $canRead, $canWrite);
                if ($permissionId) { // return new permission id
                    return $permissionId;
                }
                break;
            case 1 : // found an existing permissions record for the given user/table/row combo. update it.
                $permissionId = $existingPermissions[0]['permission_id'];
                $success = $this->updatePermission($permissionId, $canRead, $canWrite);
                if ($success) {
                    return $permissionId;
                }
                break;
            default :
                // more than one matches?
                // something is borked here, this indicates that some input is missing.
                // do nothing here for now and just return FALSE below.
        }
        return false;
    }

    /**
     * Adds or given permissions for a given user/table/row combination.
     * @param int $userId the user id
     * @param string $tableName the table name
     * @param int $tableRowId the row id
     * @param boolean $canRead the read-permission flag
     * @param boolean $canWrite the write-permission flag
     * @return int|boolean the newly-added permission record id, or FALSE on failure
     */
    public function addPermissionForUser ($userId, $tableName, $tableRowId, $canRead, $canWrite)
    {
        $data = array();
        $data['user_id'] = $userId;
        $data['table_name'] = $tableName;
        $data['table_row_id'] = $tableRowId;
        $data['can_read'] = ($canRead ? 1 : 0);
        $data['can_write'] = ($canWrite ? 1 : 0);
        $this->db->insert($this->databaseTableName, $data);
        $id = $this->db->insert_id();
        if ($id) {
            return $id;
        }
        return false;
    }

    /**
     * Updates the read/write attributes for a given permission record.
     * @param int $permissionId the permission record id.
     * @param boolean $canRead the read permission flag
     * @param boolean $canWrite the write permission flag
     * @return boolean TRUE on successful update, FALSE on failure
     */
    public function updatePermission ($permissionId, $canRead, $canWrite)
    {
        $data = array();
        $data['can_read'] = ($canRead ? 1 : 0);
        $data['can_write'] = ($canWrite ? 1 : 0);
        $this->db->where('permission_id', $permissionId);
        if ($this->db->update($this->databaseTableName, $data)) {
            return true;
        }
        return false;
    }

    /**
     * @todo add code docs
     * @param int $row
     * @return array
     */
    protected function _getPermissionObjectForRow ($row)
    {
        $permissionObject = array();

        $permissionObject['permission_id'] = $row['permission_id'];
        $permissionObject['can_read'] = $row['can_read'];
        $permissionObject['can_write'] = $row['can_write'];

        $objectRow = null;
        if ($row['table_name'] == 'program') {
            $objectRow = $this->program->getRowForPrimaryKeyId($row['table_row_id']);
        } else if ($row['table_name'] == 'course') {
            $objectRow = $this->course->getRowForPrimaryKeyId($row['table_row_id']);
        } else if ($row['table_name'] == 'school') {
            $objectRow = $this->school->getRowForPrimaryKeyId($row['table_row_id']);
        }

        if (! is_null($objectRow)) {
            $shallowObject = $this->convertStdObjToArray($objectRow);
            $shallowObject['object_name'] = $row['table_name'];
            $permissionObject['object'] = $shallowObject;
        } else {
            $permissionObject['table_name'] = $row['table_name'];
            $permissionObject['table_row_id'] = $row['table_row_id'];
        }

        return $permissionObject;
    }
}
