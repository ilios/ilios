<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

require_once 'lios_web_controller.php';

/**
 * @package Ilios
 * Calendar exporter controller.
 */
class Calendar_Exporter extends Ilios_Web_Controller
{
    /**
     * Constructor
     */
    public function __construct ()
    {
        parent::__construct();
        $this->load->library('CalendarFeedDataProvider');
    }

    /**
     * Index action.
     */
    public function index ()
    {
        // Default to export in ICalendar format
        $this->exportICalendar();
    }

    /**
     * API interface
     */
    public function api ($key)
    {
        $authenticationRow = $this->authentication->getByAPIKey($key);

        $user = false;

        if ($authenticationRow) {
            // load the user record
            $user = $this->user->getEnabledUsersById($authenticationRow->person_id);
        }

        if ($user) { // authentication succeeded. log the user in.
            $this->_log_in_user($user);
            // Default to export in ICalendar format
            $this->exportICalendar();

            // Invalidate the session we just created so it can't be reused
            // for other purposes.  The session library wants to send a
            // Cookie header to the browser to complete the session
            // termination, but that won't work since we've already
            // outputted non-header data in exportICalendar, so use
            // @ to swallow the warning
            @$this->session->sess_destroy();
        } else { // login failed
            header('HTTP/1.1 403 Forbidden'); // VERBOTEN!
        }
    }
    /**
     * @todo add code docs
     * @param string $role
     */
    public function exportICalendar ($role='all')
    {
        $userRoles = array();

        // authorization check and capture of user requested/available user roles
        if ($this->session->userdata('is_learner') && ($role == 'all' || $role == 'student')) {
            $userRoles[] = User_Role::STUDENT_ROLE_ID;
        }
        if ($this->session->userdata('has_instructor_access') && ($role == 'all' || $role == 'instructor')) {
            $userRoles[] = User_Role::FACULTY_ROLE_ID;
            $userRoles[] = User_Role::COURSE_DIRECTOR_ROLE_ID;
        }
        if (! count($userRoles)) {
            header('HTTP/1.1 403 Forbidden'); // VERBOTEN!
            return;
        }

        $userId = $this->session->userdata('uid');

        $schoolId = $this->session->userdata('school_id');

        $this->load->library("ICalExporter");

        $events = $this->calendarfeeddataprovider->getData($userId, $schoolId, $userRoles);

        $calendar_title = 'Ilios Calendar';
        $this->icalexporter->setTitle($calendar_title);
        $ical = $this->icalexporter->toICal($events);

        $filename="ilios_calendar.ics";
        header('Last-Modified: '. gmdate('D, d M Y H:i:s', time()) .' GMT');
        header('Cache-Control: private, must-revalidate, pre-check=0, post-check=0, max-age=0');
        header('Expires: '. gmdate('D, d M Y H:i:s', 0) .'GMT');
        header('Pragma: no-cache');
        header('Accept-Ranges: none');
        header('Content-disposition: attachment; filename='.$filename);
        header('Content-length: '.strlen($ical));
        header('Content-type: text/calendar');

        echo $ical;
    }
}
