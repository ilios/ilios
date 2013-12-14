<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

include_once "ilios_base_model.php";

/**
 * Data Access Object (DAO) to the authentication table.
 */
class Authentication extends Ilios_Base_Model
{

    public function __construct ()
    {
        parent::__construct('authentication', array('person_id'));
    }

    /**
     * Updates a given hashed password for a given user.
     * @param int $userId the user id
     * @param string $hash the hashed password
     * @return boolean TRUE on update, FALSE otherwise.
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
     * updates the username of a given user.
     * @param int $userId The user id.
     * @param string $username The new username.
     * @return boolean TRUE on update, FALSE otherwise.
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
     * Updates a given API key for a given user.
     *
     * @param int $userId The user id.
     * @param string $key The api key.
     * @return boolean TRUE on update, FALSE otherwise.
     */
    public function changeApiKey ($userId, $key)
    {
        $updateRow = array();
        $updateRow['api_key'] = $key;

        $this->db->where('user_id', $userId);
        $this->db->update('api_key', $updateRow);

        return ($this->db->affected_rows() == 1);
    }

    /**
     * Adds a given API key for a given user.
     *
     * @param int $userId The user id.
     * @param string $key The api key.
     * @return boolean TRUE on update, FALSE otherwise.
     */
    public function addApiKey ($userId, $key)
    {
        $newRow = array();
        $newRow['user_id'] = $userId;
        $newRow['api_key'] = $key;

        $this->db->insert('api_key', $newRow);

        return ($this->db->affected_rows() == 1);
    }

    /**
     * Retrieves the API key for a given user.
     * @param int $userId The user id.
     * @return string|boolean The API key, or FALSE if none could be found.
     */
    public function getApiKey ($userId)
    {
        $rhett = false;

        $clean = array();
        $clean['user_id'] = (int) $userId;

        $sql = "SELECT * FROM api_key WHERE user_id = {$clean['user_id']}";

        $query = $this->db->query($sql);

        if (0 < $query->num_rows()) {
            $row = $query->first_row('array');
            $rhett = $row['api_key'];
        }

        $query->free_result();

        return $rhett;
    }

    /**
     * Adds a given login/password combination for a given user to the "authentication" table.
     * Transactions should be handled outside this method.
     * @param string $username the user login handle
     * @param string $hash the hashed password
     * @param int $userId the user id
     * @return boolean TRUE on insertion, FALSE otherwise.
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
     * @return stdClass|boolean returns the authentication record as object, of FALSE if not found.
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
     * Retrieves authentication details for a given user by a given username.
     * @param string $username The username.
     * @return stdClass|boolean Returns the authentication record as object, of FALSE if not found.
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
