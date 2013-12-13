<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

require_once 'ilios_base_controller.php';

/**
 * @package Ilios
 *
 * User authentication controller.
 * It provides login/logout interfaces.
 *
 * @todo Refactor authentication sub-systems out into their own components.
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

        $this->load->model('Authentication', 'authentication', true);
        $this->load->model('User', 'user', true);

        // set the authentication subsystem to use
        $authn = $this->config->item('ilios_authentication');
        switch ($authn) {
            case 'shibboleth' :
                $this->_authn = 'shibboleth';
                break;
            case 'ldap' :
                $this->_authn = 'ldap';
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
     *
     * @param string $method the name of the invoked controller action
     * @param array $params extra url segments
     * @return mixed the output of the implementing functions
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
     *
     * This method will print out the login page.
     *
     * Accepts the following POST parameters:
     *     'logout' ... if the value is 'yes' then the current user session will be terminated before the login page is printed.
     *
     * @see Authentication_Controller::index()
     * @see Authentication_Controller::_default_logout()
     */
    protected function _default_index ()
    {
        $logout = $this->input->get_post('logout');

        $username = $this->session->userdata('username');

        $data['login_message'] = $this->languagemap->getI18NString('login.default_status');
        $data['login_title'] = $this->languagemap->getI18NString('login.title');
        $data['word_login'] = $this->languagemap->getI18NString('general.terms.login');
        $data['word_password'] = $this->languagemap->getI18NString('general.terms.password');
        $data['word_username'] = $this->languagemap->getI18NString('general.terms.username');
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
     * Implements the "logout" action for the default (Ilios-internal) authentication system.
     *
     * This method destroys the current user-session.
     *
     * @see Authentication_Controller::logout()
     */
    protected function _default_logout ()
    {
        $this->session->sess_destroy();
    }

    /**
     * Implements the "login" action for the default (Ilios-internal) authentication system.
     *
     * This method will attempt to authenticate and log-in a user based on the provided credentials.

     * Accepts the following POST parameters:
     *     'username' ... the user account login handle
     *     'password' ... the  corresponding password in plain text
     *
     * Prints out an result-array as JSON-formatted text.
     * On success, the result-array will contain a success message, keyed off by "success".
     * On failure, the result-array will contain an error message, keyed off by "error".
     *
     * @see Authentication_Controller::login()
     */
    protected function _default_login ()
    {
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
                $user = $this->user->getEnabledUserById($authenticationRow->person_id);
            }
        }

        if ($user) { // authentication succeeded. log the user in.
            $rhett['success'] = $this->_log_in_user($user);
        } else { // login failed
            $msg = $this->languagemap->getI18NString('login.error.bad_login');
            $rhett['error'] = $msg;
        }

        header("Content-Type: text/plain");
        echo json_encode($rhett);
    }

    /**
     * Implements the "index" action for the shibboleth authentication system.
     *
     * Unless requested otherwise (see "logout" parameter below), this action attempts to authenticate and log-in the
     * requesting user based on the attributes passed by the external authentication system.
     *
     * Accepts the following query string parameters:
     *     'logout' ... if 'yes' is provided as value then the user session will be terminated and the user will be
     *         redirected to the logout page.
     *
     * On successful authentication, the user will be redirect to the last visited URL ("post-back URL") within Ilios
     * if this information is available on login.
     * If no post-back URL is available, the user will be redirected to the dashboard page.
     * On authentication failure, the user will be redirected to an "access forbidden" page.
     *
     * @see Authentication_Controller::index()
     */
    protected function _shibboleth_index ()
    {
        $logout = $this->input->get_post('logout');

        $data = array();

        if ($logout == 'yes') {
            $this->_shibboleth_logout();
            $data['logout_in_progress'] = $this->languagemap->getI18NString('logout.logout_in_progress');
            $this->load->view('login/logout', $data);
        } else {
            $emailAddress = "illegal_em4!l_addr3ss";
            $shibbUserIdAttribute = $this->config->item('ilios_authentication_shibboleth_user_id_attribute');
            $shibUserId = array_key_exists($shibbUserIdAttribute, $_SERVER) ? $_SERVER[$shibbUserIdAttribute] : null; // passed in by Shibboleth
            if (! empty($shibUserId)) {
                $emailAddress = $shibUserId;
            }


            $authenticatedUsers = $this->user->getEnabledUsersWithEmailAddress($emailAddress);
            $userCount = count($authenticatedUsers);

            if ($userCount == 0) {
                $data['forbidden_warning_text']  = $this->languagemap->getI18NString('login.error.no_match_1')
                    . ' (' . $emailAddress . ') ' . $this->languagemap->getI18NString('login.error.no_match_2');
                $this->load->view('common/forbidden', $data);
            } else if ($userCount > 1) {
                $data['forbidden_warning_text'] = $this->languagemap->getI18NString('login.error.multiple_match')
                    . ' (' . $emailAddress . ' [' . $userCount . '])';
                $this->load->view('common/forbidden', $data);
            } else {
                $user = $authenticatedUsers[0];
                if ($this->user->userAccountIsDisabled($user['user_id'])) {
                    $data['forbidden_warning_text'] = $this->languagemap->getI18NString('login.error.disabled_account');
                    $this->load->view('common/forbidden', $data);
                } else {
                    $this->_log_in_user($user);
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
     * Implements the "logout" action for the shibboleth authentication system.
     *
     * This method destroys the current user-session.
     *
     * @see Authentication_Controller::logout()
     */
    protected function _shibboleth_logout ()
    {
        $this->session->sess_destroy();
    }

    /**
     * Implements the "login" action for the shibboleth authentication system.
     *
     * This method is does nothing, login is handled in the "_shibboleth_index".
     *
     * @see Authentication_Controller::_shibboleth_index()
     */
    protected function _shibboleth_login ()
    {
        // not implemented
    }



    /**
     * Implements the "login" action for the ldap authn system.
     */
    public function _ldap_login ()
    {
        $rhett = array();

        // get login credentials from user input
        $username = $this->input->get_post('username');
        $password = $this->input->get_post('password');

        $authenticated = false;

        // do LDAP authentication
        // by connecting and binding to the given ldap server with the user-provided credentials
        $ldapConf = $this->config->item('ilios_ldap_authentication');
        $ldapConn = @ldap_connect($ldapConf['host'], $ldapConf['port']);
        if ($ldapConn) {
            $ldapRdn = sprintf($ldapConf['bind_dn_template'], $username);
            $ldapBind = @ldap_bind($ldapConn, $ldapRdn, $password);
            if ($ldapBind) {
                $authenticated = true; // auth. successful
            }
        } else {
            die('couldnt connect to ldap server');
            // @todo log connectivity failure
        }

        if ($authenticated) { // login succeeded
            // get the user record from the database
            $authenticationRow = $this->authentication->getByUsername($username);
            if ($authenticationRow) {
                // load the user record
                $user = $this->user->getEnabledUserById($authenticationRow->person_id);
            }

            if ($user) {
                $rhett['success'] = $this->_log_in_user($user);
            } else {
                //  login was success but we don't have a corresponding user record on file
                // or the user is disabled
                $rhett['error']  = 'Your username does not match any active user records in Ilios. If you need further assistance, please contact your Ilios administrator. Thank you.';
            }
        } else { // login failed
            $msg = $this->i18nVendor->getI18NString('login.error.bad_login');
            $rhett['error'] = $msg;
        }

        header("Content-Type: text/plain");
        echo json_encode($rhett);
    }

    /**
     * Implements the "logout" action for the LDAP authn system.
     */
    public function _ldap_logout ()
    {
        $this->session->sess_destroy(); // nuke the current user session
    }

    /**
     * Implements the "index" action for the LDAP authn system.
     */
    public function _ldap_index ()
    {
        $this->_default_index(); // piggy-back on the default index method for displaying the login form
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
