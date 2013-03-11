<?php

include_once "abstract_ilios_model.php";

/**
 * Data Access Object for the "offering" table.
 */
class Offering extends Abstract_Ilios_Model
{

    /**
     * Constructor.
     */
    public function __construct ()
    {
        parent::__construct('offering', array('offering_id'));
        $this->load->model('Group', 'group', TRUE);
        $this->load->model('Instructor_Group', 'instructorGroup', TRUE);
        $this->load->model('Recurring_Event', 'recurringEvent', TRUE);
        $this->load->model('User', 'user', TRUE);
    }

    /**
     * @todo add code docs
     * @param int $offeringId
     * @param int $newSessionId
     * @param int $totalOffsetDays
     */
    public function rolloverOffering ($offeringId, $newSessionId, $totalOffsetDays)
    {
        $offeringRow = $this->getRowForPrimaryKeyId($offeringId);

        $dtStartPHPTime = new DateTime($offeringRow->start_date, new DateTimeZone('UTC'));
        $dtStartPHPTime->setTimeZone(new DateTimeZone(date_default_timezone_get()));
        $dtStartPHPTime->add(new DateInterval('P'.$totalOffsetDays.'D'));
        $dtStartPHPTime->setTimeZone(new DateTimeZone('UTC'));

        $dtEndPHPTime = new DateTime($offeringRow->end_date, new DateTimeZone('UTC'));
        $dtEndPHPTime->setTimeZone(new DateTimeZone(date_default_timezone_get()));
        $dtEndPHPTime->add(new DateInterval('P'.$totalOffsetDays.'D'));
        $dtEndPHPTime->setTimeZone(new DateTimeZone('UTC'));

        $newRow = array();
        $newRow['offering_id'] = null;

        $newRow['room'] = $offeringRow->room;
        $newRow['session_id'] = $newSessionId;
        $newRow['start_date'] = $dtStartPHPTime->format("Y-m-d H:i:s");
        $newRow['end_date'] = $dtEndPHPTime->format("Y-m-d H:i:s");
        $newRow['deleted'] = 0;

        $this->db->insert($this->databaseTableName, $newRow);
        $newOfferingId = $this->db->insert_id();

        if ($newOfferingId > 0) {
            $recurringEventId = $this->getRecurringEventIdForOffering($offeringId);
            if ($recurringEventId != -1) {
                $newRecurringEventId = $this->cloneRecurringEventChain($recurringEventId,
                                                                       $totalOffsetDays);

                $newRow = array();
                $newRow['offering_id'] = $newOfferingId;
                $newRow['recurring_event_id'] = $newRecurringEventId;
                $this->db->insert('offering_x_recurring_event', $newRow);
            }


            $queryString = 'SELECT copy_offering_attributes_to_offering(' . $offeringId . ', '
                                . $newOfferingId . ')';
            $this->db->query($queryString);
        }
    }

    /**
     * @todo add code docs
     * @param int $rootRecurringEventId
     * @param int $totalOffsetDays
     */
    protected function cloneRecurringEventChain ($rootRecurringEventId, $totalOffsetDays)
    {
        $reRow = $this->recurringEvent->getRowForPrimaryKeyId($rootRecurringEventId);

        $nextRecurringEventId = null;
        if (! is_null($reRow->next_recurring_event_id)) {
            $nextRecurringEventId = $this->cloneRecurringEventChain($reRow->next_recurring_event_id);
        }

        $newRow = $this->convertStdObjToArray($reRow);
        $newRow['recurring_event_id'] = null;
        $newRow['next_recurring_event_id'] = $nextRecurringEventId;

        $dtEndPHPTime = new DateTime($reRow->end_date, new DateTimeZone('UTC'));
        $dtEndPHPTime->setTimeZone(new DateTimeZone(date_default_timezone_get()));
        $dtEndPHPTime->add(new DateInterval('P'.$totalOffsetDays.'D'));
        $dtEndPHPTime->setTimeZone(new DateTimeZone('UTC'));
        $newRow['end_date'] = $dtEndPHPTime->format("Y-m-d H:i:s");

        $this->db->insert('recurring_event', $newRow);

        $newREId = $this->db->insert_id();


        $updateRow = array();
        $updateRow['previous_recurring_event_id'] = $newREId;

        $this->db->where('recurring_event_id', $nextRecurringEventId);
        $this->db->update('recurring_event', $updateRow);


        return $newREId;
    }

    /**
     * Adds or updates a given offering.
     * @param string $location the offering location
     * @param string $startDate assumed to be in mySQL friendly format (Y-m-d H:i:s)
     * @param string $endDate assumed to be in mySQL friendly format (Y-m-d H:i:s)
     * @param array $instructors array of assoc. arrays,
     *     each sub-array representing either an individual instructor or an instructor group
     * @param array $learnerGroupIds an array of learner group ids
     * @param int $sessionId the session id
     * @param array|NULL $recurringEvent a assoc. array holding recurrency info for the given offering
     * @param int $publishEventId an integer indicating whether an even should be published or not
     * @param array $auditAtoms the audit trail
     * @param int|NULL $offeringId the offering identifier. Is NULL for a new offering.
     * @return array an associative array of at least one key - 'offering_id' (new one due to insert if
     *              $offeringId is null) and perhaps also 'recurring_event_id' if $recurringEvent
     *              is not null (new one if the recurring event is new (including first insert of
     *              offering)
     *
     * @todo return results to denote a successful offering insert but a failed cross table or
     *              recurring event insert
     * @todo improve code docs
     */
    public function saveOffering ($location, $startDate, $endDate, $instructors, $learnerGroupIds,
                           $sessionId, $recurringEvent, $publishEventId, &$auditAtoms,
                           $offeringId = null)
    {
        $rhett = array();

        $recurringEventId = -1;

        $locationToUse = $location;

        $existingInstructorIds = array();
        $existingInstructorGroupIds = array();
        $existingLearnerGroupIds = array();

        /*
         * ACHTUNG - BUSINESS LOGIC AHEAD!
         *
         * How to deal with empty location value:
         *
         * If the location value for an offering is null, check to see if there are any associated learner groups.
         *   If no, insert default "TBD" text.
         *   If yes, check to see if the first group (first in list, highest level) in the list of associated groups
         *   has a default location;
         *     If so, insert that as the location value.
         *     If not, insert default "TBD" value.
         */
        if ('' === trim($locationToUse)) {
            if (count($learnerGroupIds)) {
                $groupRow = $this->group->getRowForPrimaryKeyId($learnerGroupIds[0]);
                if ($groupRow) {
                	$locationToUse = $groupRow->location;
                }
            }
        }

        if ('' === trim($locationToUse)) {
            $lang =  $this->getLangToUse();
            $locationToUse = $this->i18nVendor->getI18NString('general.acronyms.to_be_decided', $lang);
        }

        if ($offeringId == null) { // add new offering
            if ($recurringEvent != null) {
                $recurringEventId = $this->recurringEvent->saveRecurringEvent($recurringEvent,
                                                                              $auditAtoms);
            }

            $newRow = array();
            $newRow['offering_id'] = null;

            $newRow['room'] = $locationToUse;
            $newRow['session_id'] = $sessionId;
            $newRow['start_date'] = $startDate;
            $newRow['end_date'] = $endDate;
            $newRow['publish_event_id'] = ($publishEventId == -1) ? null : $publishEventId;

            $this->db->insert($this->databaseTableName, $newRow);

            $newOfferingId = $this->db->insert_id();

            if (! $newOfferingId) {
                return -1;
            }

            array_push($auditAtoms, $this->auditEvent->wrapAtom($newOfferingId, 'offering_id',
                                                                $this->databaseTableName,
                                                                Ilios_Model_AuditUtils::CREATE_EVENT_TYPE, 1));

            if ($recurringEventId != -1) {
                $newRow = array();
                $newRow['offering_id'] = $newOfferingId;
                $newRow['recurring_event_id'] = $recurringEventId;
                $this->db->insert('offering_x_recurring_event', $newRow);

                array_push($auditAtoms,
                           $this->auditEvent->wrapAtom($newOfferingId, 'offering_id',
                                                       'offering_x_recurring_event',
                                                       Ilios_Model_AuditUtils::CREATE_EVENT_TYPE));
            }

            if (is_null($instructors)) {
                $instructors = array();

                foreach ($learnerGroupIds as $groupId) {
                    $queryResults = $this->group->getQueryResultsForInstructorsForGroup($groupId);

                    foreach ($queryResults->result_array() as $row) {
                        if (($row['user_id'] == null) || ($row['user_id'] == '')) {
                            $igRow = array();
                            $igRow['isGroup'] = 1;
                            $igRow['dbId'] = $row['instructor_group_id'];

                            array_push($instructors, $igRow);
                        }
                        else {
                            $userRow = array();
                            $userRow['isGroup'] = 0;
                            $userRow['dbId'] = $row['user_id'];

                            array_push($instructors, $userRow);
                        }
                    }
                }
            }
            $rhett['offering_id'] = $newOfferingId;
        } else { // update offering
            $updateRow = array();

            if ($recurringEvent != null) {
                $recurringEventId = $this->recurringEvent->saveRecurringEvent($recurringEvent,
                                                                              $auditAtoms);
            }
            else {
                $this->deleteAssociatedRecurringEvent($offeringId, $auditAtoms);
            }

            $updateRow['room'] = $locationToUse;
            $updateRow['session_id'] = $sessionId;
            $updateRow['start_date'] = $startDate;
            $updateRow['end_date'] = $endDate;
            $updateRow['publish_event_id'] = ($publishEventId == -1) ? null : $publishEventId;

            $this->db->where('offering_id', $offeringId);
            $this->db->update($this->databaseTableName, $updateRow);

            array_push($auditAtoms, $this->auditEvent->wrapAtom($offeringId, 'offering_id',
                                                                $this->databaseTableName,
                                                                Ilios_Model_AuditUtils::UPDATE_EVENT_TYPE, 1));

            $previousRecurringEventId = $this->getRecurringEventIdForOffering($offeringId);
            if ($previousRecurringEventId != -1) {
                if ($previousRecurringEventId != $recurringEventId) {
                    $this->deleteRecurringEventForOffering($offeringId, $previousRecurringEventId,
                                                           $auditAtoms);

                    $previousRecurringEventId = -1;
                }
            }

            if (($recurringEventId != -1) && ($previousRecurringEventId == -1)) {
                $newRow = array();
                $newRow['offering_id'] = $offeringId;
                $newRow['recurring_event_id'] = $recurringEventId;
                $this->db->insert('offering_x_recurring_event', $newRow);

                array_push($auditAtoms,
                           $this->auditEvent->wrapAtom($offeringId, 'offering_id',
                                                       'offering_x_recurring_event',
                                                       Ilios_Model_AuditUtils::CREATE_EVENT_TYPE));
            }

            $existingInstructorIds = $this->_getInstructorIds($offeringId);
            $existingLearnerGroupIds = $this->_getLearnerGroupIds($offeringId);
            $existingInstructorGroupIds = $this->_getInstructorGroupIds($offeringId);

            $rhett['offering_id'] = $offeringId;
        }

        //
        // deal with offering/learner-group and offering/instructor(-groups) associations.
        //

        // separate instructor-groups from individual instructors
        $instructorGroups = array();
        $individualInstructors = array();
        if (! empty($instructors)) {
        	foreach ($instructors as $instructor) {
        		if (1 == $instructor['isGroup']) { // is individual or group?
        			$instructorGroups[] = $instructor;
        		} else {
        			$individualInstructors[] = $instructor;
        		}
        	}
        }
        $learnerGroups = array();
        if (! empty($learnerGroupIds)) {
        	//
        	// KLUDGE:
        	// see lengthly code comment in <code<Session::saveIndependentLearningFacet()</code>
        	foreach ($learnerGroupIds as $groupId) {
        	    $learnerGroups[] = array('dbId' => $groupId);
            }
        }
        // save group- and user-associations.

        // TODO cross table audit events?
        $this->_saveInstructorAssociations($rhett['offering_id'], $individualInstructors, $existingInstructorIds);
        $this->_saveInstructorGroupAssociations($rhett['offering_id'], $instructorGroups, $existingInstructorGroupIds);
        $this->_saveLearnerGroupAssociations($rhett['offering_id'], $learnerGroups, $existingLearnerGroupIds);


        $rhett['recurring_event_id'] = $recurringEventId;
        $rhett['location'] = $locationToUse;
        return $rhett;
    }

    /**
     * Transactions are assumed to be handled outside this block
     *
     * @todo improve code docs
     * @param int $offeringId
     * @param array $auditAtoms
     * @param boolean $deleteIsRootEvent
     * @return boolean TRUE if the delete removed a row in this class' main db table
     *
     */
    public function deleteOffering ($offeringId, &$auditAtoms, $deleteIsRootEvent)
    {
        $this->deleteAssociatedRecurringEvent($offeringId, $auditAtoms);

        $tables = array('offering_instructor', 'offering_learner');

        $this->db->where('offering_id', $offeringId);
        $this->db->delete($tables);

        $updateRow = array();
        $updateRow['deleted'] = 1;

        $this->db->where('offering_id', $offeringId);
        $this->db->update($this->databaseTableName, $updateRow);

        array_push($auditAtoms, $this->auditEvent->wrapAtom($offeringId, 'offering_id',
                                                            'offering_instructor',
                                                            Audit_Event::$DELETE_EVENT_TYPE));
        array_push($auditAtoms, $this->auditEvent->wrapAtom($offeringId, 'offering_id',
                                                            'offering_learner',
                                                            Audit_Event::$DELETE_EVENT_TYPE));
        array_push($auditAtoms, $this->auditEvent->wrapAtom($offeringId, 'offering_id',
                                                            $this->databaseTableName,
                                                            Audit_Event::$DELETE_EVENT_TYPE,
                                                            ($deleteIsRootEvent ? 1 : 0)));

        return (! $this->transactionAtomFailed());
    }

    /**
     * Delete from the cross table; if the cross table has no more refrences to the recurring event
     * then delete the recurring event.
     * @param int $offeringId
     * @param array $auditAtoms
     */
    protected function deleteAssociatedRecurringEvent ($offeringId, &$auditAtoms)
    {
        $recurringEventId = $this->getRecurringEventIdForOffering($offeringId);
        $this->deleteRecurringEventForOffering($offeringId, $recurringEventId, $auditAtoms);
    }

    /**
     * @todo add code docs
     * @param int $offeringId
     * @param int $recurringEventId
     * @param array $auditAtoms
     */
    protected function deleteRecurringEventForOffering ($offeringId, $recurringEventId,
                                                        &$auditAtoms)
    {
        if ($recurringEventId != -1) {
            $this->db->where('offering_id', $offeringId);
            $this->db->delete('offering_x_recurring_event');
            array_push($auditAtoms, $this->auditEvent->wrapAtom($offeringId, 'offering_id',
                                                                'offering_x_recurring_event',
                                                                Audit_Event::$DELETE_EVENT_TYPE));

            $this->db->where('recurring_event_id', $recurringEventId);
            $queryResults = $this->db->get('offering_x_recurring_event');
            if ($queryResults->num_rows() == 0) {
                $this->db->where('recurring_event_id', $recurringEventId);
                $this->db->delete('recurring_event');

                array_push($auditAtoms,
                           $this->auditEvent->wrapAtom($recurringEventId, 'recurring_event_id',
                                                       'recurring_event',
                                                       Audit_Event::$DELETE_EVENT_TYPE));
            }
        }
    }

    /**
     * @todo add code docs
     * @param int $offeringId
     * @return int
     */
    protected function getRecurringEventIdForOffering ($offeringId)
    {
        $this->db->where('offering_id', $offeringId);
        $queryResults = $this->db->get('offering_x_recurring_event');
        if ($queryResults->num_rows() > 0) {
            $row = $queryResults->first_row();

            return $row->recurring_event_id;
        }

        return -1;
    }

    /**
     * Transactions are assumed to be handled outside this block
     * @todo improve code docs
     * @param int $sessionId
     * @param array $auditAtoms
     * @return boolean
     */
    public function deleteOfferingsForSession ($sessionId, &$auditAtoms)
    {
        $offeringsToNuke = array();

        $this->db->where('session_id', $sessionId);
        $queryResults = $this->db->get($this->databaseTableName);

        foreach ($queryResults->result_array() as $row) {
            array_push($offeringsToNuke, $row['offering_id']);
        }

        foreach ($offeringsToNuke as $offeringId) {
            if (! $this->deleteOffering($offeringId, $auditAtoms, false)) {
                return false;
            }
        }

        return true;
    }

    /**
     * This includes session_type_id an session_title as part of each offering representation; this
     *  unfortunate bit of contamination is required information for offering renderings on the
     *  calendar when offerings not of the current session are requested to be shown..
     *  @todo improve code docs
     *  @param int $sessionId
     *  @param string $sessionTitle
     *  @param int $sessionTypeId
     *  @param boolean $sessionIsTBD
     *  @return array
     */
    public function getOfferingsForSession ($sessionId, $sessionTitle, $sessionTypeId, $sessionIsTBD)
    {
        $rhett = array();

        $this->db->where('session_id', $sessionId);
        $this->db->where('deleted', 0);
        $queryResults = $this->db->get($this->databaseTableName);

        foreach ($queryResults->result_array() as $row) {
            $offering = array();

            $offering['offering_id'] = $row['offering_id'];
            $offering['room'] = $row['room'];
            $offering['session_id'] = $sessionId;
            $offering['is_tbd'] = ($sessionIsTBD ? 'true' : 'false');
            $offering['start_date'] = $row['start_date'];
            $offering['end_date'] = $row['end_date'];
            $offering['publish_event_id'] = $row['publish_event_id'];
            $offering['instructors'] = $this->getInstructorsForOffering($row['offering_id']);
            $offering['learner_groups'] = $this->getLearnerGroupsForOffering($row['offering_id']);

            $recurringEventId = $this->getRecurringEventIdForOffering($row['offering_id']);
            if ($recurringEventId != -1) {
                $reRow = $this->recurringEvent->getRowForPrimaryKeyId($recurringEventId);
                $offering['recurring_event'] = $this->convertStdObjToArray($reRow);
            }

            $offering['session_type_id'] = $sessionTypeId;

            $offering['session_title'] = $sessionTitle;

            array_push($rhett, $offering);
        }

        return $rhett;
    }

    /**
     * @todo add code docs
     * @param int $offeringId
     * @return array
     */
    public function getAlertRecipientsForOffering ($offeringId)
    {
        $rhett = array();
        $rhett = $this->addUserIdsFromInstructors($this->getInstructorsForOffering($offeringId), $rhett);
        $rhett = $this->addUserIdsFromStudentGroups($this->getLearnerGroupsForOffering($offeringId), $rhett);
        return $rhett;
    }

    /**
     * Adds the user ids from a given list of instructors/instructor groups  to a given list of user ids.
     * @param array $instructors list of instructors and/or instructor-groups
     * @param array $destinationArray the array to add the user ids to
     * @return array the list of user ids
     */
    public function addUserIdsFromInstructors ($instructors, $destinationArray = array())
    {
        foreach ($instructors as $instructor) {
            // add user id
            if (isset($instructor['user_id'])) {
                $destinationArray[] = $instructor['user_id'];
            } else {
                // get users from instructor group
                // and add the id for each user in this group
                $groupMembers = $this->instructorGroup->getUsersForGroupWithId($instructor['instructor_group_id']);
                foreach ($groupMembers as $groupMember) {
                    $destinationArray[] = $groupMember->user_id;
                }
            }
        }
        return $destinationArray;
    }

    /**
     * Retrieves instructors and instructor-groups for a given offering.
     * @param int $offeringId
     * @return array
     */
    public function getInstructorsForOffering ($offeringId)
    {
        $rhett = array();

        $this->db->where('offering_id', $offeringId);
        $queryResults = $this->db->get('offering_instructor');

        foreach ($queryResults->result_array() as $row) {
            if (($row['user_id'] == null) || ($row['user_id'] == '')) {
                $igRow = $this->instructorGroup->getRowForPrimaryKeyId($row['instructor_group_id']);
                array_push($rhett, $this->convertStdObjToArray($igRow));
            }
            else {
                $userRow = $this->user->getRowForPrimaryKeyId($row['user_id']);
                array_push($rhett, $this->convertStdObjToArray($userRow));
            }
        }

        return $rhett;
    }

    /**
     * This method differs from <code>getInstructorsForOffering()</code>
     * in that all items returned are user
     * objects - so if there are instructor groups associated to an offering, the members of
     * that group are actually returned.
     * @param int $offeringId
     * @return array
     */
    public function getIndividualInstructorsForOffering ($offeringId)
    {
        $possiblyRedundantArray = array();

        $this->db->where('offering_id', $offeringId);
        $queryResults = $this->db->get('offering_instructor');

        foreach ($queryResults->result_array() as $row) {
            if (($row['user_id'] == null) || ($row['user_id'] == '')) {
                $userRows
                    = $this->instructorGroup->getUsersForGroupWithId($row['instructor_group_id']);

                foreach ($userRows as $userRow) {
                    array_push($possiblyRedundantArray, $this->convertStdObjToArray($userRow));
                }
            }
            else {
                $userRow = $this->user->getRowForPrimaryKeyId($row['user_id']);
                array_push($possiblyRedundantArray, $this->convertStdObjToArray($userRow));
            }
        }

        $rhett = array();
        foreach ($possiblyRedundantArray as $user) { // deduping
            $rhett[md5(serialize($user))] = $user;
        }

        return $rhett;
    }

    /**
     * Adds the user ids from a given list of learners/learner groups to a given list of user ids.
     * @param array $learners list of learners and/or learner-groups
     * @param array $destinationArray the array to add the user ids to
     * @return array the list of user ids
     */
    public function addUserIdsFromStudentGroups ($learners, $destinationArray =array())
    {
        foreach ($learners as $learner) {
            // add user id
            if (isset($learner['user_id'])) {
                $destinationArray[] = $learner['user_id'];
            } else {
                // get users from learner group
                // and add the id for each user in this group
                $groupMembers = $this->group->getUsersForGroupWithId($learner['group_id']);
                foreach ($groupMembers as $groupMember) {
                    $destinationArray[] = $groupMember->user_id;
                }
            }
        }
        return $destinationArray;
    }

    /**
     * @todo add code docs
     * @param int $offeringId
     * @return array
     */
    public function getLearnerGroupsForOffering ($offeringId)
    {
        $rhett = array();

        $this->db->where('offering_id', $offeringId);
        $queryResults = $this->db->get('offering_learner');

        foreach ($queryResults->result_array() as $row) {
            if (isset($row['group_id']) && (! is_null($row['group_id']))) {
                $groupRow = $this->group->getRowForPrimaryKeyId($row['group_id']);
                array_push($rhett, $this->convertStdObjToArray($groupRow));
            }
            else {
                $userRow = $this->user->getRowForPrimaryKeyId($row['user_id']);
                array_push($rhett, $this->convertStdObjToArray($userRow));
            }
        }

        return $rhett;
    }

    /**
     * @todo improve code docs
     * @param int $sessionId
     * @param string $columnName
     * @param string $columnValue
     * @return array an array of offering models which include session type id, but not the instructors
     *                  and student groups
     */
    public function getOtherOfferingsForInstructor ($sessionId, $columnName, $columnValue)
    {
        $rhett = array();

        $queryString
            = 'SELECT `offering`.`offering_id` AS offering_id, '
                            . '`offering`.`room` AS room, '
                            . '`offering`.`publish_event_id` AS publish_event_id, '
                            . '`offering`.`session_id` AS session_id, '
                            . '`offering`.`start_date` AS start_date, '
                            . '`offering`.`end_date` AS end_date '
                    . 'FROM `offering_instructor`, `offering` '
                    . 'WHERE `offering`.`session_id` != ' . $sessionId
                            . ' AND `offering`.`deleted` = 0 '
                            . ' AND `offering_instructor`.`' . $columnName . '` = ' . $columnValue
                            . ' AND `offering`.`offering_id` = `offering_instructor`.`offering_id`';

        $queryResults = $this->db->query($queryString);

        foreach ($queryResults->result_array() as $row) {
            $model = array();

            $model['offering_id'] = $row['offering_id'];
            $model['room'] = $row['room'];
            $model['publish_event_id'] = $row['publish_event_id'];
            $model['session_id'] = $row['session_id'];
            $model['start_date'] = $row['start_date'];
            $model['end_date'] = $row['end_date'];

            $recurringEventId = $this->getRecurringEventIdForOffering($row['offering_id']);
            if ($recurringEventId != -1) {
                $reRow = $this->recurringEvent->getRowForPrimaryKeyId($recurringEventId);
                $model['recurring_event'] = $this->convertStdObjToArray($reRow);
            }

            $sessionRow = $this->getRow('session', 'session_id', $row['session_id']);
            $model['session_type_id'] = $sessionRow->session_type_id;

            array_push($rhett, $model);
        }

        return $rhett;
    }

    /**
     * @todo improve code docs
     * @param int $sessionId
     * @param int $userId
     * @param int $groupId
     * @return array an array of offering models which include session type id, but not the instructors
     *                  and student groups
     */
    public function getOtherOfferingsForLearner ($sessionId, $userId, $groupId)
    {
        $rhett = array();

        $queryString
            = 'SELECT `offering`.`offering_id` AS offering_id, '
                            . '`offering`.`room` AS room, '
                            . '`offering`.`publish_event_id` AS publish_event_id, '
                            . '`offering`.`session_id` AS session_id, '
                            . '`offering`.`start_date` AS start_date, '
                            . '`offering`.`end_date` AS end_date '
                    . 'FROM `offering_learner`, `offering` '
                    . 'WHERE `offering`.`session_id` != ' . $sessionId
                            . ' AND `offering`.`deleted` = 0 '
                            . ' AND (`offering_learner`.`user_id` = ' . $userId
                            . ' OR `offering_learner`.`group_id` = ' . $groupId . ') '
                            . ' AND `offering`.`offering_id` = `offering_learner`.`offering_id`';

        $queryResults = $this->db->query($queryString);

        foreach ($queryResults->result_array() as $row) {
            $model = array();

            $model['offering_id'] = $row['offering_id'];
            $model['room'] = $row['room'];
            $model['publish_event_id'] = $row['publish_event_id'];
            $model['session_id'] = $row['session_id'];
            $model['start_date'] = $row['start_date'];
            $model['end_date'] = $row['end_date'];

            $recurringEventId = $this->getRecurringEventIdForOffering($row['offering_id']);
            if ($recurringEventId != -1) {
                $reRow = $this->recurringEvent->getRowForPrimaryKeyId($recurringEventId);
                $model['recurring_event'] = $this->convertStdObjToArray($reRow);
            }

            $sessionRow = $this->getRow('session', 'session_id', $row['session_id']);
            $model['session_type_id'] = $sessionRow->session_type_id;

            array_push($rhett, $model);
        }

        return $rhett;
    }

    /**
     * Retrieves a list of published session-offerings starting on a given day.
     * @param string $start_date the offering start date
     * @return array an list of qualifying offerings
     */
    public function getOfferingsWithStartDate($start_date)
    {
        $rhett = array();

        $this->db->where('deleted', 0);

        $queryResults = $this->db->get($this->databaseTableName);

        $sessionPublishIds = array(); // session-id/publish-id lookup map

        foreach ($queryResults->result_array() as $row) {
            $sessionId = $row['session_id'];
            if (! array_key_exists($sessionId, $sessionPublishIds)) { // check cache
                // check database
                $publishIds = $this->getIdArrayFromCrossTable('session', 'publish_event_id', 'session_id', $sessionId);
                $publishId  = is_null($publishIds) ? null : $publishIds[0];
                $sessionPublishIds[$sessionId] = $publishId; // cache it!
            }

            $publishId = $sessionPublishIds[$sessionId]; // get publish-id from cache

            if (! empty($publishId)) { // only proceed if the parent session has been published
                $dtStartPHPTime = new DateTime($row['start_date'], new DateTimeZone('UTC'));
                $dtStartPHPTime->setTimezone(new DateTimeZone(date_default_timezone_get()));

                $offeringStartDate = $dtStartPHPTime->format('Y-m-d');

                if ($start_date == $offeringStartDate) { // start-date comparison
                    $rhett[] = (object) $row;
                }
            }
        }

        return $rhett;
    }

    /**
     * Retrieves ids of users that are associated as instructors
     * with a given offering.
     * @param int $offeringId
     * @return array list of user ids
     */
    protected function _getInstructorIds ($offeringId)
    {
    	$ids = $this->getIdArrayFromCrossTable('offering_instructor',
    			'user_id', 'offering_id', $offeringId);
    	return is_null($ids) ? array() : array_filter($ids);
    }

    /**
     * Retrieves ids of instructor-groups that are associated with a given offering.
     * @param int $offeringId
     * @return array list of instructor group ids
     */
    protected function _getInstructorGroupIds ($offeringId)
    {
    	$ids = $this->getIdArrayFromCrossTable('offering_instructor',
    			'instructor_group_id', 'offering_id', $offeringId);
    	return is_null($ids) ?  array() : array_filter($ids);
    }

    /**
     * Retrieves ids of learner groups that are associated with a given offering.
     * @param int $offeringId
     * @return array list of learner group ids
     */
    protected function _getLearnerGroupIds ($offeringId)
    {
    	$ids = $this->getIdArrayFromCrossTable('offering_learner',
    			'group_id', 'offering_id', $offeringId);
    	return is_null($ids) ? array() : array_filter($ids);
    }


    /**
     * Saves the offering/instructor associations for a given offering
     * and given instructors, taken given pre-existings associations into account.
     * @param int $offeringId
     * @param array $instructors
     * @param array $associatedInstructorIds
     */
    protected function _saveInstructorAssociations ($offeringId, $instructors = array(),
    		$associatedInstructorIds = array())
    {
    	$this->_saveJoinTableAssociations('offering_instructor',
    			'offering_id', $offeringId, 'user_id',
    			$instructors, $associatedInstructorIds);
    }

    /**
     * Saves the offering/instructor-group associations for a given offering
     * and given instructors-groups, taken given pre-existings associations into account.
     * @param int $offeringId
     * @param array $instructorGroups
     * @param array $associatedInstructorGroupsIds
     */
    protected function _saveInstructorGroupAssociations ($offeringId, $instructorGroups = array(),
    		$associatedInstructorGroupsIds = array())
    {
    	$this->_saveJoinTableAssociations('offering_instructor',
    			'offering_id', $offeringId, 'instructor_group_id',
    			$instructorGroups, $associatedInstructorGroupsIds);
    }


    /**
     * Saves the offering/learner-group associations for a given offering
     * and given learner-groups, taken given pre-existings associations into account.
     * @param int $offeringId
     * @param array $learnerGroups
     * @param array $associatedLearnerGroupsIds
     */
    protected function _saveLearnerGroupAssociations ($offeringId, $learnerGroups = array(),
    		$associatedLearnerGroupsIds = array())
    {
    	$this->_saveJoinTableAssociations('offering_learner',
    			'offering_id', $offeringId, 'group_id',
    			$learnerGroups, $associatedLearnerGroupsIds);
    }

}
