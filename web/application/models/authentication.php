<?php

include_once "abstract_ilios_model.php";

/**
 * Data Access Object (DAO) to the authentication table.
 */
class Authentication extends Abstract_Ilios_Model
{

    public function __construct ()
    {
        parent::__construct('authentication', array('person_id'));
    }

    /**
     * Updates a given hashed password for a given user.
     * @param int $userId the user id
     * @param string $hash the hashed password
     * @return boolean TRUE on update, FALSE otherwise
     */
    public function changePassword ($userId, $hash)
    {
        $updateRow = array();
        $updateRow['password_sha256'] = $hash;

        $this->db->where('person_id', $userId);
        $this->db->update($this->databaseTableName, $updateRow);

        return ($this->db->affected_rows() == 1);
    }

    /**
     * Updates a given hashed password for a given user.
     * @param int $userId the user id
     * @param string $hash the hashed password
     * @return boolean TRUE on update, FALSE otherwise
     */
    public function changeUsername ($userId, $username)
    {
        $updateRow = array();
        $updateRow['username'] = $username;

        $this->db->where('person_id', $userId);
        $this->db->update($this->databaseTableName, $updateRow);

        return ($this->db->affected_rows() == 1);
    }

    /**
     * Adds a given login/password combination for a given user to the "authentication" table.
     * Transactions should be handled outside this method.
     * @param string $username the user login handle
     * @param string $hash the hashed password
     * @param int $userId the user id
     * @return boolean TRUE on insertion, FALSE otherwise
     */
    public function addNewAuthentication ($username, $hash, $userId)
    {
        $newRow = array();
        $newRow['username'] = $username;
        $newRow['password_sha256'] = $hash;
        $newRow['person_id'] = $userId;

        $this->db->insert($this->databaseTableName, $newRow);

        return ($this->db->affected_rows() == 1);
    }

    /**
     * Retrieves authentication details for a given user by user id.
     * @param int $userId the user id
     * @return Object | boolean returns the authentication record as object, of FALSE if not found
     */
    public function getByUserId ($userId)
    {
        $rhett = false;

        $this->db->where('person_id', $userId);
        $query = $this->db->get($this->databaseTableName);

        if (0 < $query->num_rows()) {
            $rhett = $query->first_row();
        }
        return $rhett;
    }

    /**
     * Retrieves authentication details for a given user by login name.
     * @param int $userId the user id
     * @return Object | boolean returns the authentication record as object, of FALSE if not found
     */
    public function getByUsername ($username)
    {
        $rhett = false;

        $this->db->where('username', $username);
        $query = $this->db->get($this->databaseTableName);

        if (0 < $query->num_rows()) {
            $rhett = $query->first_row();
        }
        return $rhett;
    }
}
