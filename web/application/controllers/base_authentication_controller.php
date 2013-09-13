<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

require_once 'ilios_web_controller.php';

/**
 * @package Ilios
 *
 * Base user authentication controller.
 * It provides common functionality among classes that deal with
 * authentication
 *
 */
abstract class Base_Authentication_Controller extends Ilios_Web_Controller
{
    /**
     * Constructor.
     */
    public function __construct ()
    {
        parent::__construct();

        $this->load->library('session');

        $this->load->model('Authentication', 'authentication', true);
        $this->load->model('User', 'user', TRUE);
    }

    /**
     * Common log-in functionality across all methods
     */
    protected function _log_in_user($user) {
        $now = time();

        $sessionData = array(
            'uid' => $user['user_id'],
            'username' => $user['email'],
            'is_learner' => $this->user->userIsLearner($user['user_id']),
            'has_instructor_access' => $this->user->userHasInstructorAccess($user['user_id']),
            'has_admin_access' => $this->user->userHasAdminAccess($user['user_id']),
            'primary_school_id' => $user['primary_school_id'],
            'school_id' => $user['primary_school_id'],
            'login' => $now,
            'last' => $now,
            'lang_locale' => $this->getLangToUse(),
            'display_fullname' => $user['first_name'] . ' ' . $user['last_name'],
            'display_last' => date('F j, Y G:i T', $now),
            'api_key' => @$user['api_key']
        );

        $this->session->set_userdata($sessionData);
        return 'huzzah';
    }
}
