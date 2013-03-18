<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

include_once "abstract_ilios_model.php";

/**
 * Data Access Object (DAO) to the "user_made_reminder" table.
 */
class User_Made_Reminder extends Abstract_Ilios_Model
{
    /**
     * Constructor
     */
    public function __construct ()
    {
        parent::__construct('user_made_reminder', array('user_made_reminder_id'));
    }

    /**
     * Adds or updates a given user reminder.
     * @param int $reminderId the reminder id, -1 for new reminders
     * @param string $noteText the reminder text
     * @param string $dueDate the due date, formatted as SQL datetime string
     * @param boolean $closed reminder status flag, TRUE for 'closed', FALSE for 'open'
     * @param int $userId the user id
     * @return int the reminder id, -1 on failed update
     */
    public function saveReminder ($reminderId, $noteText, $dueDate, $closed, $userId)
    {
        if ($reminderId == -1) {
            $newRow = array();
            $newRow['user_made_reminder_id'] = null;

            $newRow['note'] = $noteText;

            $dtCreationDate = new DateTime('now', new DateTimeZone('UTC'));
            $newRow['creation_date'] = $dtCreationDate->format('Y-m-d H:i:s');

            $newRow['due_date'] = $dueDate;
            $newRow['closed'] = 0;
            $newRow['user_id'] = $userId;

            $this->db->insert($this->databaseTableName, $newRow);
            return $this->db->insert_id();

        } else {
            $updateRow = array();
            $updateRow['note'] = $noteText;
            $updateRow['due_date'] = $dueDate;
            $updateRow['closed'] = $closed ? 1 : 0;

            $this->db->where('user_made_reminder_id', $reminderId);
            $success = $this->db->update($this->databaseTableName, $updateRow);

            if (!$success) {
                return -1;
            }
            return $reminderId;
        }
    }

    /**
     * Retrieves all user reminders for the given user that are due to in a given number of days from now.
     * @param int $dayCount days from now
     * @param int $userId the user id
     * @return array
     */
    public function loadAllRemindersForCurrentUserForFollowingDays ($dayCount, $userId)
    {
        $rhett = array();

        $cutoffTime = time() + ($dayCount * 24 * 60 * 60);
        $dateCutoff = date('Y-m-d H:i:s', $cutoffTime);
        $qualifications = array('user_id = ' => $userId,
                                'due_date <= ' => $dateCutoff);
        $this->db->where($qualifications);
        $this->db->order_by('due_date', 'asc');
        $queryResults = $this->db->get($this->databaseTableName);
        foreach ($queryResults->result_array() as $row) {
            array_push($rhett, $row);
        }
        return $rhett;
    }
}
