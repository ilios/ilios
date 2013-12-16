<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

require_once 'ilios_base_controller.php';

/**
 * Ilios API controller.
 *
 * @package Ilios
 */
class Api extends Ilios_Base_Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->load->library("ICalExporter");
        $this->load->library("CalendarFeedDataProvider");
    }

    public function index ()
    {
        header('HTTP/1.1 404 Not Found'); // nothing here
        return;
    }

    /**
     * Calendar action.
     * It retrieves calendar events for a user authenticated by a given API token and prints them
     * out as iCalendar-formatted feed.
     *
     * @param $key The API key (token).
     */
    public function calendar ($key)
    {
        // authentication by token
        $user = $this->user->getEnabledUserByToken($key);
        if (! $user) {
            header('HTTP/1.1 403 Forbidden'); // VERBOTEN!
            return;
        }

        // kludge the applicable user roles together.
        $userRoles = array();
        if ($this->user->userIsLearner($user['user_id'])) {
            $userRoles[] = User_Role::STUDENT_ROLE_ID;
        }
        if ($this->user->userHasInstructorAccess($user['user_id'])) {
            $userRoles[] = User_Role::FACULTY_ROLE_ID;
            $userRoles[] = User_role::COURSE_DIRECTOR_ROLE_ID;
        }
        if (! count($userRoles)) {
            header('HTTP/1.1 403 Forbidden'); // VERBOTEN!
            return;
        }

        // load the calendar events
        // @todo Right now, we only retrieve events from the user's primary school. Make it work with multiple schools. [ST 2013/12/13]
        $events = $this->calendarfeeddataprovider->getData($user['user_id'], $user['primary_school_id'], $userRoles);

        $calendar_title = 'Ilios Calendar';
        $this->icalexporter->setTitle($calendar_title);
        $ical = $this->icalexporter->toICal($events);

        $filename = "ilios_calendar.ics";
        header('Last-Modified: '. gmdate('D, d M Y H:i:s', time()) .' GMT');
        header('Cache-Control: private, must-revalidate, pre-check=0, post-check=0, max-age=0');
        header('Expires: '. gmdate('D, d M Y H:i:s', 0) .'GMT');
        header('Pragma: no-cache');
        header('Accept-Ranges: none');
        header('Content-disposition: attachment; filename=' . $filename);
        header('Content-length: ' . strlen($ical));
        header('Content-type: text/calendar');

        echo $ical;
    }
}
