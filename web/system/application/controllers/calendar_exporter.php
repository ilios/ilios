<?php
include_once "abstract_ilios_controller.php";

/**
 * @package Ilios2
 * Calendar exporter controller.
 */
class Calendar_Exporter extends Abstract_Ilios_Controller
{
    /**
     * Constructor
     */
    public function __construct ()
    {
        parent::__construct();
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
     * @todo add code docs
     * @param string $role
     */
    public function exportICalendar ($role='all')
    {
        $lang =  $this->getLangToUse();

        // authentication check
        if ($this->divertedForAuthentication) {
            header('HTTP/1.1 403 Forbidden'); // VERBOTEN!
            return;
        }

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

        $this->load->library("ICalExporter");

        // Specify the period of events to be included in the export.
        $timestart = strtotime("-5 days");     // last 5 days
        $timeend   = strtotime("+2 months");   // next 2 months (~60 days)

        $userId = $this->session->userdata('uid');

        $schoolId = $this->session->userdata('school_id');

        $offerings = array();
        $ilm_sessions = array();

        $offerings = $this->queries->getOfferingsForCalendar( $schoolId, $userId, $userRoles);
        $ilm_sessions = $this->queries->getSILMsForCalendar( $schoolId, $userId, $userRoles);

        $hostaddress = str_replace('http://', '', base_url());
        $hostaddress = str_replace('https://', '', base_url());

        if ("/" == substr($hostaddress, strlen($hostaddress) - 1)) {
            // strip the slash at the end of the string.
            $hostaddress = substr($hostaddress, 0, strlen($hostaddress) - 1);
        }

        // Create an array of events for iCalendar from offerings array;
        // also combine offerings with same offering_ids.
        $events = array();
        foreach ($offerings as $offering) {

            if ($timestart <= strtotime($offering['start_date']) and
                $timeend >= strtotime($offering['start_date'])) {
                $id = $offering['offering_id'];

                $offering['event_id'] = $id.'@'.$hostaddress; // UID
                $offering['text'] = $offering['course_title'].' - '.$offering['session_title']; // SUMMARY
                $offering['event_details'] = $this->iliosSession->getDescription($offering['session_id']);  // DESCRIPTION
                $offering['location'] = array_key_exists('room', $offering) ? $offering['room'] : null;  // LOCATION
                $offering['utc_time'] = true;
                $offering['event_pid'] = null;
                $offering['rec_type'] = null;
                $offering['event_length'] = null;

                $events[$id] = $offering;
            }
        }

        foreach ($ilm_sessions as $session) {
            $ilm_session = $this->iliosSession->getSILMBySessionId($session['session_id']);

            if (empty($ilm_session))
                continue;

            if ($timestart <= strtotime($session['due_date']) and
                $timeend >= strtotime($session['due_date'])) {

                // Add a 'ilm-' prefex to the ilm_session id in case it conflicts with offering id.
                $id = 'ilm-'.$ilm_session['ilm_session_facet'];

                $session['event_id'] = $id.'@'.$hostaddress; // UID

                $session['text'] = $this->i18nVendor->getI18NString('course_management.session.independent_learning_short', $lang).': ';
                $session['text'] .= $ilm_session['hours'] . ' ';
                $session['text'] .= strtolower($this->i18nVendor->getI18NString('general.terms.hours', $lang)).' ';
                $session['text'] .= strtolower($this->i18nVendor->getI18NString('general.phrases.due_by', $lang)).' ';
                $session['text'] .= strftime('%a, %b %d', strtotime($session['due_date'])).' - ';
                $session['text'] .= $session['course_title'].' - '.$session['session_title']; // SUMMARY
                $session['event_details'] = $this->iliosSession->getDescription($session['session_id']);  // DESCRIPTION
                $session['start_date'] = $session['due_date'].' 17:00:00';
                $session['end_date'] = $session['due_date'].' 17:30:00';
                $session['location'] = null;
                $session['event_pid'] = null;
                $session['rec_type'] = null;
                $session['event_length'] = null;

                $events[$id] = $session;
            }
        }

        $calendar_title = 'Ilios Calendar';
        $this->icalexporter->setTitle($calendar_title);
        $ical = $this->icalexporter->toICal(array_values($events));

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
