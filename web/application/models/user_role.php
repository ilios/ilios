<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

include_once "ilios_base_model.php";

/**
 * Data Access Object to the user role table.
 */
class User_Role extends Ilios_Base_Model
{
    /**
     * "Course Director" role identifier.
     * @var int
     */
     const COURSE_DIRECTOR_ROLE_ID = 1;

     /**
     * "Developer" role identifier.
     * @var int
     */
     const DEVELOPER_ROLE_ID = 2;

     /**
    * "Faculty" (aka Instructor) role identifier.
    * @var int
    */
     const FACULTY_ROLE_ID = 3;

     /**
    * "Student" (aka Learner) role identifier.
    * @var int
    */
    const STUDENT_ROLE_ID = 4;

    /**
    * "Public" role identifier.
    * @var int
    */
    const PUBLIC_ROLE_ID = 5;

    /**
     * Constructor.
     */
    public function __construct ()
    {
        parent::__construct('user_role', array('user_role_id'));
    }

    /**
     * Retrieves an associative array of user roles, keyed off by role id.
     * The value of each entry is the role title.
     * @return array
     */
    public function getUserRolesMap ()
    {
        $rhett = array();

        $queryResults = $this->db->get('user_role');

        if (0 < $queryResults->num_rows()) {
            foreach ($queryResults->result_array() as $row) {
                $rhett[$row['user_role_id']] = $row['title'];
            }
        }

        return $rhett;
    }

    /**
     * Get the user_role_id by title
     * @param string roleName
     * @return int, a Role Id, or null if not found.
     */
    public function getRoleId ( $roleName )
    {
        $this->db->where('title', $roleName);
        $qResults = $this->db->get($this->databaseTableName);

        return ($qResults->num_rows() > 0) ? $qResults->first_row()->user_role_id : null;
    }
}
