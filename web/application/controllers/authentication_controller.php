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
     * Accepts the following GET parameters:
     *     'logout' ... if the value is 'yes' then the current user session will be terminated before the login page is printed.
     *
     * @see Authentication_Controller::index()
     * @see Authentication_Controller::_default_logout()
     */
    protected function _default_index ()
    {
        $logout = $this->input->get('logout');
        $username = $this->session->userdata('username');

        $data['login_message'] = $this->languagemap->getI18NString('login.default_status');

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
     * On successful login, the user will be redirected to the dashboard.
     * On login failure, the user will be thrown back onto the login screen.
     *
     * @see Authentication_Controller::login()
     * @todo Add proper input validation. [ST 2013/12/23]
     * @todo Add CSRF token to login form. [ST 2013/12/23]
     */
    protected function _default_login ()
    {
        $username = $this->input->post('username');
        $password = $this->input->post('password');

        $salt = $this->config->item('ilios_authentication_internal_auth_salt');

        $authenticationRow = $this->authentication->getByUsername($username);

        $user = array();

        if ($authenticationRow) {
            if ('' !== trim($authenticationRow->password_sha256) // ensure that we have a password on file
                && $authenticationRow->password_sha256 === Ilios_PasswordUtils::hashPassword($password, $salt)) { // password comparison

                // load the user record
                $user = $this->user->getEnabledUserById($authenticationRow->person_id);
            }
        }

        // authentication succeeded. log the user in, then redirect to the dashboard.
        if (! empty($user)) {
            $this->_storeUserInSession($user);
            $this->output->set_header("Location: " . base_url() . "ilios.php/dashboard_controller");
            return;
        }

        // handle login error
        $this->output->set_header('Expires: 0');
        $this->load->view('login/login', array(
            'login_message' => $this->languagemap->getI18NString('login.error.bad_login'))
        );
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
        $logout = $this->input->get('logout');

        $data = array();

        if ($logout == 'yes') {
            $this->_shibboleth_logout();
            return;
        }

        $shibbUserIdAttribute = $this->config->item('ilios_authentication_shibboleth_user_id_attribute');
        $authFieldToMatch = $this->config->item('ilios_authentication_field_to_match');
        $shibUserId = array_key_exists($shibbUserIdAttribute, $_SERVER) ? $_SERVER[$shibbUserIdAttribute] : null; // passed in by Shibboleth
        if (! empty($shibUserId)) {
            $identifier = '';
            $authenticatedUsers = array();
            switch($authFieldToMatch) {
                case 'uc_uid':
                    $identifier = trim($shibUserId);
                    $authenticatedUsers = $this->user->getEnabledUsersWithInstitutionId($identifier);
                    break;
                case 'email':
                default:
                    /**
                     * Some schools release the 'mail' attribute twice, urn:mace:dir:attribute-def:mail (SAML1) AND
                     * urn:oid:0.9.2342.19200300.100.1.3 (SAML2), as one string of two email addresses separated by a semi-
                     * colon.  They should always be the same value so, to account for this, explode the returned value on the
                     * semicolon and just use the first one...
                     */
                    $mailAttributes = explode(';',$shibUserId);
                    $identifier = $mailAttributes[0];
                    $authenticatedUsers = $this->user->getEnabledUsersWithEmailAddress($identifier);
                    break;
            }
            $userCount = count($authenticatedUsers);

            if ($userCount == 0) {
                switch($authFieldToMatch) {
                    case 'uc_uid':
                        $data['forbidden_warning_text']  = $this->languagemap->getI18NString('login.error.uid_no_match_1');
                        break;
                    case 'email':
                    default:
                        $data['forbidden_warning_text']  = $this->languagemap->getI18NString('login.error.email_no_match_1');
                }
                $data['forbidden_warning_text'] .= ' (' . $identifier . ') ';
                $data['forbidden_warning_text'] .= $this->languagemap->getI18NString('login.error.no_match_2');
                $this->load->view('common/forbidden', $data);

            } else if ($userCount > 1) {
                $data['forbidden_warning_text'] = $this->languagemap->getI18NString('login.error.multiple_match');
                $data['forbidden_warning_text'] .= ' (' . $identifier . ') [' . $userCount . ']';
                $this->load->view('common/forbidden', $data);
            } else {
                $user = $authenticatedUsers[0];
                if ($this->user->userAccountIsDisabled($user['user_id'])) {
                    $data['forbidden_warning_text'] = $this->languagemap->getI18NString('login.error.disabled_account');
                    $this->load->view('common/forbidden', $data);
                } else {
                    $this->_storeUserInSession($user);
                    if ($this->session->userdata('last_url')) {
                        $this->output->set_header("Location: " . $this->session->userdata('last_url'));
                        $this->session->unset_userdata('last_url');
                    } else {
                        $this->output->set_header("Location: " . base_url() . "ilios.php/dashboard_controller");
                    }
                }
            }
        } else {
          //no id in shib session
          $data['forbidden_warning_text'] = $this->languagemap->getI18NString('login.error.missing_id');
          $this->load->view('common/forbidden', $data);
        }
    }

    /**
     * Implements the "logout" action for the shibboleth authentication system.
     *
     * This method destroys the current user-session and redirects the user
     * to the external logout URL.
     *
     * @see Authentication_Controller::logout()
     */
    protected function _shibboleth_logout ()
    {
        $redirect = $this->config->item("ilios_authentication_shibboleth_logout_path");
        if (! $redirect) {
            $redirect = '/Shibboleth.sso/Logout';
        }
        $this->session->sess_destroy();
        $this->output->set_header("Location: " . $redirect);
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

     * Accepts the following POST parameters:
     *     'username' ... the user account login handle
     *     'password' ... the  corresponding password in plain text
     *
     * @todo Add CSRF token to login form. [ST 2013/12/23]
     */
    public function _ldap_login ()
    {
        $errorMessages = array();

        // get login credentials from user input
        $username = $this->input->post('username');
        $password = $this->input->post('password');

        if (empty($username)) {
            $errorMessages[] = $this->languagemap->getI18NString('login.error.username_missing');
        }

        if (empty($password)) {
            $errorMessages[] = $this->languagemap->getI18NString('login.error.password_missing');
        }

        $authenticated = false;

        if(!empty($username) and !empty($password)){
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
                $errorMessages[] = $this->languagemap->getI18NString('login.error.provider_error');
            }
            $user = false;
            if ($authenticated) { // login succeeded
                // get the user record from the database
                $authenticationRow = $this->authentication->getByUsername($username);
                if ($authenticationRow) {
                    // load the user record
                    $user = $this->user->getEnabledUserById($authenticationRow->person_id);
                }

                if ($user) {
                    $this->_storeUserInSession($user);
                } else {
                    //  login was success but we don't have a corresponding user record on file
                    // or the user is disabled
                    $errorMessages[] = $username . ' ' . $this->languagemap->getI18NString('login.error.no_match_2');
                }
            } else { // login failed
                $errorMessages[] = $this->languagemap->getI18NString('login.error.bad_login');
            }
        }

        // login succeeded. redirect to dashboard.
        if ($user and empty($errorMessages)) {
            $this->output->set_header("Location: " . base_url() . "ilios.php/dashboard_controller");
            return;
        }

        // handle login error.
        $this->output->set_header('Expires: 0');
        $this->load->view('login/login', array(
            'login_message' => implode('<br />', $errorMessages))
        );
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
     * Takes a user record and populates the user session from it.
     *
     * @method array $user An associative array representing a user record.
     */
    protected function _storeUserInSession (array $user) {
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
    }
}
