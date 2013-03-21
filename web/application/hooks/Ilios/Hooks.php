<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
 * This class provides pre/post-processing hooks into the Ilios
 * workflow as defined by the CodeIgniter framework.
 *
 * @link http://codeigniter.com/user_guide/general/hooks.html
 */
class Ilios_Hooks
{
    /**
     * Implements a pre-system hook.
     * Registers a class autoloader.
     * @see Ilios_Hooks::autoload()
     */
    public function registerAutoloader ()
    {
        spl_autoload_register(array('Ilios_Hooks', 'autoload'));
    }

    /**
     * Class autoloader function.
     * @param string $className
     */
    public static function autoload ($className)
    {
        // do not attempt to autoload CodeIgniter classes
        // since CI has its own ways to load this stuff
        if (0 === strpos($className, 'CI_')) {
            return;
        }

        // convert the class name to a path
        // by replacing underscores with path separators
        // and by appending it with the '.php' file suffix
        // e.g. a class name "Ilios_Database_Constant"
        // will be converted to a corresponding file path
        // "Ilios/Database/Constant.php"
        $filePath = str_replace('_', DIRECTORY_SEPARATOR, $className) . '.php';

        // complete the file path construction
        // by scoping it down to the APPPATH/libraries subdirectory
        $filePath = APPPATH . 'libraries' . DIRECTORY_SEPARATOR . $filePath;

        // check if the file exists
        // if so then load it.
        if (file_exists($filePath)) {
            require_once $filePath;
        }
    }

    /**
     * Implements a post-controller-construct hook.
     * Checks if the incoming request requires an authenticated user session to be present.
     * Unauthenticated requests are handled accordingly, depending on the rules and context
     * they may
     * - pass
     * - result in a redirect to the login page
     * - return an error message
     * - return a 403 response
     */
    public function checkAuthentication ()
    {
        $ci =& get_instance();

        $controller = $ci->router->fetch_class();
        $action = $ci->router->fetch_method();

        //
        // 1. handle requests that don't require and authenticated user session first.
        //

        // 1a. no auth on CLI mode requests.
        if ($ci->input->is_cli_request()) {
            return;
        }

        // 1b. pass through requests to controllers that don't support user sessions
        if (! isset($ci->session)) {
            return;
        }

        // 1c. no authentication if Ilios runs in "test" mode.
        if ((array_key_exists('ILIOS_ENVIRONMENT', $_ENV)
            && 'test' == $_ENV['ILIOS_ENVIRONMENT'])) {
            return;
        }

        // 1d. white-list certain controller/action combinations.
        if ('getI18NJavascriptVendor' === $action) { // always, from all controllers
            return;
        }
        if ('status' === $controller
            && 'index' === $action) {
            return;
        };
        if ('authentication_controller' === $controller
            && in_array($action, array('index', 'login', 'logout'))) {
            return;
        }

        // 2. handle unauthenticated user sessions
        if (! $ci->session->userdata('username')) {
            // Handle XHR request:
            // Prints a JSON-formatted array with a generic, i18ned "not logged in" error message,
            // keyed off by "error".
            if ($ci->input->is_ajax_request()) {
                $lang = $ci->config->item('ilios_default_lang_locale');
                $msg = $ci->languagemap->getI18NString('login.error.not_logged_in', $lang);
                $rhett = array();
                $rhett['error'] = $msg;
                header("Content-Type: text/plain");
                echo json_encode($rhett);
                exit;
            }

            // Do not redirect un-authenticated requests
            // for the learning materials d/l action, or any actions on the calendar exporter controller.
            // Just emit a 403 http response code.
            if (('learning_materials' === $controller  && 'getLearningMaterialWithId' === $action)
                || ('calendar_exporter' === $controller)) {
                header('HTTP/1.1 403 Forbidden');
                exit;
            }

            // Save the current url so that we can redirect back to it after authenticated by
            // the authentication controller.  (Include the query string too if any.)
            $ci->session->set_userdata('last_url',
                $_SERVER['QUERY_STRING'] ?
                    current_url() . '?' . $_SERVER['QUERY_STRING'] : current_url());

            log_message('debug', 'Diverting user to the login page.');

            // redirect request to the authentication controller.
            redirect('authentication_controller');
        }
    }

    public function checkShibbolethSession ()
    {

        $ci =& get_instance();

        // cli mode, move on.
        if ($ci->input->is_cli_request()) {
            return;
        }

        // not running on shibboleth authentication.
        if ('shibboleth' !== $ci->config->item('ilios_authentication')) {
            return;
        }

        $key = $ci->config->item('ilios_authentication_shibboleth_user_session_constant');

        // the attribute was not passed.
        // this may happen if we the requested path is white-listed in the shibd config,
        // or if there is no shib session.
        if (! array_key_exists($key, $_SERVER)) {
            return;
        }

        // must have a user session in ilios.
        // if not, move on.
        if (! isset($ci->session)) {
            return;
        }

        // check if the user in the shib session has changed
        // since login
        // if so, then we will remove the username from the session.
        // this will force re-authentication further downstream.
        $inner = $ci->session->userdata('_shib_attr');
        $outer = $_SERVER[$key];
        if ($inner !== $outer) {
            log_message('debug', 'User in Shibboleth session has changed, invalidating Ilios user session.');
            $ci->session->unset_userdata('username');
        }
    }
}
