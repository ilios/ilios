<?php

/**
 * Implementation of the change alert notification process.
 */
class Ilios_ChangeAlert_NotificationProcess
{
    /* constants */
    /**
     * default offering change alerts template
     * @var string
     */
    const DEFAULT_OFFERING_ALERT_TEMPLATE = 'offering_change_template.txt';

    /**
     * process configuration.
     * @var array
     */
    protected $_config;

    /**
     * file path to directory holding the notification templates.
     * @var string
     */
    protected $_templatesDirPath;

    /**
     * Alert DAO
     * @var Alert
     */
    protected $_alertDao;

    /**
     * Course DAO
     * @var Course
     */
    protected $_courseDao;

    /**
     * Offering DAO
     * @var Offering
     */
    protected $_offeringDao;

    /**
     * School DAO
     * @var School
     */
    protected $_schoolDao;

    /**
     * Session DAO
     * @var Session
     */
    protected $_sessionDao;

    /**
     * Session Type DAO
     * @var Session_Type
     */
    protected $_sessionTypeDao;

    /**
     * Server Timezone
     * @var DateTimeZone
     */
    protected $_localTz;

    /**
     * UTC Timezone.
     * @var DateTimeZone
     */
    protected $_utcTz;

    /**
     * Interal cache registry.
     * @var array
     */
    protected $_caches = array();


    /**
     * Constructor.
     * @param array $config
     * @param Alert $alertDao
     * @param School $schoolDao
     * @param Offering $offeringDao
     * @param Session $sessionDao
     * @param Session_Type $sessionTypeDao
     * @param Course $courseDao
     * @throws Ilios_ChangeAlert_Exception
     */
    public function __construct (array $config, Alert $alertDao, School $schoolDao,
            Offering $offeringDao, Session $sessionDao, Session_Type $sessionTypeDao, Course $courseDao)
    {
        $this->_alertDao = $alertDao;
        $this->_schoolDao = $schoolDao;
        $this->_offeringDao = $offeringDao;
        $this->_sessionDao = $sessionDao;
        $this->_sessionTypeDao = $sessionTypeDao;
        $this->_courseDao = $courseDao;
        $this->_config = $config;
        // set timezones
        $this->_utcTz = new DateTimeZone('UTC'); // UTC timezone
        $this->_localTz = new DateTimeZone(date_default_timezone_get());

    }

    /**
     * Returns the path to the notification templates directory.
     * @return string the path to the templates directory
     * @throws Ilios_ChangeAlert_Exception if no directory can be found
     */
    protected function _getTemplatesDirPath ()
    {
        if (! isset($this->_templatesDirPath)) {
            $path = null;
            if (! array_key_exists('templates_dir_path', $this->_config)) {
                 throw new Ilios_ChangeAlert_Exception(
                     'Configuration Error: Templates directory missing.',
                     Ilios_ChangeAlert_Exception::TEMPLATES_DIR_MISSING);

            }
            $path = $this->_config['templates_dir_path'];

            if (! isset($path) || ! is_dir($path)) {
                throw new Ilios_ChangeAlert_Exception(
                    "Templates directory '{$path}' not found.",
                    Ilios_ChangeAlert_Exception::TEMPLATES_DIR_NOT_FOUND);
            }
            $this->_templatesDirPath = $path;

        }
        return $this->_templatesDirPath;
    }

    /**
     * Retrieves the content of a given message template file.
     * @param string $filename name of the template file
     * @return string the template file content
     * @throws Ilios_ChangeAlert_Exception if template cannot be found or read.
     */
    protected function _getMessageTemplateContent($filename)
    {
        $filePath = $this->_getTemplatesDirPath() . basename($filename);
        if (! file_exists($filePath) || ! is_file($filePath)) {
            throw new Ilios_ChangeAlert_Exception("Template file '{$filePath}' not found.",
                Ilios_ChangeAlert_Exception::TEMPLATE_FILE_NOT_FOUND);
        }

        $content = @file_get_contents($filePath);
        if (false === $content) {
            throw new Ilios_ChangeAlert_Exception("Template file '{$filePath}' not read.",
                Ilios_ChangeAlert_Exception::TEMPLATE_FILE_UNREADABLE);
        }
        return $content;
    }
    /**
     * Runs the change alert notification process.
     * @param Ilios_Logger $logger file logger
     */
    public function run (Ilios_Logger $logger, $debug = false)
    {
        $processId = time();
        $this->_clearAllCaches(); // reset the cache registry
        $logger->info('Kicking off the offering change alert notification process, here we go...', $processId);
        $this->_processOfferingChangeAlerts($processId, $logger, $debug);
        $logger->info('Completed offering change alert notification process.', $processId);
        $logger->info(Ilios_Logger::LOG_SEPARATION_LINE, $processId);
        $logger->info(Ilios_Logger::LOG_SEPARATION_LINE, $processId);
    }


    /**
     * Processes undispatched change alerts.
     * @param int $processId the id of the currently running process
     * @param Ilios_Logger $logger file logger
     * @param boolean $debug debug flag, set to TRUE to turn debugging on
     */
    protected function _processOfferingChangeAlerts ($processId, Ilios_Logger $logger, $debug = false)
    {
    	// process undispatched change alerts on a per-school basis
    	$schools = $this->_schoolDao->getSchoolsMap();
    	foreach ($schools as $school) {
    		$this->_processOfferingChangeAlertsBySchool($processId, $logger, $school, $debug);
    	}
    }

    /**
     * Processes offering change alerts for a given school.
     * @param int $processId the id of the currently running process
     * @param Ilios_Logger $logger file logger
     * @param array $school array representing a school record
     * @param boolean $debug set to TRUE to turn debugging on
     */
    protected function _processOfferingChangeAlertsBySchool($processId, Ilios_Logger $logger, array $school, $debug = false)
    {
        $logger->info("Started processing offering change alerts for School of {$school['title']}.", $processId);
        // load unprocessed offering change alerts
    	$changeAlerts = $this->_alertDao->getUndispatchedAlertsBySchoolAndTable($school['school_id'], 'offering');

    	$alertCount = count($changeAlerts);
    	if (! $alertCount) {
    	    $logger->info("There are no offering change alerts queued for processing.", $processId);
    	    $logger->info(Ilios_Logger::LOG_SEPARATION_LINE, $processId);
    	    return;
    	}
    	$mailTo = $school['change_alert_recipients'];

    	if (! empty($mailTo)) {

    	    $mailBodyTemplate = $this->_getMessageTemplateContent('offering_change_template.txt');
    	    $mailSubjectTemplate = '%%COURSE_EXT_ID%%: %%OFFERING_START_DATE_SHORT%%';
    	    $mailFrom = $school['ilios_administrator_email'];

    	    $placeholderTokens = array(
    	    		'%%SCHOOL_NAME%%', '%%SCHOOL_ILIOS_ADMIN_EMAIL%%',
    	    		'%%OFFERING_LOCATION%%', '%%OFFERING_DATE%%',
    	    		'%%OFFERING_START_DATE%%', '%%OFFERING_END_DATE%%',
    	    		'%%OFFERING_START_TIME%%', '%%OFFERING_END_TIME%%',
    	    		'%%CHANGE_TYPE_LIST%%', '%%CHANGE_HISTORY_LIST%%',
    	    		'%%COURSE_TITLE%%', '%%LEARNING_OBJECTIVES_LIST%%',
    	            '%%SESSION_TITLE%%', '%%SESSION_TYPE%%',
    	    		'%%STUDENT_GROUP_LIST%%', '%%INSTRUCTOR_LIST%%'
    	    );

    	    $logger->info("Processing {$alertCount} queued offering change alerts.", $processId);

    	    foreach ($changeAlerts as $alert) {
    	        //
                $substitutions =  $this->_getValuesForOfferingChangeAlertNotification($alert, $school, $placeholderTokens);
    			$mailBody = $this->_populateTemplate($mailBodyTemplate, $substitutions);
    			$mailSubject = $this->_populateTemplate($mailSubjectTemplate, $substitutions);

    			if ($debug) {
    			    // log message
    			    $logger->debug(Ilios_Logger::LOG_SEPARATION_LINE, $processId, 1);
    			    $logger->debug("FROM: {$mailFrom}", $processId, 1);
    			    $logger->debug("TO: {$mailTo}", $processId, 1);
    			    $logger->debug("SUBJECT: {$mailSubject}", $processId, 1);
    			    $logger->debug("BODY:" . PHP_EOL . $mailBody, $processId, 1);
    			}
   			    $this->_mailNotification($mailFrom, $mailTo, $mailSubject, $mailBody);
    		}
    	} else {
    	   $logger->warn("No change alert recipients are configured for this school, so no messages were sent.", $processId);
    	}

    	// flag all retrieved change alerts as processed
    	$logger->info("Flagging {$alertCount} offering change alerts as dispatched.", $processId);
    	$changeAlertIds = array();
    	foreach ($changeAlerts as $changeAlert) {
    		$changeAlertIds[] = $changeAlert['alert_id'];
    	}
        $this->_alertDao->startTransaction();
        $this->_alertDao->markAlertsAsDispatched($changeAlertIds);
        if ($this->_alertDao->transactionAtomFailed()) {
            $this->_alertDao->rollbackTransaction();
            $logger->warn('DB Transaction error: Failed to flag offering change alerts as dispatched.', $processId);
        } else {
            $this->_alertDao->commitTransaction();
        }

        $logger->info("Completed processing offering change alerts.", $processId);
        $logger->info(Ilios_Logger::LOG_SEPARATION_LINE, $processId);
    }

    /**
     * Substitutes the placeholder token in a given template with
     * given values.
     * @param string $template the template text
     * @param array $data assoc. array of token/replacement-value pairs
     * @return string the fully populated template
     */
    protected function _populateTemplate($template, array $data)
    {
        $token = array_keys($data);
        $values = array_values($data);
        return str_replace($token, $values, $template);
    }

    /**
     * Sends out a given notification to given recipient(s).
     * @param string $sender
     * @param string $recipients
     * @param string $subject
     * @param string $body
     */
    protected function _mailNotification($sender, $recipients, $subject, $body)
    {
        $headers = "From: Change Notification <{$sender}>";
        mail($recipients, $subject, $body, $headers);
    }

    /**
     * Pulls all the required data to populate the offering change alert notification template
     * and returns it.
     * @param array $alert change alert record
     * @param array $school record
     * @param array $placeholderTokens an array of placeholder tokens used in the notification template
     * @return array an associative array of placeholder-tokens/replacement-values that can be used to populate the notification template
     */
    protected function _getValuesForOfferingChangeAlertNotification(array $alert, array $school, array $placeholderTokens)
    {

        $rhett = array_fill_keys($placeholderTokens, ''); // prime the substitutions array

        // school name and admin email
        $rhett['%%SCHOOL_NAME%%'] = $school['title'];
        $rhett['%%SCHOOL_ILIOS_ADMIN_EMAIL%%'] = $school['ilios_administrator_email'];

        // get associated offering
        $offering = $this->_offeringDao->getRowForPrimaryKeyId($alert['table_row_id']);

        // offering start-date/time and end-date/time
        $dateValues = array();

        $offeringStartDate = new DateTime($offering->start_date, $this->_utcTz);
        $offeringEndDate   = new DateTime($offering->end_date, $this->_utcTz);
        // timezone adjustment
        $offeringStartDate->setTimezone($this->_localTz);
        $offeringEndDate->setTimezone($this->_localTz);
        $rhett['%%OFFERING_START_DATE%%'] =  $offeringStartDate->format('D M d, Y');
        $rhett['%%OFFERING_END_DATE%%'] =  $offeringEndDate->format('D M d, Y');
        if (strcmp($rhett['%%OFFERING_START_DATE%%'], $rhett['%%OFFERING_END_DATE%%'])) {
        	$rhett['%%OFFERING_DATE%%'] = $rhett['%%OFFERING_START_DATE%%'] . ' - ' . $rhett['%%OFFERING_END_DATE%%'];
        } else { // only show start-date if start- and end-date are the same
        	$rhett['%%OFFERING_DATE%%'] = $rhett['%%OFFERING_START_DATE%%'];
        }
        $rhett['%%OFFERING_START_TIME%%'] = $offeringStartDate->format('h:i a');
        $rhett['%%OFFERING_END_TIME%%'] = $offeringEndDate->format('h:i a');
        $rhett['%%OFFERING_START_DATE_SHORT%%'] = $offeringStartDate->format('m/d/Y');

        // offering location
        $rhett['%%OFFERING_LOCATION%%'] = trim($offering->room);

        // change types
        $changeTypes = $this->_alertDao->getChangeTypesForAlert($alert['alert_id']);
        if (! empty($changeTypes)) {
        	$rhett['%%CHANGE_TYPE_LIST%%'] = '    - ' . implode(PHP_EOL . '    - ', $changeTypes);
        }

        // change history
        $changeHistory = $this->_alertDao->getChangeHistoryForAlert($alert['alert_id']);
        if (! empty($changeHistory)) {
        	$rows = array();
        	foreach ($changeHistory as $change) {
        		// change time is stored in UTC in database, convert it to local tz
        		$changeTime = new DateTime($change['created_at'], $this->_utcTz);
        		$changeTime->setTimeZone($this->_localTz); // TZ adjustment
        		$row = '    - Updates made ' . $changeTime->format('m/d/Y') . ' at '
        		    . $changeTime->format('h:i a') . ' by ' . trim($change['first_name'] . ' ' . $change['last_name']);
        		$rows[] =  $row;
        	}
        	$rhett['%%CHANGE_HISTORY_LIST%%'] = implode(PHP_EOL, $rows);
        }

        // student list
        $learners = $this->_offeringDao->getLearnersAndLearnerGroupsForOffering($alert['table_row_id']);
        $rows = array();
        foreach ($learners as $learner) {
        	if (isset($learner['user_id'])) { // is user record
        		$rows[] = trim($learner['first_name'] . ' ' . $learner['last_name']);
        	} else { // is learner group otherwise
        		$rows[] = $learner['title'];
        	}
        }
        if (! empty($rows)) {
        	$rhett['%%STUDENT_GROUP_LIST%%'] = Ilios_MailUtils::implodeListForMail($rows, '; ');
        }

        // session
        $session = $this->_getSession($offering->session_id);
        $rhett['%%SESSION_TITLE%%'] = trim($session->title);

        // session type
        $sessionType = $this->_getSessionType($session->session_type_id);
        $rhett['%%SESSION_TYPE%%'] = trim($sessionType->title);

        // course title and "external id"
        $course = $this->_getCourse($session->course_id);
        $rhett['%%COURSE_TITLE%%'] = trim($course->title);
        $rhett['%%COURSE_EXT_ID%%'] = trim($course->external_id);

        // instructors
        $instructors = $this->_offeringDao->getIndividualInstructorsForOffering($offering->offering_id);
        $rows = array();
        foreach ($instructors as $instructor) {
        	if (isset($instructor['user_id'])) { // is user record
        		$rows[] = trim($instructor['first_name'] . ' ' . $instructor['last_name']);
        	} else { // is learner group otherwise
        		$rows[] = $instructor['title'];
        	}
        }
        if (! empty($rows)) {
        	$rhett['%%INSTRUCTOR_LIST%%']  = Ilios_MailUtils::implodeListForMail($rows, '; ');
        }

        // learning materials list
        $objectives = $this->_getSessionObjectives($offering->session_id);
        $rows = array();
        foreach ($objectives as $objective) {
        	$rows[] = $objective['title'];
        }
        if (! empty($rows)) {
        	$rhett['%%LEARNING_OBJECTIVES_LIST%%'] = '    - ' . implode(PHP_EOL . PHP_EOL . '    - ', $rows);
        }
        return $rhett;
    }

    /**
     * Retrieves a session by a given id.
     * @param int $sessionId the session identifer
     * @return stdClass a session object
     */
    protected function _getSession ($sessionId)
    {
        $cacheName = 'sessions';
        $cache = $this->_getCache($cacheName);
        if (! array_key_exists($sessionId, $cache)) {
        	$session = $this->_sessionDao->getRowForPrimaryKeyId($sessionId);
        	$cache[$sessionId] = $session;
        	$this->_setCache($cacheName, $cache);
        }
        return $cache[$sessionId];
    }

    /**
     * Retrieves a session-type by a given session id.
     * @param int $sessionId the session identifier
     * @return stdClass a session-type object
     */
    protected function _getSessionType ($sessionId)
    {
        $cacheName = 'session_types';
    	$cache = $this->_getCache($cacheName);
    	if (! array_key_exists($sessionId, $cache)) {
    		$sessionType = $this->_sessionTypeDao->getRowForPrimaryKeyId($sessionId);
    		$cache[$sessionId] = $sessionType;
    		$this->_setCache($cacheName, $cache);
    	}
    	return $cache[$sessionId];
    }

    /**
     * Retrieves a course by a given id.
     * @param int $courseId the course identifier
     * @return stdClass a course object
     */
    protected function _getCourse ($courseId)
    {
        $cacheName = 'courses';
        $cache = $this->_getCache($cacheName);
        if (! array_key_exists($courseId, $cache)) {
        	$course = $this->_courseDao->getRowForPrimaryKeyId($courseId);
        	$cache[$courseId] = $course;
        	$this->_setCache($cacheName, $cache);
        }
        return $cache[$courseId];
    }

    /**
     * Retrieves session objectives by a given a given session id.
     * @param int $sessionId the session identifier
     * @return array an array of objects each representing a session objective
     */
    protected function _getSessionObjectives ($sessionId)
    {
        $cacheName = 'session_objectives';
        $cache = $this->_getCache($cacheName);
        if (! array_key_exists($sessionId, $cache)) {
            $objectives = $this->_sessionDao->getObjectivesForSession($sessionId);
            $cache[$sessionId] = $objectives;
            $this->_setCache($cacheName, $cache);
        }
        return $cache[$sessionId];
    }

    /**
     * Resets the internal cache registry.
     */
    protected function _clearAllCaches ()
    {
        $this->_caches = array();
    }

    /**
     * Get a cache from the internal cache registry.
     * @param string $name name of the cache
     * @return array the cache
     */
    protected function _getCache ($name)
    {
        if (! array_key_exists($name, $this->_caches)) { // lazy init
            $this->_caches[$name] = array();
        }
        return $this->_caches[$name];
    }

    /**
     * Sets a given cache in the internal cache registry
     * @param string $name name of the cache
     * @param array $cache the cache
     */
    protected function _setCache ($name, array $cache)
    {
        $this->_caches[$name] = $cache;
    }
}
