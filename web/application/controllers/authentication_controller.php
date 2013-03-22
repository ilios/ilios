<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

require_once 'ilios_base_controller.php';

/**
 * @package Ilios
 *
 * User authentication controller.
 * It provides login/logout interfaces.
 *
 * @todo This class should be sensitive to repeated failed authentication attempts.
 */
class Authentication_Controller extends Ilios_Base_Controller
{
    /**
     * Authentication subsystem name.
     * @var string
     */
    protected $_authn = 'default';
    /**
     * Constructor.
     */
    public function __construct ()
    {
        parent::__construct();

        $this->load->library('session');

        $this->load->model('Authentication', 'authentication', TRUE);
        $this->load->model('User', 'user', TRUE);

        // set the authentication subsystem to use
        $authn = $this->config->item('ilios_authentication');
        switch ($authn) {
            case 'shibboleth' :
                $this->_authn = 'shibboleth';
                break;
            case 'default' :
            default :
                // do nothing
        }
    }

    /**
     * Remaps calls for "action" methods to corresponding authentication-system-specific methods.
     * These actions are: "index", "login" and "logout".
     * All other methods are invoked under their given names.
     * This is a workaround to CodeIgniter's inability to internally forward a request from one controller to another.
     * Henceforth all supported authn systems cannot be subclassed into specific controllers
     * but are baked right into here.
     * They must follow a naming convention that requires implementing methods to have the same name as their
     * proxy action counterparts, but suffixed by an underscore, the authn subsystem's name and another underscore.
     *
     * Example:
     *
     * The configured active authn system is "shibboleth", and the invoked controller action is "login".
     * This will result in the request being forwarded to the <code>Authentication_Controller::_shibboleth_login()</code> method.
     *
     * See http://codeigniter.com/user_guide/general/controllers.html#remapping
     * @param string $method the name of the invoked controller action
     * @param array $params extra url segments
     * @return mixed
     */
    public function _remap ($method, $params = array())
    {
        // route index/login/lgout actions to the
        // corresponding authn-specific method and call it.
        if (in_array($method, array('index', 'login', 'logout'))) {
            $fn = '_' . $this->_authn . '_' . $method;
            return call_user_func_array(array($this, $fn), $params);
        }

        // check if method exists
        if (! method_exists($this, $method)) {
            show_404();
            return;
        }

        // security stop!
        // check if the requested method is public
        // if not then serve up a 403/VERBOTEN!
        $rm = new ReflectionMethod($this, $method);
        if (! $rm->isPublic()) {
            header('HTTP/1.1 403 Forbidden');
            return;
        }

        // public method - this is an "action". invoke it.
        return call_user_func_array(array($this, $method), $params);
    }

    /**
     * Implements the "index" action for the default/ilios-internal authn system.
     * This will load the login screen.
     * If the request parameter "logout" was passed then the user gets logged out first and then shown the login screen.
     */
    protected function _default_index ()
    {
        $logout = $this->input->get_post('logout');

        $username = $this->session->userdata('username');

        $lang = $this->getLangToUse();
        $data['lang'] = $lang;
        $data['login_message'] = $this->languagemap->getI18NString('login.default_status', $lang);
        $data['login_title'] = $this->languagemap->getI18NString('login.title', $lang);
        $data['word_login'] = $this->languagemap->getI18NString('general.terms.login', $lang);
        $data['word_password'] = $this->languagemap->getI18NString('general.terms.password', $lang);
        $data['word_username'] = $this->languagemap->getI18NString('general.terms.username', $lang);
        $data['last_url'] = '';
        $data['param_string'] = '';

        if(! $username) { // not logged in
             $this->load->view('login/login', $data);
        } else {
            if ($logout == 'yes') {
                $this->session->unset_userdata('username');
                $this->_default_logout();
            }
            $this->output->set_header('Expires: 0');
            $this->load->view('login/login', $data);
        }
    }

    /**
     * Implements the "logout" action for the default/ilios-internal authn system.
     */
    protected function _default_logout ()
    {
        $this->session->sess_destroy();
    }

    /**
     * Implements the "login" action for the default/ilios-internal authn system.
     * This method will attempt to authenticate a user based on provided credentials.
     * The result of this authentication will be printed as JSON-formatted array, containing either
     * a 'success' or 'error' value.
     * The following user input is expected in the request:
     *     'username' ... the user account login handle
     *     'password' ... the  corresponding password in plain text
     */
    protected function _default_login ()
    {
        $lang = $this->getLangToUse();

        $rhett = array();

        $username = $this->input->get_post('username');

        $password = $this->input->get_post('password');

        $salt = $this->config->item('ilios_authentication_internal_auth_salt');

        $authenticationRow = $this->authentication->getByUsername($username);

        $user = false;

        if ($authenticationRow) {
            if ('' !== trim($authenticationRow->password_sha256) // ensure that we have a password on file
                && $authenticationRow->password_sha256 === Ilios_PasswordUtils::hashPassword($password, $salt)) { // password comparison

                // load the user record
                $user = $this->user->getEnabledUsersById($authenticationRow->person_id);
            }
        }

        if ($user) { // authentication succeeded. log the user in.

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
                'display_fullname' => $user['first_name'] . ' ' . $user['last_name'],
                'display_last' => date('F j, Y G:i T', $now)
            );

            $this->session->set_userdata($sessionData);
            $rhett['success'] = 'huzzah';
        } else { // login failed
            $msg = $this->languagemap->getI18NString('login.error.bad_login', $lang);
            $rhett['error'] = $msg;
        }

        header("Content-Type: text/plain");
        echo json_encode($rhett);
    }

    /**
     * Implements the "index" action for the shibboleth authn system.
     */
    protected function _shibboleth_index ()
    {
        $lang = $this->getLangToUse();

        $logout = $this->input->get_post('logout');

        $data = array();
        $data['lang'] = $lang;

        if ($logout == 'yes') {
            $this->_shibboleth_logout();
            $data['logout_in_progress'] = $this->languagemap->getI18NString('logout.logout_in_progress', $lang);
            $this->load->view('login/logout', $data);
        } else {
            $emailAddress = "illegal_em4!l_addr3ss";
            $shibbUserIdAttribute = $this->config->item('ilios_authentication_shibboleth_user_id_attribute');
            $shibbSessionUserKey = $this->config->item('ilios_authentication_shibboleth_user_session_constant');
            $shibUserId = array_key_exists($shibbUserIdAttribute, $_SERVER) ? $_SERVER[$shibbUserIdAttribute] : null; // passed in by Shibboleth
            $shibUserKey = array_key_exists($shibbSessionUserKey, $_SERVER) ? $_SERVER[$shibbSessionUserKey] : null; // passed in by Shibboleth
            if (! empty($shibUserId)) {
                $emailAddress = $shibUserId;
            }


            $authenticatedUsers = $this->user->getEnabledUsersWithEmailAddress($emailAddress);
            $userCount = count($authenticatedUsers);

            if ($userCount == 0) {
                $data['forbidden_warning_text']  = $this->languagemap->getI18NString('login.error.no_match_1', $lang)
                    . ' (' . $emailAddress . ') ' . $this->languagemap->getI18NString('login.error.no_match_2', $lang);
                $this->load->view('common/forbidden', $data);
            } else if ($userCount > 1) {
                $data['forbidden_warning_text'] = $this->languagemap->getI18NString('login.error.multiple_match', $lang)
                    . ' (' . $emailAddress . ' [' . $userCount . '])';
                $this->load->view('common/forbidden', $data);
            } else {
                $user = $authenticatedUsers[0];
                if ($this->user->userAccountIsDisabled($user['user_id'])) {
                    $data['forbidden_warning_text'] = $this->languagemap->getI18NString('login.error.disabled_account', $lang);
                    $this->load->view('common/forbidden', $data);
                } else {
                    $now = time();
                    $sessionData = array(
                        'uid' => $user['user_id'],
                        'username' => $emailAddress,
                        'is_learner' => $this->user->userIsLearner($user['user_id']),
                        'has_instructor_access' => $this->user->userHasInstructorAccess($user['user_id']),
                        'has_admin_access' => $this->user->userHasAdminAccess($user['user_id']),
                        'primary_school_id' => $user['primary_school_id'],
                        'school_id' => $user['primary_school_id'],
                        'login' => $now,
                        'last' => $now,
                        'display_fullname' => $user['first_name'] . ' ' . $user['last_name'],
                        'display_last' => date('F j, Y G:i T', $now),
                        '_shib_attr' => $shibUserKey
                    );
                    $this->session->set_userdata($sessionData);
                    $this->session->set_flashdata('logged_in', 'jo');
                    if ($this->session->userdata('last_url')) {
                        $this->output->set_header("Location: " . $this->session->userdata('last_url'));
                        $this->session->unset_userdata('last_url');
                    } else {
                        $this->output->set_header("Location: " . base_url() . "ilios.php/dashboard_controller");
                    }
                }
            }
        }
    }

    /**
     * Implements the "logout" action for the shibboleth authn system.
     */
    protected function _shibboleth_logout ()
    {
        $this->session->sess_destroy();
    }

    /**
     * Implements the "login" action for the shibboleth authn system.
     */
    protected function _shibboleth_login ()
    {
        // not implemented
    }
}
