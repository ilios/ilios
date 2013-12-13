<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

require_once 'base_authentication_controller.php';

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
     * Transform HTML formatted string into plain text
     *
     * @param string $s
     */
    private function _unHTML($s) {
      return str_replace("\n", ' ', trim(strip_tags($s)));
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

        $this->load->library("ICalExporter");

        // Specify the period of events to be included in the export.
        $timestart = strtotime("-5 days");     // last 5 days
        $timeend   = strtotime("+2 months");   // next 2 months (~60 days)

        $userId = $this->session->userdata('uid');

        $schoolId = $this->session->userdata('school_id');

        $offerings = $this->queries->getOfferingsDetailsForCalendar($schoolId, $userId, $userRoles, null, false,
            Ilios_Config_Defaults::DEFAULT_VISUAL_ALERT_THRESHOLD_IN_DAYS, $timestart, $timeend);
        $ilm_sessions = $this->queries->getSILMsForCalendar( $schoolId, $userId, $userRoles);

        $hostaddress = str_replace('http://', '', base_url());
        $hostaddress = str_replace('https://', '', $hostaddress);

        if ("/" == substr($hostaddress, strlen($hostaddress) - 1)) {
            // strip the slash at the end of the string.
            $hostaddress = substr($hostaddress, 0, strlen($hostaddress) - 1);
        }

        // Create an array of events for iCalendar from offerings array;
        // also combine offerings with same offering_ids.
        $events = array();
        foreach ($offerings as $offering) {
            $id = $offering['offering_id'];

            $offering['event_id'] = $id.'@'.$hostaddress; // UID
            $offering['text'] = $offering['session_title']; // SUMMARY
            $offering['location'] = array_key_exists('room', $offering) ? $offering['room'] : null;  // LOCATION
            $offering['utc_time'] = true;
            $offering['event_pid'] = null;
            $offering['rec_type'] = null;
            $offering['event_length'] = null;
            $details = '';

            if ($offering['description'])
                $details = $this->_unHTML($offering['description']) . "\n";

            // Taught by
            if (is_array($offering['instructors'])) {
                $details .= $this->languagemap->getI18NString(
                 'general.phrases.taught_by')
                 . ' ' . implode(', ', $offering['instructors']) . "\n";
            }

            // This offering is a(n)
            $details .= $this->languagemap->getI18NString(
             'dashboard.offering_description.offering_type')
             . ' ' . $offering['session_type'];

            if ($offering['supplemental']) {
              $details .=  $this->languagemap->getI18NString(
               'dashboard.offering_description.offering_supplemental_suffix')
               . "\n";
            } else {
              $details .= "\n";
            }
            if ($offering['attire_required']) {
              $details .= $this->languagemap->getI18NString(
               'dashboard.offering_description.special_attire'). "\n";
            }   
            if ($offering['equipment_required']) {
              $details .= $this->languagemap->getI18NString(
               'dashboard.offering_description.special_equipment'). "\n";
            }   
            if (count($offering['session_objectives']) > 0) {
              $details .= "\n";
              $details .= $this->languagemap->getI18NString(
               'general.terms.session') . ' ';
              $details .= $this->languagemap->getI18NString(
               'general.terms.objectives') . "\n";
              foreach ($offering['session_objectives'] as $objective)
                $details .= $this->_unHTML($objective) . "\n";
            }
            if (count($offering['session_materials']) > 0) {
              $details .= "\n";
              $details .= $this->languagemap->getI18NString(
               'general.terms.session') . ' ';
              $details .= $this->languagemap->getI18NString(
               'general.phrases.learning_materials') . "\n";
              foreach ($offering['session_materials'] as $material) {
                $details .= $this->_unHTML($material['title']);
                if ($material['required']) {
                  $details .= ' (' . $this->languagemap->getI18NString(
                   'general.terms.required'). ')';
                }
                $details .= ' (' . base_url()
                 . 'ilios.php/learning_materials/getLearningMaterialWithId?learning_material_id=' . $material['learning_material_id']
                 . ')';
                $details .= ': ' . $this->_unHTML($material['description']) . "\n";
              }
            }
            if (count($offering['course_objectives']) > 0) {
              $details .= "\n";
              $details .= $this->languagemap->getI18NString(
               'general.terms.course') . ' ';
              $details .= $this->languagemap->getI18NString(
               'general.terms.objectives') . "\n";
              foreach ($offering['course_objectives'] as $objective)
                $details .= $this->_unHTML($objective) . "\n";
            }
            if (count($offering['course_materials']) > 0) {
              $details .= "\n";
              $details .= $this->languagemap->getI18NString(
               'general.terms.course') . ' ';
              $details .= $this->languagemap->getI18NString(
               'general.phrases.learning_materials') . "\n";
              foreach ($offering['course_materials'] as $material) {
                $details .= $this->_unHTML($material['title']);
                $details .= ' (' . base_url()
                 . 'ilios.php/learning_materials/getLearningMaterialWithId?learning_material_id=' . $material['learning_material_id']
                 . ')';
                if ($material['required']) {
                  $details .= ' (' . $this->languagemap->getI18NString(
                   'general.phrases.learning_materials') . ')';
                }
                $details .= ': ' . $this->_unHTML($material['description']) . "\n";
              }
            }

            $offering['event_details'] = $details;

            $events[$id] = $offering;
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

                $session['text'] = $this->languagemap->getI18NString('course_management.session.independent_learning_short').': ';
                $session['text'] .= $ilm_session['hours'] . ' ';
                $session['text'] .= strtolower($this->languagemap->getI18NString('general.terms.hours')).' ';
                $session['text'] .= strtolower($this->languagemap->getI18NString('general.phrases.due_by')).' ';
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

    /**
     * @param int $userId
     * @param int $schoolId
     * @param array $userRoles
     * @return array
     *
     * @todo improve code docs.
     */
    protected function _exportCalendar($userId, $schoolId, array $userRoles)
    {
        $events = array();
        // @todo implement
        return  $events;
     }
}
