<?php
/*
 * HACK HACK HACK!
 * This file instantiates a fully functional CodeIgniter environment up to the point of request processing,
 * but without actually triggering an action.
 * It's content has been salvaged from the default "index" file (web/ilios.php)
 * and the bootstrapper script (/web/system/core/CodeIgniter.php).
 *
 * Note:
 * Unlike the original bootstrapping process, which is run in global scope, this script is run within function scope.
 * Therefore, the instantiated "global" CI objects are forced into the proper scope by excplitly setting them in $GLOBALS.
 */

//
// Fake a HTTP request, even though CI 2.x has better tools for running the application in CLI-mode.
// However, we can't leverage that feature without trampling all over PHPUnit's command line arguments.
//

$_SERVER['REMOTE_ADDR'] = '127.0.0.1';
$_SERVER['PATH_INFO'] = '/test_controller/index';
$_SERVER['REQUEST_URI'] = '/test_controller/index';
$_SERVER['SERVER_NAME'] = 'ilios-test.library.ucsf.edu'; // it doesn't matter...
$_SERVER['QUERY_STRING'] = '';
$_ENV['ILIOS_ENVIRONMENT'] = 'test';  // indicate test environment


// off we go bootstrapping the web application...

// [snip index file]
/*
 *---------------------------------------------------------------
 * APPLICATION ENVIRONMENT
 *---------------------------------------------------------------
 *
 * You can load different configurations depending on your
 * current environment. Setting the environment also influences
 * things like logging and error reporting.
 *
 * This can be set to anything, but default usage is:
 *
 *     development
 *     testing
 *     production
 *
 * NOTE: If you change these, also change the error_reporting() code below
 *
 */
    define('ENVIRONMENT', 'development');
/*
 *---------------------------------------------------------------
 * ERROR REPORTING
 *---------------------------------------------------------------
 *
 * Different environments will require different levels of error reporting.
 * By default development will show errors but testing and live will hide them.
 */

if (defined('ENVIRONMENT'))
{
    switch (ENVIRONMENT)
    {
        case 'development':
            error_reporting(E_ALL);
        break;

        case 'testing':
        case 'production':
            error_reporting(0);
        break;

        default:
            exit('The application environment is not set correctly.');
    }
}

/*
 *---------------------------------------------------------------
 * SYSTEM FOLDER NAME
 *---------------------------------------------------------------
 *
 * This variable must contain the name of your "system" folder.
 * Include the path if the folder is not in the same  directory
 * as this file.
 *
 */
    $system_path = 'system';

/*
 *---------------------------------------------------------------
 * APPLICATION FOLDER NAME
 *---------------------------------------------------------------
 *
 * If you want this front controller to use a different "application"
 * folder then the default one you can set its name here. The folder
 * can also be renamed or relocated anywhere on your server.  If
 * you do, use a full server path. For more info please see the user guide:
 * http://codeigniter.com/user_guide/general/managing_apps.html
 *
 * NO TRAILING SLASH!
 *
 */
    $application_folder = 'application';

/*
 * --------------------------------------------------------------------
 * DEFAULT CONTROLLER
 * --------------------------------------------------------------------
 *
 * Normally you will set your default controller in the routes.php file.
 * You can, however, force a custom routing by hard-coding a
 * specific controller class/function here.  For most applications, you
 * WILL NOT set your routing here, but it's an option for those
 * special instances where you might want to override the standard
 * routing in a specific front controller that shares a common CI installation.
 *
 * IMPORTANT:  If you set the routing here, NO OTHER controller will be
 * callable. In essence, this preference limits your application to ONE
 * specific controller.  Leave the function name blank if you need
 * to call functions dynamically via the URI.
 *
 * Un-comment the $routing array below to use this feature
 *
 */
    // The directory name, relative to the "controllers" folder.  Leave blank
    // if your controller is not in a sub-folder within the "controllers" folder
    // $routing['directory'] = '';

    // The controller class file name.  Example:  Mycontroller
    // $routing['controller'] = '';

    // The controller function you wish to be called.
    // $routing['function'] = '';


/*
 * -------------------------------------------------------------------
 *  CUSTOM CONFIG VALUES
 * -------------------------------------------------------------------
 *
 * The $assign_to_config array below will be passed dynamically to the
 * config class when initialized. This allows you to set custom config
 * items or override any default config values found in the config.php file.
 * This can be handy as it permits you to share one application between
 * multiple front controller files, with each file containing different
 * config values.
 *
 * Un-comment the $assign_to_config array below to use this feature
 *
 */
    // $assign_to_config['name_of_config_item'] = 'value of config item';
$assign_to_config['uri_protocol'] = 'PATH_INFO'; // pretend to come in via HTTP request ALL THE WAY!

// --------------------------------------------------------------------
// END OF USER CONFIGURABLE SETTINGS.  DO NOT EDIT BELOW THIS LINE
// --------------------------------------------------------------------

/*
 * ---------------------------------------------------------------
 *  Resolve the system path for increased reliability
 * ---------------------------------------------------------------
 */

    // Set the current directory correctly for CLI requests
    if (defined('STDIN'))
    {
        chdir(dirname(dirname(__DIR__)) . '/web');
    }

    if (realpath($system_path) !== FALSE)
    {
        $system_path = realpath($system_path).'/';
    }

    // ensure there's a trailing slash
    $system_path = rtrim($system_path, '/').'/';

    // Is the system path correct?
    if ( ! is_dir($system_path))
    {
        exit("Your system folder path does not appear to be set correctly. Please open the following file and correct this: ".pathinfo(__FILE__, PATHINFO_BASENAME));
    }

/*
 * -------------------------------------------------------------------
 *  Now that we know the path, set the main path constants
 * -------------------------------------------------------------------
 */
    // The name of THIS file
    define('SELF', pathinfo(dirname(dirname(__DIR__)) . '/web/ilios.php', PATHINFO_BASENAME)); // fake it

    // The PHP file extension
    // this global constant is deprecated.
    define('EXT', '.php');

    // Path to the system folder
    define('BASEPATH', str_replace("\\", "/", $system_path));

    // Path to the front controller (this file)
    define('FCPATH', str_replace(SELF, '', __FILE__));

    // Name of the "system folder"
    define('SYSDIR', trim(strrchr(trim(BASEPATH, '/'), '/'), '/'));


    // The path to the "application" folder
    if (is_dir($application_folder))
    {
        define('APPPATH', $application_folder.'/');
    }
    else
    {
        if ( ! is_dir(BASEPATH.$application_folder.'/'))
        {
            exit("Your application folder path does not appear to be set correctly. Please open the following file and correct this: ".SELF);
        }

        define('APPPATH', BASEPATH.$application_folder.'/');
    }
// [/snip index file]

// [snip CI bootstrapper]
/**
 * CodeIgniter Version
 *
 * @var string
 *
 */
    define('CI_VERSION', '2.1.3');

/**
 * CodeIgniter Branch (Core = TRUE, Reactor = FALSE)
 *
 * @var boolean
 *
 */
    define('CI_CORE', FALSE);

/*
 * ------------------------------------------------------
 *  Load the global functions
 * ------------------------------------------------------
 */
    require(BASEPATH.'core/Common.php');

/*
 * ------------------------------------------------------
 *  Load the framework constants
 * ------------------------------------------------------
 */
    if (defined('ENVIRONMENT') AND file_exists(APPPATH.'config/'.ENVIRONMENT.'/constants.php'))
    {
        require(APPPATH.'config/'.ENVIRONMENT.'/constants.php');
    }
    else
    {
        require(APPPATH.'config/constants.php');
    }

/*
 * ------------------------------------------------------
 *  Define a custom error handler so we can log PHP errors
 * ------------------------------------------------------
 */
    set_error_handler('_exception_handler');

    if ( ! is_php('5.3'))
    {
        @set_magic_quotes_runtime(0); // Kill magic quotes
    }

/*
 * ------------------------------------------------------
 *  Set the subclass_prefix
 * ------------------------------------------------------
 *
 * Normally the "subclass_prefix" is set in the config file.
 * The subclass prefix allows CI to know if a core class is
 * being extended via a library in the local application
 * "libraries" folder. Since CI allows config items to be
 * overriden via data set in the main index. php file,
 * before proceeding we need to know if a subclass_prefix
 * override exists.  If so, we will set this value now,
 * before any classes are loaded
 * Note: Since the config file data is cached it doesn't
 * hurt to load it here.
 */
    if (isset($assign_to_config['subclass_prefix']) AND $assign_to_config['subclass_prefix'] != '')
    {
        get_config(array('subclass_prefix' => $assign_to_config['subclass_prefix']));
    }

/*
 * ------------------------------------------------------
 *  Set a liberal script execution time limit
 * ------------------------------------------------------
 */
    if (function_exists("set_time_limit") == TRUE AND @ini_get("safe_mode") == 0)
    {
        @set_time_limit(300);
    }

/*
 * ------------------------------------------------------
 *  Start the timer... tick tock tick tock...
 * ------------------------------------------------------
 */
    $BM =& load_class('Benchmark', 'core');
    $BM->mark('total_execution_time_start');
    $BM->mark('loading_time:_base_classes_start');
    $GLOBALS['BM'] = $BM;

/*
 * ------------------------------------------------------
 *  Instantiate the hooks class
 * ------------------------------------------------------
 */
    $EXT =& load_class('Hooks', 'core');
    $GLOBALS['EXT'] = $EXT;

/*
 * ------------------------------------------------------
 *  Is there a "pre_system" hook?
 * ------------------------------------------------------
 */
    $EXT->_call_hook('pre_system');


/*
 * ------------------------------------------------------
 *  Instantiate the config class
 * ------------------------------------------------------
 */
    $CFG =& load_class('Config', 'core');

    // Do we have any manually set config items in the index.php file?
    if (isset($assign_to_config))
    {
        $CFG->_assign_to_config($assign_to_config);
    }
    $GLOBALS['CFG'] = $CFG;

/*
 * ------------------------------------------------------
 *  Instantiate the UTF-8 class
 * ------------------------------------------------------
 *
 * Note: Order here is rather important as the UTF-8
 * class needs to be used very early on, but it cannot
 * properly determine if UTf-8 can be supported until
 * after the Config class is instantiated.
 *
 */

    $UNI =& load_class('Utf8', 'core');
    $GLOBALS['UNI'] = $UNI;
/*
 * ------------------------------------------------------
 *  Instantiate the URI class
 * ------------------------------------------------------
 */
    $URI =& load_class('URI', 'core');
    $GLOBALS['URI'] = $URI;
/*
 * ------------------------------------------------------
 *  Instantiate the routing class and set the routing
 * ------------------------------------------------------
 */
    $RTR =& load_class('Router', 'core');
    $RTR->_set_routing();

    // Set any routing overrides that may exist in the main index file
    if (isset($routing))
    {
        $RTR->_set_overrides($routing);
    }
    $GLOBALS['RTR'] = $RTR;
/*
 * ------------------------------------------------------
 *  Instantiate the output class
 * ------------------------------------------------------
 */
    $OUT =& load_class('Output', 'core');

/*
 * ------------------------------------------------------
 *  Is there a valid cache file?  If so, we're done...
 * ------------------------------------------------------
 */
    if ($EXT->_call_hook('cache_override') === FALSE)
    {
        if ($OUT->_display_cache($CFG, $URI) == TRUE)
        {
            exit;
        }
    }
    $GLOBALS['OUT'] = $OUT;
/*
 * -----------------------------------------------------
 * Load the security class for xss and csrf support
 * -----------------------------------------------------
 */
    $SEC =& load_class('Security', 'core');
    $GLOBALS['SEC'] = $SEC;
/*
 * ------------------------------------------------------
 *  Load the Input class and sanitize globals
 * ------------------------------------------------------
 */
    $IN =& load_class('Input', 'core');
    $GLOBALS['IN'] = $IN;

/*
 * ------------------------------------------------------
 *  Load the Language class
 * ------------------------------------------------------
 */
    $LANG =& load_class('Lang', 'core');
    $GLOBALS['LANG'] = $LANG;

/*
 * ------------------------------------------------------
 *  Load the app controller and local controller
 * ------------------------------------------------------
 *
 */
    // Load the base controller class
    require BASEPATH.'core/Controller.php';

    function &get_instance()
    {
        return CI_Controller::get_instance();
    }


    if (file_exists(APPPATH.'core/'.$CFG->config['subclass_prefix'].'Controller.php'))
    {
        require APPPATH.'core/'.$CFG->config['subclass_prefix'].'Controller.php';
    }

    // Load the local application controller
    // Note: The Router class automatically validates the controller path using the router->_validate_request().
    // If this include fails it means that the default controller in the Routes.php file is not resolving to something valid.
    if ( ! file_exists(APPPATH.'controllers/'.$RTR->fetch_directory().$RTR->fetch_class().'.php'))
    {
        show_error('Unable to load your default controller. Please make sure the controller specified in your Routes.php file is valid.');
    }

    include(APPPATH.'controllers/'.$RTR->fetch_directory().$RTR->fetch_class().'.php');

    // Set a mark point for benchmarking
    $BM->mark('loading_time:_base_classes_end');

/*
 * ------------------------------------------------------
 *  Security check
 * ------------------------------------------------------
 *
 *  None of the functions in the app controller or the
 *  loader class can be called via the URI, nor can
 *  controller functions that begin with an underscore
 */
    $class  = $RTR->fetch_class();
    $method = $RTR->fetch_method();

    if ( ! class_exists($class)
        OR strncmp($method, '_', 1) == 0
        OR in_array(strtolower($method), array_map('strtolower', get_class_methods('CI_Controller')))
        )
    {
        if ( ! empty($RTR->routes['404_override']))
        {
            $x = explode('/', $RTR->routes['404_override']);
            $class = $x[0];
            $method = (isset($x[1]) ? $x[1] : 'index');
            if ( ! class_exists($class))
            {
                if ( ! file_exists(APPPATH.'controllers/'.$class.'.php'))
                {
                    show_404("{$class}/{$method}");
                }

                include_once(APPPATH.'controllers/'.$class.'.php');
            }
        }
        else
        {
            show_404("{$class}/{$method}");
        }
    }

/*
 * ------------------------------------------------------
 *  Is there a "pre_controller" hook?
 * ------------------------------------------------------
 */
    $EXT->_call_hook('pre_controller');

/*
 * ------------------------------------------------------
 *  Instantiate the requested controller
 * ------------------------------------------------------
 */
    // Mark a start point so we can benchmark the controller
    $BM->mark('controller_execution_time_( '.$class.' / '.$method.' )_start');

    $CI = new $class();

    $GLOBALS['CI'] = $CI;
/*
 * ------------------------------------------------------
 *  Is there a "post_controller_constructor" hook?
 * ------------------------------------------------------
 */
    $EXT->_call_hook('post_controller_constructor');

// [/snip CI bootstrapper]
