<?php defined('BASEPATH') OR exit('No direct script access allowed');

include_once "ilios_cli_controller.php";

/**
 * @package Ilios
 *
 * Scheduled Tasks controller.
 * Provides functionality to run recurring tasks within the application,
 * such as synchronizing the Ilios user store against an external user store.
 */
class Cron_Tasks extends Ilios_Cli_Controller
{
    /**
     * Tasks configuration container.
     * @var array
     */
    protected $_tasksConfig = array();

    /**
     * Constructor
     */
    public function __construct ()
    {
        parent::__construct();

        // up the memory limit
        ini_set('memory_limit','256M');

        // turn off script execution timeout
        set_time_limit(0);


        $this->load->model('Alert', 'alert', true);
        $this->load->model('School', 'school', true);
        $this->load->model('User', 'user', true);
        $this->load->model('User_Sync_Exception', 'userSyncException', true);

        // load tasks configuration
        $tasksConfig = $this->config->item('tasks');
        if (is_array($tasksConfig)) {
            $this->_tasksConfig = $tasksConfig;
        }
    }

    /**
     * Default method.
     * This triggers all enabled scheduled task to run.
     */
    public function index ()
    {
        // user sync processing
        try {
            $userSyncConfig = array();
            if (array_key_exists('user_sync', $this->_tasksConfig)
                && is_array($this->_tasksConfig['user_sync'])) {
                    $userSyncConfig = $this->_tasksConfig['user_sync'];
            }
            $userSyncEnabled = false;
            if (array_key_exists('enabled', $userSyncConfig) && $userSyncConfig['enabled']) {
                $userSyncEnabled = true;
            }
            if ($userSyncEnabled) {
                $this->_triggerUserSyncProcesses($userSyncConfig);
            }
        } catch (Exception $e){
            // no exception should bubble up to this point.
            // however, if it does then catch it here and print it out.
            echo $e;
        }

        // change alerts processing
        try {
            $changeAlertsConfig = array();
            if (array_key_exists('change_alerts', $this->_tasksConfig)
                && is_array($this->_tasksConfig['change_alerts'])) {
                    $changeAlertsConfig = $this->_tasksConfig['change_alerts'];
            }
            $changeAlertsProcessingEnabled = false;
            if (array_key_exists('enabled', $changeAlertsConfig) && $changeAlertsConfig['enabled']) {
                $changeAlertsProcessingEnabled = true;
            }
            if ($changeAlertsProcessingEnabled) {
                $this->_triggerChangeAlertsProcess($changeAlertsConfig);
            }
        } catch (Exception $e) {
            // same here, last ditch effort to prevent script execution from failing.
            echo $e;
        }
    }

    //
    // Tasks in this function are expected to run only once a day.
    //
    public function daily_tasks()
    {
        // export enrollment data
        try {
            $exportConfig = array ();
            if (array_key_exists('enrollment_export', $this->_tasksConfig)
                && is_array($this->_tasksConfig['enrollment_export'])) {
                $exportConfig = $this->_tasksConfig['enrollment_export'];
            }
            $enrollExportEnabled = false;
            if (array_key_exists('enabled', $exportConfig) && $exportConfig['enabled']) {
                $enrollExportEnabled = true;
            }
            if ($enrollExportEnabled) {
                $this->_exportEnrollmentData($exportConfig);
            }
        } catch (Exception $e) {
            echo $e;
        }

        // send Teaching Reminder alerts
        try {
            $remindersConfig = array();
            if (array_key_exists('teaching_reminders', $this->_tasksConfig)
                && is_array($this->_tasksConfig['teaching_reminders'])) {
                $remindersConfig = $this->_tasksConfig['teaching_reminders'];
            }
            $teachingRemindersEnabled = false;
            if (array_key_exists('enabled', $remindersConfig) && $remindersConfig['enabled']) {
                $teachingRemindersEnabled = true;
            }
            if ($teachingRemindersEnabled) {
                $this->processOfferingReminders();
            }
        } catch (Exception $e) {
            echo $e;
        }
    }

    /**
     * Kicks off the change alert notification process.
     * @param array $config process configuration
     * @return boolean TRUE if the process ran, FALSE if not
     * @throws Ilios_ChangeAlert_Exception
     */
    protected function _triggerChangeAlertsProcess (array $config)
    {
        $debug = false;
        if (array_key_exists('debug', $config)) {
        	$debug = $config['debug'];
        }

        // instantiate logger
        $logger = null;
        $logFilePath = $this->config->item('cron_log_file');
        try {
            $logger = Ilios_Logger::getInstance($logFilePath);
        } catch (Ilios_Log_Exception $e) {
            // fail! make a note about this in the error log
            error_log($e->getMessage());
            return false;
        }

        // (somewhat) HACKETY HACK!
        // in order to keep the coupling between CI components and Ilios Code Library
        // components to the absolut minimum, we must pass the path to the alerts templates
        // to the notification component via the configuration array (rather than invoking
        // our custom CI helper functions from within the process object).
        $conf = array_merge($config, array('templates_dir_path' => getServerFilePath('alert_templates')));

        // instantiate and invoke notification process
        $process = new Ilios_ChangeAlert_NotificationProcess($conf, $this->alert,
            $this->school, $this->offering, $this->iliosSession, $this->sessionType,
            $this->course);
        $process->run($logger, $debug);
        unset($logger); // cleanup
        return true;
    }

    /**
     * Kicks off the user synchronization processes.
     * @param array $config process configuration
     * @return boolean TRUE if the processes ran, FALSE if not
     */
    protected function _triggerUserSyncProcesses (array $config)
    {
        // get the log file path
        $logFilePath = array_key_exists('log_file_path', $config) ? $config['log_file_path'] : false;

        // instantiate the user source
        $userSourceClassName = array_key_exists('user_source_class', $config) ? $config['user_source_class'] : false;
        $userSource = null;

        if ($userSourceClassName
            && class_exists($userSourceClassName, true)) {
            $userSource = new $userSourceClassName($config);
        }

        // input validation
        // if we don't have a valid user source object
        // or no log file path, then we abort mission here.
        if (! $userSource  || ! $logFilePath) {
            return false;
        }

        // instantiate logger
        $logger = null;
        try {
            $logger = Ilios_Logger::getInstance($logFilePath);
        } catch (Ilios_Log_Exception $e) {
            // fail! make a note about this in the error log
            error_log($e->getMessage());
            return false;
        }

        // instantiate and run the student sync
        $studentSyncProcess = new Ilios_UserSync_Process_StudentProcess($userSource,
            $this->user, $this->school, $this->userSyncException);
        $processId = time();
        $studentSyncProcess->run($processId, $logger);
        unset($studentSyncProcess);

        // instantiate and run the non-student sync
        $processId = time();
        $nonStudentSyncProcess = new Ilios_UserSync_Process_NonStudentProcess($userSource,
            $this->user, $this->userSyncException);
        $nonStudentSyncProcess->run($processId, $logger);
        unset($studentSyncProcess);

        // cleanup
        unset($logger);
        return true;
    }

    /**
     * Exports enrollment information to file for ingestion into CLEAE through
     * an external process.
     * @param array $exportConfig export process configuration
     * @return int|boolean returns the # of bytes that were written to the file, or FALSE on failure.
     */
    protected function _exportEnrollmentData (array $exportConfig) {
        $this->load->model('canned_queries', '', true);
        $export_list = array();

        // Get the file path where the output would go
        $outputFilePath = array_key_exists('output_file_path', $exportConfig) ?
            $exportConfig['output_file_path'] : false;

        if (empty($outputFilePath)) {
            return false;
        }

        // Export instructor list
        $instructorSchools = array_key_exists('instructor_schools', $exportConfig) ? $exportConfig['instructor_schools'] : false;
        $instructorRole = array_key_exists('instructor_role', $exportConfig) ? $exportConfig['instructor_role'] : false;

        if (!empty($instructorSchools) && !empty($instructorRole)) {
            $instructorSchools = is_array($instructorSchools) ? $instructorSchools : array($instructorSchools);
            $list = array_merge($this->canned_queries->getCoursesAndDirectors(),$this->canned_queries->getCoursesAndInstructors());

            foreach ($list as $row) {
                $ucsfid = trim($row['uc_uid']);
                $courseid = $this->course->getUniqueId($row['course_id']);
                $sid = $row['owning_school_id'];

                if (!empty($ucsfid) && in_array($sid, $instructorSchools)) {
                    $export_list["$ucsfid-$courseid-$instructorRole"] = implode(',', array($ucsfid,$courseid,$instructorRole));
                }
            }
        }

        // Export learner list
        $learnerSchools = array_key_exists('learner_schools', $exportConfig) ? $exportConfig['learner_schools'] : false;
        $learnerRole = array_key_exists('learner_role', $exportConfig) ? $exportConfig['learner_role'] : false;

        if (!empty($learnerRole) && !empty($learnerSchools)) {
            $learnerSchools = is_array($learnerSchools) ? $learnerSchools : array($learnerSchools);
            $list = $this->canned_queries->getCoursesAndLearners();

            foreach ($list as $row) {
                $ucsfid = trim($row['uc_uid']);
                $courseid = $this->course->getUniqueId($row['course_id']);
                $sid = $row['owning_school_id'];

                if (!empty($ucsfid) && in_array($sid, $learnerSchools)) {
                    $export_list["$ucsfid-$courseid-$learnerRole"] = implode(',', array($ucsfid,$courseid,$learnerRole));
                }
            }
        }

        // Participant enrollment
        $participantSchools = array_key_exists('participant_schools', $exportConfig) ? $exportConfig['participant_schools'] : false;
        $participantRole = array_key_exists('participant_role', $exportConfig) ? $exportConfig['participant_role'] : false;

        if (!empty($participantSchools) && !empty($participantRole)) {
            $participantSchools = is_array($participantSchools) ? $participantSchools : array($participantSchools);

            foreach ($participantSchools as $schoolId) {
                // build participant list
                $participants = array();

                $list = array_merge($this->canned_queries->getCoursesAndDirectors(),$this->canned_queries->getCoursesAndInstructors());

                foreach ($list as $row) {
                    $ucsfid = trim($row['uc_uid']);
                    $sid = $row['owning_school_id'];

                    if (!empty($ucsfid) && !empty($schoolId) && ($sid == $schoolId)) {
                        $participants[$ucsfid] = $ucsfid;
                    }
                }

                $rows = $this->user->getUsersWithPrimarySchoolId( $schoolId );

                foreach ($rows as $row) {
                    $ucsfid = trim($row['uc_uid']);
                    $userid = $row['user_id'];

                    if (!empty($ucsfid) && !$this->user->userIsStudent($userid)) {
                        $participants[$ucsfid] = $ucsfid;
                    }
                }

                // Find all SOM courses and add the entire participants to $export_list
                $rows = $this->course->getCoursesWithPrimarySchoolId( $schoolId );

                foreach ($rows as $row) {
                    $courseid = $this->course->getUniqueId($row['course_id']);

                    foreach ($participants as $ucsfid) {
                        $export_list["$ucsfid-$courseid-$participantRole"] = implode(',', array($ucsfid,$courseid,$participantRole));
                    }
                }
            }
        }

        return file_put_contents($outputFilePath, implode( "\n", $export_list ));
    }



    /**
     * @deprecated
     * @todo reimplement according to new rules, see ticket #1009
     */
    protected function processOfferingReminders ()
    {
    	$days_in_advance = $this->config->item('event_reminder_alert_in_days');
    	if (!$days_in_advance) {
    		$days_in_advance = 7;
    	}

    	$secs_in_advance = $days_in_advance * 24 * 3600;
    	$todays_date = date('Y-m-d');
    	$remind_date = date('Y-m-d', strtotime($todays_date) + $secs_in_advance);

    	$offerings = $this->offering->getOfferingsWithStartDate($remind_date);

    	foreach ($offerings as $offering) {
    		$this->handleOfferingReminder($offering);
    	}
    }


    /**
     * @deprecated
     * @todo reimplement according to new rules, see ticket #1009
     */
    protected function handleOfferingReminder($offering)
    {
		// get instructors as recipients
    	$recipients = $this->offering->getIndividualInstructorsForOffering( $offering->offering_id );

    	$offeringLocation = $offering->room;
    	$dtStartPHPTime = new DateTime($offering->start_date, new DateTimeZone('UTC'));
    	$dtEndPHPTime   = new DateTime($offering->end_date, new DateTimeZone('UTC'));
    	$dtStartPHPTime->setTimezone(new DateTimeZone(date_default_timezone_get()));
    	$dtEndPHPTime->setTimezone(new DateTimeZone(date_default_timezone_get()));

    	$offeringStartDate = $dtStartPHPTime->format('D M d, Y');
    	$offeringStartTime = $dtStartPHPTime->format('h:i a');
    	$offeringEndTime = $dtEndPHPTime->format('h:i a');

    	$studentList = '';
    	$students = $this->offering->getLearnerGroupsForOffering($offering->offering_id);
    	foreach ($students as $student) {
    		if (isset($student['user_id'])) {
    			$learnerName = $student['first_name'] . ' ' . $student['last_name'];
    		}
    		else {
    			$learnerName = $student['title'];
    		}

    		if ($studentList != '') {
    			$studentList .= '; ';
    		}

    		$studentList .= $learnerName;
    	}

    	$session = $this->iliosSession->getRowForPrimaryKeyId($offering->session_id);
    	$sessionTitle = $session->title;
    	$sessionType = $this->sessionType->getRowForPrimaryKeyId($session->session_type_id);
    	$sessionTypeName = $sessionType->title;

        $course = $this->course->getRowForPrimaryKeyId($session->course_id);
        $courseTitle = $course->title;
        $school = $this->school->getRowForPrimaryKeyId($course->owning_school_id);
        if (! $school) {
            // no owning school? borked course - no email goes out.
            // log the incident and move on.
            log_message('error', "Failed to identify owning school for course (id = {$session->course_id}). No offering reminder was sent.");
            return;
        }
        $schoolName = $school->title;
        $schoolTemplatePrefix = $school->template_prefix;
        $schoolAdminEmail = $school->ilios_administrator_email;

    	/*** SET UP FOR THE EMAIL TEMPLATE ***/

    	//get/set the custom template location based on the school's respective template prefix
  	    $filepath_parent = getServerFilePath('alert_templates');
  	    //if the school's template prefix exists, add the 'custom' subfolder and the template prefix + underscore
  	    if(isset($schoolTemplatePrefix)) {
  	      $filepath_custom_prefix = 'custom/'.$schoolTemplatePrefix.'_';
  	    } else {
  	      $filepath_custom_prefix = '';
  	    }

  	    // use upcoming_teaching_session_template.txt as the base template name...
  	    $template_name = 'upcoming_teaching_session_template.txt';
  	    $filepath_custom = $filepath_parent . $filepath_custom_prefix . $template_name;
      	if(file_exists($filepath_custom) && is_readable($filepath_custom)) {
  	      $filepath = $filepath_custom;
  	    } else {
      	  $filepath = $filepath_parent . $template_name;
      	}
  	    $templateContent = file_get_contents($filepath);
    	// define its own token list
    	$tokenArray = array('%%SCHOOL_NAME%%', '%%SCHOOL_ILIOS_ADMIN_EMAIL%%',
    			'%%COURSE_TITLE%%', '%%COURSE_SESSION_LINK%%',
    			'%%SESSION_TITLE%%', '%%SESSION_TYPE%%',
    			'%%OFFERING_LOCATION%%', '%%OFFERING_DATE%%',
    			'%%OFFERING_START_TIME%%', '%%OFFERING_END_TIME%%',
    			'%%STUDENT_GROUP_LIST%%', '%%LEARNING_OBJECTIVE_LIST%%',
    	        '%%COURSE_OBJECTIVE_LIST%%');

    	// This course session url does not point to the right place yet.  Replace it with the base URL.
    	//
    	//$courseSessionURL = site_url() . '/course_management?course_id=' . $course->course_id
    	//                    . '&session_id=' . $session->session_id;
    	$courseSessionURL = base_url();

    	// find out how to get learning object list
    	$sessionObjectivesList = '';
    	$objectives = $this->iliosSession->getObjectivesForSession($offering->session_id);
    	foreach ($objectives as $objective) {
    		$sessionObjectivesList .= "\n    - ".strip_tags($objective['title']);
    	}

        // get the course objectives list
    	$courseObjectivesList = '';
    	$objectives = $this->course->getObjectivesForCourse($course->course_id);
    	foreach ($objectives as $objective) {
    		$courseObjectivesList .= "\n    - ".strip_tags($objective['title']);
    	}

    	$replaceValueBaseArray = array($schoolName, $schoolAdminEmail, $courseTitle,
    			$courseSessionURL, $sessionTitle, $sessionTypeName,
    			$offeringLocation, $offeringStartDate, $offeringStartTime,
    			$offeringEndTime, $studentList, $sessionObjectivesList, $courseObjectivesList);

    	$sentTotal = $this->mergeTemplateAndMailGroup($recipients, 'Upcoming Teaching Session',
    			$templateContent, $tokenArray, $replaceValueBaseArray,
    			$schoolAdminEmail);

    	log_message('info', "Sent email to $sentTotal users for offering id " . $offering->offering_id);
    }

    /**
     * @deprecated
     * @todo rewrite from scratch
     */
    protected function mergeTemplateAndMailGroup($recipients, $subject, $template, $tokens, $values, $sender) {

    	$count = 0;

    	foreach ($recipients as $recipient) {

    		// Will skip email address 'nobody@example.com', which is used for testing only.
    		if ($recipient['email'] == 'nobody@example.com')
    			continue;

    		$fromHeaders = 'From: Teaching Session Notification <' . $sender . '>';
    		$fullName = $recipient['first_name'] . ' ' . $recipient['last_name'];
    		$tokenArray = array_merge($tokens, array('%%RECIPIENT_NAME%%'));
    		$replaceArray = array_merge($values, array($fullName));
    		$substitutedEmail = str_replace($tokenArray, $replaceArray, $template);

    		if (isset($this->debug)) {
    			echo '<pre>';
    			echo 'These are the email headers:'."\n";
    			print_r($fromHeaders);
    			echo "\n\n";
    			echo 'This the recipient\'s email address:'."\n";
    			print_r($recipient['email']);
    			echo "\n\n";
    			echo 'This is the email subject:'."\n";
    			print_r($subject);
    			echo "\n\n";
    			echo 'This is the email (from template):'."\n";
    			print_r($substitutedEmail);
    			echo "\n</pre>";
    		} else {
    			mail($recipient['email'], $subject, $substitutedEmail, $fromHeaders);
    		}

    		$count++;
    	}

    	if ($count && isset($this->debug)) {
    		echo "<pre>Total email(s) that would have been sent: $count \n\n</pre>";
    	}
    	return $count;
    }
}
