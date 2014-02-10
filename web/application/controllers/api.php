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
        $events = $this->calendarfeeddataprovider->getData($user['user_id'], null, $userRoles);

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

    /**
     * Performs a requested operation a given learning material.
     *
     * @param string $op The operation to perform. Currently, only "dl" (download) is supported.
     * @param string $token The pseudo key of the learning material.
     */
    public function lm ($op, $token = null)
    {
        if ('dl' !== $op) {
            header('HTTP/1.1 400 Bad Request'); // not supported operation
            return;
        }

        if (! $token) {
            header('HTTP/1.1 400 Bad Request'); // falsy token given
            return;
        }

        // retrieve the lm record by the pseudo key.
        $lm = $this->learningMaterial->getByToken($token);

        //
        if (! $lm) {
            header('HTTP/1.1 404 Not Found');
            return;
        }

        // no path? must not be a file asset then.
        if (! $lm['relative_file_system_location']) {
            header('HTTP/1.1 404 Not Found');
            return;
        }

        // build the absolute path to the file.
        // @todo this is hokey, find a better way to assemble the file path. [ST 2014/02/08]
        $file = getcwd() . $lm['relative_file_system_location'];

        // sanity check
        if (! is_file($file) || ! is_readable($file)) {
            header('HTTP/1.1 404 Not Found');
            return;
        }
        // set the proper response headers and stream down the file
        header("Content-Type: " . $lm['mime_type']);
        header('Content-Disposition: attachment; filename="' . $lm['filename'] . '"');

        $fileUtils = new Ilios_FileUtils();
        $fileUtils->streamFileContentsChunked($file);
    }

    /**
     * Downloads a requested learning material file.
     * This is a shortcut for calling <code>lm('dl', $token)</code>
     *
     * @param string $token The pseudo key of the learning material.

     * @see Api::lm()
     */
    public function dl ($token = null)
    {
        $this->lm('dl', $token);
    }
}
